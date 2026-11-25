<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class EmployeeAttendanceReport
{
    /**
     * One employee's attendance, one row per day, oldest first.
     * Days with no sign-in are "No record": the system holds no roster or leave
     * calendar, so it cannot tell an absence from a day off.
     */
    public function build(Employee $employee, Carbon $from, Carbon $to, bool $includeEmptyDays): array
    {
        $records = Attendance::with(['outings' => fn ($query) => $query->orderBy('out_time')])
            ->where('employee_id', $employee->id)
            ->whereDate('attendance_date', '>=', $from)
            ->whereDate('attendance_date', '<=', $to)
            ->get()
            ->keyBy(fn ($record) => $record->attendance_date->format('Y-m-d'));

        // Days that have not happened yet are not missing records.
        $lastDay = $to->copy()->min(Carbon::today());
        $rows = collect();

        if ($lastDay->gte($from)) {
            foreach (CarbonPeriod::create($from, $lastDay) as $day) {
                $record = $records->get($day->format('Y-m-d'));
                $rows->push($record ? $this->recordRow($record) : $this->emptyRow($day));
            }
        }

        $recorded = $rows->where('hasRecord', true);
        $empty = $rows->where('hasRecord', false);

        return [
            'employee' => $employee,
            'from' => $from,
            'to' => $to,
            'includeEmptyDays' => $includeEmptyDays,
            'rows' => ($includeEmptyDays ? $rows : $recorded)->values(),
            'summary' => [
                'daysAttended' => $recorded->whereNotNull('checkIn')->count(),
                'late' => $recorded->where('status', 'Late')->count(),
                'leave' => $recorded->where('status', 'Leave')->count(),
                'absent' => $recorded->where('status', 'Absent')->count(),
                'noRecordWeekdays' => $empty->filter(fn ($row) => $row['date']->isWeekday())->count(),
                'missingSignOut' => $recorded->where('note', 'No sign out')->count(),
                'outings' => $recorded->sum(fn ($row) => count($row['outings'])),
                'outsideMinutes' => $recorded->sum('outsideMinutes'),
                'onSiteMinutes' => $recorded->sum('onSiteMinutes'),
            ],
        ];
    }

    public static function duration(?int $minutes): string
    {
        return $minutes === null ? '-' : sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    private function recordRow(Attendance $record): array
    {
        $checkIn = $this->time($record->check_in);
        $checkOut = $this->time($record->check_out);

        $outings = $record->outings->map(function ($outing) {
            $out = $this->time($outing->out_time);
            $return = $this->time($outing->return_time);

            return [
                'out' => $out,
                'return' => $return,
                'reason' => trim(implode(', ', array_filter([$outing->reason, $outing->destination]))),
                'minutes' => $outing->duration_minutes !== null
                    ? (int) $outing->duration_minutes
                    : $this->minutesBetween($out, $return),
            ];
        })->all();

        $outside = (int) array_sum(array_column($outings, 'minutes'));
        $total = $this->minutesBetween($checkIn, $checkOut);

        $note = '';
        if ($checkIn && ! $checkOut) {
            $note = $record->attendance_date->isToday() ? 'Still signed in' : 'No sign out';
        }

        return [
            'date' => $record->attendance_date,
            'hasRecord' => true,
            'status' => $record->status,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'outings' => $outings,
            'outsideMinutes' => $outside,
            'onSiteMinutes' => $total === null ? null : max(0, $total - $outside),
            'note' => $note,
            'remarks' => (string) $record->remarks,
        ];
    }

    private function emptyRow(Carbon $day): array
    {
        return [
            'date' => Carbon::instance($day),
            'hasRecord' => false,
            'status' => 'No record',
            'checkIn' => null,
            'checkOut' => null,
            'outings' => [],
            'outsideMinutes' => null,
            'onSiteMinutes' => null,
            'note' => '',
            'remarks' => '',
        ];
    }

    private function time(?string $value): ?Carbon
    {
        return $value ? Carbon::today()->setTimeFromTimeString($value) : null;
    }

    private function minutesBetween(?Carbon $start, ?Carbon $end): ?int
    {
        return $start && $end && $end->gte($start) ? (int) round($start->diffInMinutes($end)) : null;
    }
}
