<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Site;
use App\Services\EmployeeImportTemplate;
use App\Services\FaceMatcher;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeController extends Controller
{
   public function index(Request $request)
{
    $query = Employee::query();

    // Search
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('employee_code', 'like', "%{$search}%")
              ->orWhere('name', 'like', "%{$search}%")
              ->orWhere('department', 'like', "%{$search}%")
              ->orWhere('designation', 'like', "%{$search}%");
        });
    }

    // Site filter
    if ($request->filled('site_id')) {
        $query->where('site_id', $request->site_id);
    }

    $employees = $query
        ->with('site')
        ->orderBy('name')
        ->get();

    $sites = Site::where('active', true)
        ->orderBy('name')
        ->get();

    return view('employees.index', compact(
        'employees',
        'sites'
    ));
}

    public function create()
{
    $sites = Site::where('active', true)
        ->orderBy('name')
        ->get();

    return view('employees.create', compact('sites'));
}

    public function store(Request $request)
{
    $validated = $request->validate([
        'employee_code' => 'required|string|max:50|unique:employees,employee_code',
        'name' => 'required|string|max:255',
        'department' => 'nullable|string|max:255',
        'designation' => 'nullable|string|max:255',
        'site_id' => 'required|exists:sites,id',
        'status' => 'required|in:Active,Inactive',
    ]);

    Employee::create($validated);

    return redirect()
        ->route('employees.index')
        ->with('success', 'Employee added successfully.');
}

    public function edit(Employee $employee)
{
    $sites = Site::where('active', true)
        ->orderBy('name')
        ->get();

    return view('employees.edit', compact('employee', 'sites'));
}

    public function update(Request $request, Employee $employee)
{
    $validated = $request->validate([
        'employee_code' => 'required|string|max:50|unique:employees,employee_code,' . $employee->id,
        'name' => 'required|string|max:255',
        'department' => 'nullable|string|max:255',
        'designation' => 'nullable|string|max:255',
        'site_id' => 'required|exists:sites,id',
        'status' => 'required|in:Active,Inactive',
    ]);

    $employee->update($validated);

    return redirect()
        ->route('employees.index')
        ->with('success', 'Employee updated successfully.');
}

    public function registerFace(Request $request, Employee $employee)
{
    $request->validate([
        'face_descriptor' => [
            'required',
            'string',
            'max:10000',
        ],
    ]);

    $descriptor = json_decode($request->face_descriptor, true);

    if (!is_array($descriptor) || count($descriptor) !== 128) {
        return response()->json([
            'success' => false,
            'message' => 'Invalid face descriptor. Please capture the face again.',
        ], 422);
    }

    foreach ($descriptor as $value) {
        if (!is_numeric($value) || !is_finite((float) $value)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid face data detected.',
            ], 422);
        }
    }

    $descriptor = array_map('floatval', array_values($descriptor));

    // Refuse a face that already belongs to a different employee; a
    // duplicate is the main way the kiosk ends up recognising the wrong person.
    $duplicate = app(FaceMatcher::class)->findDuplicate($descriptor, $employee->id);

    if ($duplicate) {
        return response()->json([
            'success' => false,
            'message' => 'This face looks the same as the face registered for ' .
                $duplicate->name . ' (' . $duplicate->employee_code . '). ' .
                'If that registration is wrong, reset it first, then register again.',
        ], 409);
    }

    $employee->face_descriptor = json_encode($descriptor);

    $employee->face_registered_at = now();

    $employee->save();

    return response()->json([
        'success' => true,
        'message' => 'Face registered successfully.',
        'employee' => [
            'id' => $employee->id,
            'name' => $employee->name,
            'employee_code' => $employee->employee_code,
        ],
    ]);
}

    public function toggleStatus(Employee $employee)
    {
        $employee->status =
            $employee->status === 'Active'
                ? 'Inactive'
                : 'Active';

        $employee->save();

        return redirect()
            ->route('employees.index')
            ->with(
                'success',
                'Employee status changed to ' . $employee->status . '.'
            );
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
/*
|--------------------------------------------------------------------------
| SHOW CSV IMPORT PAGE
|--------------------------------------------------------------------------
*/

public function showImport()
{
    $sites = Site::where('active', true)
        ->orderBy('name')
        ->get();

    return view('employees.import', compact('sites'));
}


/*
|--------------------------------------------------------------------------
| DOWNLOAD IMPORT TEMPLATE
|--------------------------------------------------------------------------
*/

public function importTemplate(EmployeeImportTemplate $template)
{
    $book = $template->build(
        Site::where('active', true)->orderBy('name')->get()
    );

    return response()->streamDownload(
        fn () => IOFactory::createWriter($book, 'Xlsx')->save('php://output'),
        'Employee Import Template.xlsx',
        [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'no-store',
        ]
    );
}


/*
|--------------------------------------------------------------------------
| IMPORT EMPLOYEES FROM CSV
|--------------------------------------------------------------------------
*/

public function import(Request $request)
{
    $request->validate([
        'csv_file' => 'required|file|mimes:csv,txt|max:10240',
    ]);

    $file = $request->file('csv_file');

    $handle = fopen($file->getRealPath(), 'r');

    if (!$handle) {
        return back()->with(
            'error',
            'Unable to open the CSV file.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | READ HEADER ROW
    |--------------------------------------------------------------------------
    */

    // Skip a UTF-8 BOM first, otherwise a quoted first heading keeps its quotes.
    if (fread($handle, 3) !== "\xEF\xBB\xBF") {
        rewind($handle);
    }

    $headers = fgetcsv($handle);

    if (!$headers) {
        fclose($handle);

        return back()->with(
            'error',
            'The CSV file is empty.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAN HEADERS
    |--------------------------------------------------------------------------
    */

    $headers = array_map(function ($header) {

        // Remove UTF-8 BOM
        $header = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $header
        );

        // Replace non-breaking spaces
        $header = str_replace(
            "\xC2\xA0",
            ' ',
            $header
        );

        return trim($header);

    }, $headers);


    /*
    |--------------------------------------------------------------------------
    | REQUIRED COLUMNS
    |--------------------------------------------------------------------------
    */

    // Same headings as the Add Employee form. The older payroll headings
    // are still accepted. Capitals and extra spaces are ignored.
    $columns = [
        'Employee ID' => 'Employee Code',
        'Employee Name' => null,
        'Department' => 'Cost Centre Description Code',
        'Designation' => 'Position Description',
        'Site' => null,
    ];

    $headerKey = fn (string $value) => mb_strtolower(
        preg_replace('/\s+/', ' ', $value)
    );

    $headerKeys = array_map($headerKey, $headers);

    $positions = [];

    foreach ($columns as $heading => $olderHeading) {

        $position = array_search($headerKey($heading), $headerKeys, true);

        if ($position === false && $olderHeading) {
            $position = array_search($headerKey($olderHeading), $headerKeys, true);
        }

        if ($position === false) {

            fclose($handle);

            return back()->with(
                'error',
                'Missing required CSV column: ' . $heading
            );
        }

        $positions[$heading] = $position;
    }

    $employeeCodeIndex = $positions['Employee ID'];
    $employeeNameIndex = $positions['Employee Name'];
    $departmentIndex = $positions['Department'];
    $designationIndex = $positions['Designation'];
    $siteIndex = $positions['Site'];

    // Site column: matched to an active site's name or code, ignoring
    // capitals and extra spaces. A name wins if it equals another site's code.
    $siteKey = fn (string $value) => mb_strtolower(
        trim(preg_replace('/\s+/', ' ', $value))
    );

    $siteIds = [];

    foreach (Site::where('active', true)->get() as $site) {
        $siteIds['code'][$siteKey($site->code)] = $site->id;
        $siteIds['name'][$siteKey($site->name)] = $site->id;
    }


    /*
    |--------------------------------------------------------------------------
    | IMPORT COUNTERS
    |--------------------------------------------------------------------------
    */

    $added = 0;
    $updated = 0;
    $skipped = 0;

    $errors = [];

    $rowNumber = 1;


    /*
    |--------------------------------------------------------------------------
    | PROCESS CSV ROWS
    |--------------------------------------------------------------------------
    */

    DB::beginTransaction();

    try {

        while (($row = fgetcsv($handle)) !== false) {

            $rowNumber++;

            /*
            |--------------------------------------------------------------------------
            | SKIP COMPLETELY EMPTY ROWS
            |--------------------------------------------------------------------------
            */

            if (
                count($row) === 1 &&
                trim($row[0]) === ''
            ) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | GET AND CLEAN DATA
            |--------------------------------------------------------------------------
            */

            $employeeCode = isset($row[$employeeCodeIndex])
                ? trim(
                    str_replace(
                        "\xC2\xA0",
                        ' ',
                        $row[$employeeCodeIndex]
                    )
                )
                : '';

            $employeeName = isset($row[$employeeNameIndex])
                ? trim(
                    str_replace(
                        "\xC2\xA0",
                        ' ',
                        $row[$employeeNameIndex]
                    )
                )
                : '';

            $department = isset($row[$departmentIndex])
                ? trim(
                    str_replace(
                        "\xC2\xA0",
                        ' ',
                        $row[$departmentIndex]
                    )
                )
                : '';

            $designation = isset($row[$designationIndex])
                ? trim(
                    str_replace(
                        "\xC2\xA0",
                        ' ',
                        $row[$designationIndex]
                    )
                )
                : '';

            $siteText = isset($row[$siteIndex])
                ? trim(
                    str_replace(
                        "\xC2\xA0",
                        ' ',
                        $row[$siteIndex]
                    )
                )
                : '';


            /*
            |--------------------------------------------------------------------------
            | VALIDATE ROW
            |--------------------------------------------------------------------------
            */

            if ($employeeCode === '') {

                $skipped++;

                $errors[] =
                    "Row {$rowNumber}: Employee ID is missing.";

                continue;
            }

            if ($employeeName === '') {

                $skipped++;

                $errors[] =
                    "Row {$rowNumber}: Employee Name is missing.";

                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | CHECK EXISTING EMPLOYEE
            |--------------------------------------------------------------------------
            */

            $employee = Employee::where(
                'employee_code',
                $employeeCode
            )->first();


            /*
            |--------------------------------------------------------------------------
            | MATCH SITE
            |--------------------------------------------------------------------------
            | Without a site an employee appears on no tablet, so a new
            | employee must have one. A blank site on an existing employee
            | keeps the site they already have.
            */

            if ($siteText === '') {

                $siteId = $employee?->site_id;

                if (! $siteId) {

                    $skipped++;

                    $errors[] =
                        "Row {$rowNumber}: {$employeeName} ({$employeeCode}) - Site is missing.";

                    continue;
                }

            } else {

                $key = $siteKey($siteText);

                $siteId = $siteIds['name'][$key]
                    ?? $siteIds['code'][$key]
                    ?? null;

                if (! $siteId) {

                    $skipped++;

                    $errors[] =
                        "Row {$rowNumber}: {$employeeName} ({$employeeCode}) - Site \"{$siteText}\" does not match an active site.";

                    continue;
                }
            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE EXISTING EMPLOYEE
            |--------------------------------------------------------------------------
            */

            if ($employee) {

                $employee->update([
                    'name' => $employeeName,
                    'department' => $department ?: null,
                    'designation' => $designation ?: null,
                    'site_id' => $siteId,
                ]);

                $updated++;

            } else {

                /*
                |--------------------------------------------------------------------------
                | CREATE NEW EMPLOYEE
                |--------------------------------------------------------------------------
                */

                Employee::create([
                    'employee_code' => $employeeCode,
                    'name' => $employeeName,
                    'department' => $department ?: null,
                    'designation' => $designation ?: null,
                    'site_id' => $siteId,
                    'status' => 'Active',
                ]);

                $added++;
            }

        }

        DB::commit();

    } catch (\Throwable $e) {

        DB::rollBack();

        fclose($handle);

        return back()->with(
            'error',
            'Import failed: ' . $e->getMessage()
        );
    }

    fclose($handle);


    /*
    |--------------------------------------------------------------------------
    | RETURN IMPORT RESULTS
    |--------------------------------------------------------------------------
    */

    return redirect()
        ->route('employees.index')
        ->with(
            'success',
            "Import completed. Added: {$added}, Updated: {$updated}, Skipped: {$skipped}."
        )
        ->with('import_errors', $errors);
}


public function removeFace(Employee $employee)
{
    $employee->face_descriptor = null;
    $employee->save();

    return redirect()
        ->route('employees.index')
        ->with('success', 'Face registration removed successfully.');
}

}