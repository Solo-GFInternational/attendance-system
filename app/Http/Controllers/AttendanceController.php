<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceOuting;
use App\Models\Employee;
use App\Models\Visitor;
use App\Models\Site;
use App\Exports\AttendanceHistoryExport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\FaceMatcher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | PUBLIC ATTENDANCE
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $sites = Site::where('active', true)
            ->orderBy('name')
            ->get();

        $selectedSite = session('attendance_site_id');

        $employees = Employee::where('status', 'Active')
            ->when($selectedSite, function ($query) use ($selectedSite) {
                $query->where('site_id', $selectedSite);
            })
            ->orderBy('name')
            ->get();

        return view('attendance.index', compact(
            'employees',
            'sites',
            'selectedSite'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | ADMIN DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function adminDashboard(Request $request)
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | SITE FILTER
        |--------------------------------------------------------------------------
        */

        $selectedSiteId = $request->input('site_id');

        $sites = Site::where('active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE EMPLOYEES
        |--------------------------------------------------------------------------
        */

        $employees = Employee::where('status', 'Active')
            ->when($selectedSiteId, function ($query) use ($selectedSiteId) {
                $query->where('site_id', $selectedSiteId);
            })
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | TODAY'S ATTENDANCE
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = Attendance::with([
            'employee',
            'outings'
        ])
            ->whereDate('attendance_date', $today)
            ->whereIn('employee_id', $employees->pluck('id'))
            ->get()
            ->keyBy('employee_id');

        /*
        |--------------------------------------------------------------------------
        | BUILD ATTENDANCE DATA
        |--------------------------------------------------------------------------
        */

        $attendance = $employees->map(function ($employee) use (
            $attendanceRecords,
            $today
        ) {
            $record = $attendanceRecords->get($employee->id);

            $outings = $record
                ? $record->outings
                    ->sortBy('out_time')
                    ->values()
                : collect();

            $activeOuting = $outings
                ->whereNull('return_time')
                ->sortByDesc('id')
                ->first();

            $totalMinutesOutside = $outings
                ->whereNotNull('duration_minutes')
                ->sum('duration_minutes');

            return (object) [
                'employee' => $employee,

                'attendance_date' =>
                    $record?->attendance_date ?? $today,

                'check_in' =>
                    $record?->check_in,

                'check_out' =>
                    $record?->check_out,

                'sign_in_photo' =>
                    $record?->sign_in_photo,

                'status' =>
                    $record?->status ?? 'Absent',

                'remarks' =>
                    $record?->remarks,

                'outings' =>
                    $outings,

                'active_outing' =>
                    $activeOuting,

                'currently_out' =>
                    $activeOuting !== null,

                'total_minutes_outside' =>
                    $totalMinutesOutside,
            ];
        });

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalStaff = $employees->count();

        $present = $attendance
            ->where('status', 'Present')
            ->count();

        $late = $attendance
            ->where('status', 'Late')
            ->count();

        $absent = $attendance
            ->where('status', 'Absent')
            ->count();

        $currentlyOut = $attendance
            ->where('currently_out', true)
            ->count();

        $checkedOut = $attendance
            ->filter(function ($record) {
                return $record->check_out !== null;
            })
            ->count();

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('attendance.admin', compact(
            'attendance',
            'totalStaff',
            'present',
            'late',
            'absent',
            'currentlyOut',
            'checkedOut',
            'sites',
            'selectedSiteId'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | OUTING HISTORY
    |--------------------------------------------------------------------------
    */

    public function outingHistory(Request $request)
    {
        $date = $request->input(
            'date',
            Carbon::today()->format('Y-m-d')
        );

        $query = AttendanceOuting::with([
            'employee',
            'attendance'
        ])
            ->whereHas('attendance', function ($query) use ($date) {
                $query->whereDate(
                    'attendance_date',
                    $date
                );
            });

        /*
        | EMPLOYEE FILTER
        */

        if ($request->filled('employee_id')) {
            $query->where(
                'employee_id',
                $request->employee_id
            );
        }

        /*
        | DEPARTMENT FILTER
        */

        if ($request->filled('department')) {
            $query->whereHas(
                'employee',
                function ($query) use ($request) {
                    $query->where(
                        'department',
                        $request->department
                    );
                }
            );
        }

        /*
        | SEARCH
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'employee',
                function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'employee_code',
                            'like',
                            '%' . $search . '%'
                        );
                    });
                }
            );
        }

        /*
        | GET OUTINGS
        */

        $outings = $query
            ->orderBy('out_time')
            ->get();

        /*
        | EMPLOYEES
        */

        $employees = Employee::where(
            'status',
            'Active'
        )
            ->orderBy('name')
            ->get();

        /*
        | DEPARTMENTS
        */

        $departments = Employee::where(
            'status',
            'Active'
        )
            ->whereNotNull('department')
            ->where(
                'department',
                '!=',
                ''
            )
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        /*
        | SUMMARY
        */

        $totalOutings = $outings->count();

        $completedOutings = $outings
            ->whereNotNull('return_time');

        $currentlyOut = $outings
            ->whereNull('return_time')
            ->count();

        $totalMinutesOutside = $completedOutings
            ->sum('duration_minutes');

        $totalHours = intdiv(
            $totalMinutesOutside,
            60
        );

        $remainingMinutes = $totalMinutesOutside % 60;

        return view(
            'attendance.outings',
            compact(
                'outings',
                'employees',
                'departments',
                'date',
                'totalOutings',
                'currentlyOut',
                'totalMinutesOutside',
                'totalHours',
                'remainingMinutes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE STATUS
    |--------------------------------------------------------------------------
    */

    public function status(Employee $employee)
    {
        $attendance = Attendance::with('outings')
            ->where(
                'employee_id',
                $employee->id
            )
            ->whereDate(
                'attendance_date',
                Carbon::today()
            )
            ->first();

        /*
        | NOT SIGNED IN
        */

        if (!$attendance) {

            return response()->json([
                'status' => 'not_signed_in',
                'face_registered' => !empty(
                    $employee->face_descriptor
                ),
            ]);
        }

        /*
        | SIGNED IN BUT NOT SIGNED OUT
        */

        if (!$attendance->check_out) {

            $activeOuting = $attendance
                ->outings()
                ->whereNull('return_time')
                ->latest('id')
                ->first();

            return response()->json([

                'status' => 'signed_in',

                'check_in' =>
                    Carbon::parse(
                        $attendance->check_in
                    )->format('h:i A'),

                'currently_out' =>
                    $activeOuting !== null,

                'out_time' =>
                    $activeOuting
                        ? Carbon::parse(
                            $activeOuting->out_time
                        )->format('h:i A')
                        : null,

                'destination' => null,

                'outings' =>
                    $attendance->outings
                        ->sortBy('id')
                        ->values()
                        ->map(function ($outing) {

                            return [

                                'destination' => null,

                                'out_time' =>
                                    Carbon::parse(
                                        $outing->out_time
                                    )->format('h:i A'),

                                'return_time' =>
                                    $outing->return_time
                                        ? Carbon::parse(
                                            $outing->return_time
                                        )->format('h:i A')
                                        : null,

                                'duration_minutes' =>
                                    $outing->duration_minutes,

                                'reason' =>
                                    $outing->reason,
                            ];
                        }),
            ]);
        }

        /*
        | COMPLETED / SIGNED OUT
        */

        return response()->json([

            'status' => 'completed',

            'check_in' =>
                Carbon::parse(
                    $attendance->check_in
                )->format('h:i A'),

            'check_out' =>
                Carbon::parse(
                    $attendance->check_out
                )->format('h:i A'),

            'outings' =>
                $attendance->outings
                    ->sortBy('id')
                    ->values()
                    ->map(function ($outing) {

                        return [

                            'destination' => null,

                            'out_time' =>
                                Carbon::parse(
                                    $outing->out_time
                                )->format('h:i A'),

                            'return_time' =>
                                $outing->return_time
                                    ? Carbon::parse(
                                        $outing->return_time
                                    )->format('h:i A')
                                    : null,

                            'duration_minutes' =>
                                $outing->duration_minutes,

                            'reason' =>
                                $outing->reason,
                        ];
                    }),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SIGN IN
    |--------------------------------------------------------------------------
    */

    /*
|--------------------------------------------------------------------------
| SIGN IN
|--------------------------------------------------------------------------
*/

public function signIn(Request $request)
{
    if (! $this->consumeFaceVerification($request, $request->input('employee_id'))) {
        return $this->faceNotVerifiedResponse();
    }

    $request->validate([
        'employee_id' =>
            'required|exists:employees,id',
    ]);

    $employee = Employee::where(
        'id',
        $request->employee_id
    )
        ->where(
            'status',
            'Active'
        )
        ->firstOrFail();

    $today = Carbon::today();

    $existingAttendance = Attendance::where(
        'employee_id',
        $employee->id
    )
        ->whereDate(
            'attendance_date',
            $today
        )
        ->first();

    if ($existingAttendance) {

        return redirect()
            ->route('attendance.employee')
            ->with(
                'error',
                $employee->name .
                ' has already signed in today.'
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DETERMINE STATUS
    |--------------------------------------------------------------------------
    */

    $checkInTime = now();

    $officialStartTime = Carbon::createFromTime(
        8,
        5,
        0
    );

    $status =
        $checkInTime->lte($officialStartTime)
            ? 'Present'
            : 'Late';

    /*
    |--------------------------------------------------------------------------
    | CREATE ATTENDANCE
    |--------------------------------------------------------------------------
    |
    | No second camera/photo is required.
    | Face recognition has already identified the employee.
    |
    */

    Attendance::create([
        'employee_id' =>
            $employee->id,

        'attendance_date' =>
            $today,

        'status' =>
            $status,

        'check_in' =>
            $checkInTime->format('H:i:s'),

        'sign_in_photo' =>
            null,
    ]);

    return redirect()
        ->route('attendance.employee')
        ->with(
            'success',
            $employee->name .
            ' signed in successfully at ' .
            now()->format('h:i A') .
            '.'
        );
}


    /*
    |--------------------------------------------------------------------------
    | SIGN OUT
    |--------------------------------------------------------------------------
    */

    public function signOut(Request $request)
    {
        if (! $this->consumeFaceVerification($request, $request->input('employee_id'))) {
            return $this->faceNotVerifiedResponse();
        }

        $request->validate([
            'employee_id' =>
                'required|exists:employees,id',
        ]);

        $employee = Employee::where(
            'id',
            $request->employee_id
        )
            ->where(
                'status',
                'Active'
            )
            ->firstOrFail();

        $attendance = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'attendance_date',
                Carbon::today()
            )
            ->first();

        if (!$attendance) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has not signed in today.'
                );
        }

        if ($attendance->check_out) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has already signed out today.'
                );
        }

        /*
        | PREVENT SIGNING OUT WHILE STILL OUT
        */

        $activeOuting = AttendanceOuting::where(
            'attendance_id',
            $attendance->id
        )
            ->whereNull('return_time')
            ->latest('id')
            ->first();

        if ($activeOuting) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' is currently out. Please mark Returning first.'
                );
        }

        $attendance->update([
            'check_out' =>
                now()->format('H:i:s'),
        ]);

        return redirect()
            ->route('attendance.employee')
            ->with(
                'success',
                $employee->name .
                ' signed out successfully at ' .
                now()->format('h:i A') .
                '.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | GOING OUT
    |--------------------------------------------------------------------------
    */

    public function goingOut(Request $request)
    {
        if (! $this->consumeFaceVerification($request, $request->input('employee_id'))) {
            return $this->faceNotVerifiedResponse();
        }

        /*
        | Reason is compulsory.
        | Destination is no longer required because
        | the kiosk only asks for a reason.
        */

        $request->validate([
            'employee_id' =>
                'required|exists:employees,id',

            'reason' =>
                'required|string|max:255',
        ]);

        $employee = Employee::where(
            'id',
            $request->employee_id
        )
            ->where(
                'status',
                'Active'
            )
            ->firstOrFail();

        $today = Carbon::today();

        $attendance = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        /*
        | MUST BE SIGNED IN
        */

        if (!$attendance) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has not signed in today.'
                );
        }

        /*
        | MUST NOT BE SIGNED OUT
        */

        if ($attendance->check_out) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has already signed out today.'
                );
        }

        /*
        | CHECK FOR EXISTING ACTIVE OUTING
        */

        $activeOuting = AttendanceOuting::where(
            'attendance_id',
            $attendance->id
        )
            ->whereNull('return_time')
            ->latest('id')
            ->first();

        if ($activeOuting) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' is already marked as out.'
                );
        }

        /*
        | CREATE OUTING
        */

        AttendanceOuting::create([

            'attendance_id' =>
                $attendance->id,

            'employee_id' =>
                $employee->id,

            'out_time' =>
                now()->format('H:i:s'),

            'reason' =>
                $request->reason,
        ]);

        return redirect()
            ->route('attendance.employee')
            ->with(
                'success',
                $employee->name .
                ' has been marked as Going Out at ' .
                now()->format('h:i A') .
                '.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | RETURNING
    |--------------------------------------------------------------------------
    */

    public function returning(Request $request)
    {
        if (! $this->consumeFaceVerification($request, $request->input('employee_id'))) {
            return $this->faceNotVerifiedResponse();
        }

        $request->validate([
            'employee_id' =>
                'required|exists:employees,id',
        ]);

        $employee = Employee::where(
            'id',
            $request->employee_id
        )
            ->where(
                'status',
                'Active'
            )
            ->firstOrFail();

        $today = Carbon::today();

        $attendance = Attendance::where(
            'employee_id',
            $employee->id
        )
            ->whereDate(
                'attendance_date',
                $today
            )
            ->first();

        /*
        | NO ATTENDANCE
        */

        if (!$attendance) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has no attendance record today.'
                );
        }

        /*
        | CANNOT RETURN AFTER SIGNING OUT
        */

        if ($attendance->check_out) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' has already signed out today.'
                );
        }

        /*
        | FIND CURRENT ACTIVE OUTING
        */

        $outing = AttendanceOuting::where(
            'attendance_id',
            $attendance->id
        )
            ->where(
                'employee_id',
                $employee->id
            )
            ->whereNull('return_time')
            ->latest('id')
            ->first();

        /*
        | NOT CURRENTLY OUT
        */

        if (!$outing) {

            return redirect()
                ->route('attendance.employee')
                ->with(
                    'error',
                    $employee->name .
                    ' is not currently marked as out.'
                );
        }

        /*
        | RETURN TIME
        */

        $returnTime = now();

        /*
        | OUT TIME
        |
        | The database stores out_time as H:i:s,
        | so attach today's date before calculating.
        */

        $outTime = Carbon::today()->setTimeFromTimeString(
            $outing->out_time
        );

        /*
        | CALCULATE DURATION
        */

        $duration = (int) round($outTime->diffInMinutes(
            $returnTime
        ));

        /*
        | UPDATE OUTING
        */

        $outing->update([

            'return_time' =>
                $returnTime->format('H:i:s'),

            'duration_minutes' =>
                $duration,
        ]);

        return redirect()
            ->route('attendance.employee')
            ->with(
                'success',
                $employee->name .
                ' has returned at ' .
                $returnTime->format('h:i A') .
                '. Time out: ' .
                $duration .
                ' minutes.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EMERGENCY ROLL CALL
    |--------------------------------------------------------------------------
    */

    public function emergency()
    {
        // no-store: every open of the page fetches fresh data from the server.
        // The service worker still keeps its own copy for offline use.
        return response()
            ->view('attendance.emergency', $this->emergencyData())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }


    /*
    |--------------------------------------------------------------------------
    | EMERGENCY ROLL COUNT PDF
    |--------------------------------------------------------------------------
    |
    | Employees in the office and visitors on site, with a tick box per
    | person for the warden's roll count.
    |
    */

    public function emergencyPdf()
    {
        $data = $this->emergencyData();

        $pdf = Pdf::loadView('attendance.pdf.emergency', $data)
            ->setOption('enable_font_subsetting', true)
            ->setPaper('a4', 'portrait');

        $filename = 'emergency-roll-count-' .
            $data['generatedAt']->format('Y-m-d-His') .
            '.pdf';

        return $pdf->download($filename);
    }


    private function emergencyData(): array
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | SELECTED SITE
        |--------------------------------------------------------------------------
        |
        | The tablet site is stored in the session by:
        |
        | /attendance/DG
        | /attendance/FG
        | /attendance/FM
        |
        | If no site is selected, the emergency roll call shows all sites.
        |
        */

        $selectedSiteId = session('attendance_site_id');

        $selectedSite = null;

        if ($selectedSiteId) {
            $selectedSite = Site::find($selectedSiteId);
        }


        /*
        |--------------------------------------------------------------------------
        | EMPLOYEES
        |--------------------------------------------------------------------------
        */

        $employees = Employee::where(
            'status',
            'Active'
        )
            ->when(
                $selectedSiteId,
                function ($query) use ($selectedSiteId) {

                    $query->where(
                        'site_id',
                        $selectedSiteId
                    );
                }
            )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | TODAY'S ATTENDANCE
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = Attendance::with([
            'employee',
            'outings'
        ])
            ->whereDate(
                'attendance_date',
                $today
            )
            ->whereIn(
                'employee_id',
                $employees->pluck('id')
            )
            ->get()
            ->keyBy('employee_id');


        /*
        |--------------------------------------------------------------------------
        | BUILD ROLL CALL
        |--------------------------------------------------------------------------
        */

        $rollCall = $employees->map(
            function ($employee) use (
                $attendanceRecords
            ) {

                $record =
                    $attendanceRecords->get(
                        $employee->id
                    );


                /*
                | NOT SIGNED IN
                */

                if (!$record) {

                    return (object) [

                        'employee' =>
                            $employee,

                        'status' =>
                            'not_signed_in',

                        'check_in' =>
                            null,

                        'check_out' =>
                            null,

                        'active_outing' =>
                            null,
                    ];
                }


                /*
                | ACTIVE OUTING
                */

                $activeOuting =
                    $record->outings
                        ->whereNull('return_time')
                        ->sortByDesc('id')
                        ->first();


                /*
                | DETERMINE STATUS
                */

                if ($record->check_out) {

                    $status =
                        'checked_out';

                } elseif ($activeOuting) {

                    $status =
                        'out';

                } else {

                    $status =
                        'in_office';
                }


                return (object) [

                    'employee' =>
                        $employee,

                    'status' =>
                        $status,

                    'check_in' =>
                        $record->check_in,

                    'check_out' =>
                        $record->check_out,

                    'active_outing' =>
                        $activeOuting,
                ];
            }
        );


        /*
        |--------------------------------------------------------------------------
        | CURRENT VISITORS
        |--------------------------------------------------------------------------
        */

        $visitors = Visitor::whereNull('sign_out')
            ->when(
                $selectedSiteId,
                function ($query) use ($selectedSiteId) {

                    $query->where(
                        'site_id',
                        $selectedSiteId
                    );
                }
            )
            ->orderBy('sign_in')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $inOffice = $rollCall
            ->where(
                'status',
                'in_office'
            )
            ->count();


        $currentlyOut = $rollCall
            ->where(
                'status',
                'out'
            )
            ->count();


        $notSignedIn = $rollCall
            ->where(
                'status',
                'not_signed_in'
            )
            ->count();


        $checkedOut = $rollCall
            ->where(
                'status',
                'checked_out'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | PEOPLE CURRENTLY ON SITE
        |--------------------------------------------------------------------------
        */

        $visitorsOnSite =
            $visitors->count();


        $peopleOnSite =
            $inOffice +
            $currentlyOut +
            $visitorsOnSite;


        $inOfficeList = $rollCall
            ->where('status', 'in_office')
            ->values();

        $generatedAt = now();

        return compact(
            'rollCall',
            'inOffice',
            'inOfficeList',
            'currentlyOut',
            'notSignedIn',
            'checkedOut',
            'visitors',
            'visitorsOnSite',
            'peopleOnSite',
            'selectedSite',
            'generatedAt'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ATTENDANCE HISTORY
    |--------------------------------------------------------------------------
    */

    public function attendanceHistory(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | SELECTED DATE
        |--------------------------------------------------------------------------
        */

        $date = $request->input(
            'date',
            Carbon::today()->format('Y-m-d')
        );

        /*
        |--------------------------------------------------------------------------
        | SITE FILTER
        |--------------------------------------------------------------------------
        */

        $selectedSiteId = $request->input('site_id');

        $sites = Site::where('active', true)
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET ACTIVE EMPLOYEES
        |--------------------------------------------------------------------------
        */

        $employees = Employee::where(
            'status',
            'Active'
        )
            ->when(
                $selectedSiteId,
                function ($query) use ($selectedSiteId) {

                    $query->where(
                        'site_id',
                        $selectedSiteId
                    );
                }
            )
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | GET ATTENDANCE RECORDS FOR SELECTED DATE
        |--------------------------------------------------------------------------
        */

        $attendanceRecords = Attendance::with([
            'employee',
            'outings'
        ])
            ->whereDate(
                'attendance_date',
                $date
            )
            ->whereIn(
                'employee_id',
                $employees->pluck('id')
            )
            ->get()
            ->keyBy('employee_id');

        /*
        |--------------------------------------------------------------------------
        | BUILD HISTORY
        |--------------------------------------------------------------------------
        */

        $attendance = $employees->map(
            function ($employee) use (
                $attendanceRecords,
                $date
            ) {

                $record = $attendanceRecords->get(
                    $employee->id
                );

                /*
                | EMPLOYEE HAS ATTENDANCE RECORD
                */

                if ($record) {

                    return $record;
                }

                /*
                | EMPLOYEE DID NOT SIGN IN
                */

                return (object) [

                    'employee' =>
                        $employee,

                    'attendance_date' =>
                        Carbon::parse($date),

                    'check_in' =>
                        null,

                    'check_out' =>
                        null,

                    'status' =>
                        'Absent',

                    'sign_in_photo' =>
                        null,

                    'remarks' =>
                        null,

                    'outings' =>
                        collect(),
                ];
            }
        );

        /*
        |--------------------------------------------------------------------------
        | SEARCH EMPLOYEE
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = strtolower(
                $request->search
            );

            $attendance = $attendance
                ->filter(function ($record) use ($search) {

                    $name = strtolower(
                        $record->employee->name ?? ''
                    );

                    $employeeCode = strtolower(
                        $record->employee->employee_code ?? ''
                    );

                    return str_contains(
                        $name,
                        $search
                    )
                    ||
                    str_contains(
                        $employeeCode,
                        $search
                    );
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department')) {

            $attendance = $attendance
                ->filter(function ($record) use ($request) {

                    return ($record->employee->department ?? null)
                        === $request->department;
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $attendance = $attendance
                ->filter(function ($record) use ($request) {

                    return ($record->status ?? null)
                        === $request->status;
                })
                ->values();
        }

        /*
        |--------------------------------------------------------------------------
        | SORT BY CHECK-IN / ABSENT
        |--------------------------------------------------------------------------
        */

        $attendance = $attendance
            ->sortBy(function ($record) {

                return $record->check_in
                    ? Carbon::parse($record->check_in)->timestamp
                    : PHP_INT_MAX;
            })
            ->values();

        /*
        |--------------------------------------------------------------------------
        | DEPARTMENTS
        |--------------------------------------------------------------------------
        */

        $departments = Employee::where(
            'status',
            'Active'
        )
            ->when(
                $selectedSiteId,
                function ($query) use ($selectedSiteId) {

                    $query->where(
                        'site_id',
                        $selectedSiteId
                    );
                }
            )
            ->whereNotNull('department')
            ->where(
                'department',
                '!=',
                ''
            )
            ->distinct()
            ->orderBy('department')
            ->pluck('department');

        /*
        |--------------------------------------------------------------------------
        | SUMMARY
        |--------------------------------------------------------------------------
        */

        $totalRecords =
            $attendance->count();

        $present =
            $attendance
                ->where(
                    'status',
                    'Present'
                )
                ->count();

        $late =
            $attendance
                ->where(
                    'status',
                    'Late'
                )
                ->count();

        $absent =
            $attendance
                ->where(
                    'status',
                    'Absent'
                )
                ->count();

        $leave =
            $attendance
                ->where(
                    'status',
                    'Leave'
                )
                ->count();

        $checkedOut =
            $attendance
                ->whereNotNull(
                    'check_out'
                )
                ->count();

        $totalOutings =
            $attendance->sum(
                function ($record) {

                    return $record
                        ->outings
                        ->count();
                }
            );

        /*
        |--------------------------------------------------------------------------
        | RETURN HISTORY VIEW
        |--------------------------------------------------------------------------
        */

        return view(
            'attendance.history',
            compact(
                'attendance',
                'departments',
                'date',
                'totalRecords',
                'present',
                'late',
                'absent',
                'leave',
                'checkedOut',
                'totalOutings',
                'sites',
                'selectedSiteId'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD ATTENDANCE HISTORY - PDF
    |--------------------------------------------------------------------------
    */

    public function downloadAttendancePdf(Request $request)
    {
        $query = Attendance::with([
            'employee',
            'outings'
        ]);

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date')) {

            $query->whereDate(
                'attendance_date',
                $request->date
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SITE FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('site_id')) {

            $query->whereHas(
                'employee',
                function ($query) use ($request) {

                    $query->where(
                        'site_id',
                        $request->site_id
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | EMPLOYEE SEARCH
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'employee',
                function ($query) use ($search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'name',
                            'like',
                            '%' . $search . '%'
                        )
                        ->orWhere(
                            'employee_code',
                            'like',
                            '%' . $search . '%'
                        );
                    });
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DEPARTMENT FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('department')) {

            $query->whereHas(
                'employee',
                function ($query) use ($request) {

                    $query->where(
                        'department',
                        $request->department
                    );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | STATUS FILTER
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | GET ATTENDANCE RECORDS
        |--------------------------------------------------------------------------
        */

        $attendances = $query
            ->orderBy('attendance_date')
            ->orderBy('check_in')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | PDF VARIABLES
        |--------------------------------------------------------------------------
        */

        $generatedAt = now();

        $dateFrom = $request->input('date');

        $dateTo = $request->input('date');


        /*
        |--------------------------------------------------------------------------
        | GENERATE PDF
        |--------------------------------------------------------------------------
        */

        $pdf = Pdf::loadView(
            'attendance.pdf.history',
            [
                'attendances' => $attendances,
                'generatedAt' => $generatedAt,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]
        );


        $pdf->setPaper(
            'a4',
            'landscape'
        );


        /*
        |--------------------------------------------------------------------------
        | FILE NAME
        |--------------------------------------------------------------------------
        */

        $filename = 'attendance-history-' .
            ($dateFrom ?: now()->format('Y-m-d')) .
            '.pdf';


        return $pdf->download($filename);
    }


    /*
    |--------------------------------------------------------------------------
    | DOWNLOAD ATTENDANCE HISTORY - EXCEL
    |--------------------------------------------------------------------------
    */

    public function downloadAttendanceExcel(Request $request)
    {
        $date = $request->input('date');

        if ($date) {

            $filename = 'attendance-history-' .
                Carbon::parse($date)->format('Y-m-d') .
                '.xlsx';

        } else {

            $filename = 'attendance-history-' .
                Carbon::now()->format('Y-m-d-His') .
                '.xlsx';
        }

        return Excel::download(
            new AttendanceHistoryExport($request),
            $filename
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FACE RECOGNITION
    |--------------------------------------------------------------------------
    */

    public function recognizeFace(Request $request, FaceMatcher $matcher)
    {
        $validated = $request->validate(FaceMatcher::rules());

        $samples = FaceMatcher::samplesFromRequest($validated);

        $known = $matcher->knownFaces();

        if ($known->isEmpty()) {

            return response()->json([
                'matched' => false,
                'message' => 'No employees have registered faces.',
            ]);
        }

        $match = $matcher->identify($samples, $known);

        if (! $match) {

            $this->clearFaceVerification($request);

            return response()->json([
                'matched' => false,
                'message' => 'Face not recognised. Look straight at the camera and hold still.',
            ]);
        }

        // Faces are compared with every site (so a visitor from another site is
        // not mistaken for someone here), but other sites' people are never shown.
        $siteId = $request->session()->get('attendance_site_id');

        if ($siteId && (int) $match['employee']->site_id !== (int) $siteId) {

            $this->clearFaceVerification($request);

            return response()->json([
                'matched' => false,
                'message' => 'You are not registered at this site.',
            ]);
        }

        $this->markFaceVerified($request, $match['employee']->id);

        return response()->json([
            'matched' => true,

            'employee' => [
                'id' => $match['employee']->id,
                'name' => $match['employee']->name,
                'employee_code' => $match['employee']->employee_code,
            ],

            'distance' => round($match['distance'], 4),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FIRST-TIME EMPLOYEE FACE REGISTRATION
    |--------------------------------------------------------------------------
    |
    | Employees can register their face only once.
    | Once a face_descriptor exists, only an administrator can change it.
    | Several camera samples are averaged into one descriptor, and a face
    | that already belongs to another employee is refused.
    |
    */

    public function registerFaceFirstTime(
        Request $request,
        Employee $employee,
        FaceMatcher $matcher
    ) {
        $validated = $request->validate(FaceMatcher::rules(10));

        if (strtolower($employee->status) !== 'active') {

            return response()->json([
                'success' => false,
                'message' => 'This employee is not active.',
            ], 403);
        }

        if (! empty($employee->face_descriptor)) {

            return response()->json([
                'success' => false,
                'message' =>
                    'Face registration has already been completed. ' .
                    'Please contact an administrator if your face needs to be changed.',
            ], 403);
        }

        $descriptor = $matcher->averageSamples(
            FaceMatcher::samplesFromRequest($validated)
        );

        if (! $descriptor) {

            return response()->json([
                'success' => false,
                'message' => 'The camera images did not match each other. Please hold still and try again.',
            ], 422);
        }

        $duplicate = $matcher->findDuplicate($descriptor, $employee->id);

        if ($duplicate) {

            return response()->json([
                'success' => false,
                'message' =>
                    'This face is already registered to another employee. ' .
                    'Please see an administrator.',
            ], 409);
        }

        $employee->face_descriptor = json_encode($descriptor);
        $employee->face_registered_at = now();
        $employee->save();

        // They have just shown their face, so let them sign in straight away.
        $this->markFaceVerified($request, $employee->id);

        return response()->json([
            'success' => true,
            'message' =>
                'Face registered successfully. ' .
                'Future face changes must be made by an administrator.',
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | FACE VERIFICATION FOR KIOSK ACTIONS
    |--------------------------------------------------------------------------
    |
    | Sign in / going out / returning / sign out are only accepted for the
    | employee whose face was recognised on this device in the last few
    | minutes. Each recognition allows one action.
    |
    */

    private const FACE_VERIFIED_SECONDS = 180;

    private function markFaceVerified(Request $request, int $employeeId): void
    {
        $request->session()->put('face_verified', [
            'employee_id' => $employeeId,
            'at' => now()->getTimestamp(),
        ]);
    }

    private function clearFaceVerification(Request $request): void
    {
        $request->session()->forget('face_verified');
    }

    /**
     * True if this employee was face-verified recently. The verification is
     * used up, so the next action needs a new recognition.
     */
    private function consumeFaceVerification(Request $request, $employeeId): bool
    {
        $verified = $request->session()->pull('face_verified');

        return is_array($verified)
            && (int) ($verified['employee_id'] ?? 0) === (int) $employeeId
            && now()->getTimestamp() - (int) ($verified['at'] ?? 0) <= self::FACE_VERIFIED_SECONDS;
    }

    private function faceNotVerifiedResponse()
    {
        return redirect()
            ->route('attendance.employee')
            ->with('error', 'Please identify yourself with face recognition first, then try again.');
    }
}
