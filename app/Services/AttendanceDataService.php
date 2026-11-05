<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\AttendanceOuting;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AttendanceDataService
{
    /**
     * Get attendance summary for a specific date.
     */
    public function dailySummary(?string $date = null): array
    {
        $date = $date
            ? Carbon::parse($date)->toDateString()
            : Carbon::today()->toDateString();

        $employees = Employee::query()
            ->where('status', 'Active')
            ->with([
                'site',
                'attendances' => function ($query) use ($date) {
                    $query->whereDate('attendance_date', $date)
                        ->with('outings');
                },
            ])
            ->get();

        $present = 0;
        $late = 0;
        $absent = 0;
        $checkedOut = 0;
        $currentlyOut = 0;

        $records = [];

        foreach ($employees as $employee) {
            $attendance = $employee->attendances->first();

            if (!$attendance) {
                $status = 'Absent';
                $absent++;
            } else {
                $status = $attendance->status;

                if ($status === 'Present') {
                    $present++;
                } elseif ($status === 'Late') {
                    $late++;
                }

                if ($attendance->check_out) {
                    $checkedOut++;
                }

                $activeOuting = $attendance->outings
                    ->whereNull('return_time')
                    ->sortByDesc('out_time')
                    ->first();

                if ($activeOuting) {
                    $currentlyOut++;
                }
            }

            $records[] = [
                'employee_id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->name,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'site' => $employee->site?->name,
                'status' => $status,
                'check_in' => $attendance?->check_in,
                'check_out' => $attendance?->check_out,
            ];
        }

        return [
            'date' => $date,
            'total_staff' => $employees->count(),
            'present' => $present,
            'late' => $late,
            'absent' => $absent,
            'checked_out' => $checkedOut,
            'currently_out' => $currentlyOut,
            'employees' => $records,
        ];
    }

    /**
     * Get employees who are currently out on a specific date.
     */
    public function currentlyOut(?string $date = null): array
    {
        $date = $date
            ? Carbon::parse($date)->toDateString()
            : Carbon::today()->toDateString();

        $outings = AttendanceOuting::query()
            ->whereNull('return_time')
            ->whereHas('attendance', function ($query) use ($date) {
                $query->whereDate('attendance_date', $date);
            })
            ->with([
                'employee.site',
                'attendance',
            ])
            ->orderBy('out_time')
            ->get();

        return $outings->map(function ($outing) {
            return [
                'employee_id' => $outing->employee_id,
                'employee_code' => $outing->employee?->employee_code,
                'name' => $outing->employee?->name,
                'department' => $outing->employee?->department,
                'site' => $outing->employee?->site?->name,
                'out_time' => $outing->out_time,
                'reason' => $outing->reason,
            ];
        })->values()->all();
    }

    /**
     * Get attendance history for an employee.
     */
    public function employeeAttendance(
        int $employeeId,
        ?string $from = null,
        ?string $to = null
    ): array {
        $fromDate = $from
            ? Carbon::parse($from)->toDateString()
            : Carbon::today()->subDays(30)->toDateString();

        $toDate = $to
            ? Carbon::parse($to)->toDateString()
            : Carbon::today()->toDateString();

        $employee = Employee::query()
            ->with('site')
            ->find($employeeId);

        if (!$employee) {
            return [];
        }

        $attendances = Attendance::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('attendance_date', [$fromDate, $toDate])
            ->with('outings')
            ->orderBy('attendance_date')
            ->get();

        return [
            'employee' => [
                'id' => $employee->id,
                'employee_code' => $employee->employee_code,
                'name' => $employee->name,
                'department' => $employee->department,
                'designation' => $employee->designation,
                'site' => $employee->site?->name,
            ],
            'from' => $fromDate,
            'to' => $toDate,
            'attendance' => $attendances->map(function ($attendance) {
                return [
                    'date' => $attendance->attendance_date?->toDateString(),
                    'status' => $attendance->status,
                    'check_in' => $attendance->check_in,
                    'check_out' => $attendance->check_out,
                    'remarks' => $attendance->remarks,
                    'outings' => $attendance->outings
                        ->sortBy('out_time')
                        ->map(function ($outing) {
                            return [
                                'out_time' => $outing->out_time,
                                'return_time' => $outing->return_time,
                                'duration_minutes' => $outing->duration_minutes,
                                'reason' => $outing->reason,
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })->values()->all(),
        ];
    }

    /**
     * Get outing history.
     */
    public function outingHistory(
        ?string $from = null,
        ?string $to = null,
        ?int $employeeId = null
    ): array {
        $fromDate = $from
            ? Carbon::parse($from)->toDateString()
            : Carbon::today()->subDays(30)->toDateString();

        $toDate = $to
            ? Carbon::parse($to)->toDateString()
            : Carbon::today()->toDateString();

        $query = AttendanceOuting::query()
            ->whereHas('attendance', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('attendance_date', [$fromDate, $toDate]);
            })
            ->with([
                'employee.site',
                'attendance',
            ])
            ->orderBy('out_time');

        if ($employeeId) {
            $query->where('employee_id', $employeeId);
        }

        $outings = $query->get();

        return [
            'from' => $fromDate,
            'to' => $toDate,
            'total_outings' => $outings->count(),
            'total_completed_minutes' => $outings
                ->whereNotNull('return_time')
                ->sum('duration_minutes'),
            'currently_out' => $outings
                ->whereNull('return_time')
                ->count(),
            'outings' => $outings->map(function ($outing) {
                return [
                    'employee_id' => $outing->employee_id,
                    'employee_code' => $outing->employee?->employee_code,
                    'name' => $outing->employee?->name,
                    'department' => $outing->employee?->department,
                    'site' => $outing->employee?->site?->name,
                    'date' => $outing->attendance?->attendance_date?->toDateString(),
                    'out_time' => $outing->out_time,
                    'return_time' => $outing->return_time,
                    'duration_minutes' => $outing->duration_minutes,
                    'reason' => $outing->reason,
                ];
            })->values()->all(),
        ];
    }

    /**
 * Find an employee by name or employee code.
 */
public function findEmployee(string $search): ?array
{
    $search = trim($search);

    if ($search === '') {
        return null;
    }

    $employee = Employee::query()
        ->with('site')
        ->where(function ($query) use ($search) {
            $query->where('employee_code', $search)
                ->orWhere('name', 'like', '%' . $search . '%');
        })
        ->orderByRaw(
            "CASE
                WHEN employee_code = ? THEN 1
                WHEN name = ? THEN 2
                ELSE 3
            END",
            [$search, $search]
        )
        ->first();

    if (!$employee) {
        return null;
    }

    return [
        'id' => $employee->id,
        'employee_code' => $employee->employee_code,
        'name' => $employee->name,
        'department' => $employee->department,
        'designation' => $employee->designation,
        'site' => $employee->site?->name,
    ];
}
}