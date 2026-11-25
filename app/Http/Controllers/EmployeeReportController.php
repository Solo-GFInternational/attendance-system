<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\EmployeeAttendanceReport;
use App\Services\EmployeeReportWorkbook;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EmployeeReportController extends Controller
{
    private const MAX_DAYS = 366;

    public function __construct(private EmployeeAttendanceReport $reports)
    {
    }

    public function index(Request $request)
    {
        // Inactive employees are included: reports are often needed after someone leaves.
        $employees = Employee::with('site:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'employee_code', 'status', 'site_id']);

        $report = null;
        $validator = null;

        if ($request->has('employee_id')) {
            $validator = $this->validator($request);

            if ($validator->passes()) {
                $report = $this->report($request);
            }
        }

        $today = Carbon::today();

        return view('reports.employee', [
            'employees' => $employees,
            'report' => $report,
            'problems' => $validator?->errors()->all() ?? [],
            'values' => [
                'employee_id' => $this->text($request, 'employee_id'),
                'from' => $this->text($request, 'from', $today->copy()->startOfMonth()->format('Y-m-d')),
                'to' => $this->text($request, 'to', $today->format('Y-m-d')),
                'hide_empty' => $request->boolean('hide_empty'),
            ],
            'ranges' => [
                'This week' => [$today->copy()->startOfWeek(Carbon::MONDAY), $today],
                'Last week' => [$today->copy()->subWeek()->startOfWeek(Carbon::MONDAY), $today->copy()->subWeek()->endOfWeek(Carbon::SUNDAY)],
                'This month' => [$today->copy()->startOfMonth(), $today],
                'Last month' => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
                'This year' => [$today->copy()->startOfYear(), $today],
            ],
        ]);
    }

    public function pdf(Request $request)
    {
        if ($this->validator($request)->fails()) {
            return $this->backToForm($request);
        }

        $report = $this->report($request);

        // A full year of rows needs more than PHP's default 128 MB in dompdf.
        ini_set('memory_limit', '512M');
        set_time_limit(120);

        return Pdf::loadView('reports.pdf.employee', ['report' => $report, 'generatedAt' => now()])
            ->setPaper('a4', 'landscape')
            ->download($this->filename($report, 'pdf'));
    }

    public function excel(Request $request, EmployeeReportWorkbook $workbook)
    {
        if ($this->validator($request)->fails()) {
            return $this->backToForm($request);
        }

        $report = $this->report($request);
        $book = $workbook->build($report);

        return response()->streamDownload(
            fn () => IOFactory::createWriter($book, 'Xlsx')->save('php://output'),
            $this->filename($report, 'xlsx'),
            [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Cache-Control' => 'no-store',
            ]
        );
    }

    private function validator(Request $request)
    {
        return Validator::make($request->query(), [
            'employee_id' => ['bail', 'required', 'integer', 'max:2147483647', 'exists:employees,id'],
            'from' => ['bail', 'required', 'date_format:Y-m-d', 'after_or_equal:2000-01-01'],
            'to' => [
                'bail', 'required', 'date_format:Y-m-d', 'after_or_equal:from', 'before:2100-01-01',
                function ($attribute, $value, $fail) use ($request) {
                    $from = $request->query('from');

                    if (is_string($from) && strtotime($from) && Carbon::parse($from)->diffInDays(Carbon::parse($value)) >= self::MAX_DAYS) {
                        $fail('Choose a period of one year or less.');
                    }
                },
            ],
        ], [
            'employee_id.*' => 'Choose an employee from the list.',
            'from.*' => 'Enter a valid From date.',
            'to.after_or_equal' => 'The To date must be the same as or after the From date.',
            'to.required' => 'Enter a valid To date.',
            'to.date_format' => 'Enter a valid To date.',
            'to.before' => 'Enter a valid To date.',
        ]);
    }

    private function report(Request $request): array
    {
        return $this->reports->build(
            Employee::with('site')->findOrFail($request->query('employee_id')),
            Carbon::parse($request->query('from'))->startOfDay(),
            Carbon::parse($request->query('to'))->startOfDay(),
            ! $request->boolean('hide_empty'),
        );
    }

    private function backToForm(Request $request)
    {
        return redirect()->route('reports.employee', [
            'employee_id' => $this->text($request, 'employee_id'),
            'from' => $this->text($request, 'from'),
            'to' => $this->text($request, 'to'),
        ]);
    }

    private function filename(array $report, string $extension): string
    {
        $name = Str::slug($report['employee']->employee_code . '-' . $report['employee']->name) ?: 'employee';

        return "attendance-{$name}-{$report['from']->format('Y-m-d')}-to-{$report['to']->format('Y-m-d')}.{$extension}";
    }

    private function text(Request $request, string $key, string $default = ''): string
    {
        $value = $request->query($key, $default);

        return is_string($value) ? $value : $default;
    }
}
