<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AttendanceHistoryExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithStyles
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Get attendance records
     */
    public function collection()
    {
        $request = $this->request;

        $query = Attendance::with([
            'employee',
            'outings',
        ]);

        /*
        |--------------------------------------------------------------------------
        | DATE FILTER
        |--------------------------------------------------------------------------
        | The Attendance History page uses "date", so Excel must use the same.
        */

        if ($request->filled('date')) {

            $query->whereDate(
                'attendance_date',
                $request->date
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
                function ($q) use ($search) {

                    $q->where(function ($employeeQuery) use ($search) {

                        $employeeQuery
                            ->where(
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
                function ($q) use ($request) {

                    $q->where(
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

        return $query
            ->orderBy('attendance_date', 'desc')
            ->orderBy('check_in')
            ->get();
    }

    /**
     * Excel headings
     */
    public function headings(): array
    {
        return [
            'Employee',
            'Employee Code',
            'Department',
            'Date',
            'Sign In',
            'Status',
            'Going Out',
            'Returning',
            'Total Outside',
            'Sign Out',
        ];
    }

    /**
     * Map each attendance record
     */
    public function map($attendance): array
    {
        $outings = $attendance->outings ?? collect();

        /*
        |--------------------------------------------------------------------------
        | OUTING TIMES
        |--------------------------------------------------------------------------
        */

        $goingOutTimes = $outings
            ->pluck('out_time')
            ->filter()
            ->map(function ($time) {

                return Carbon::parse($time)
                    ->format('h:i A');

            })
            ->implode(', ');

        $returningTimes = $outings
            ->pluck('return_time')
            ->filter()
            ->map(function ($time) {

                return Carbon::parse($time)
                    ->format('h:i A');

            })
            ->implode(', ');

        /*
        |--------------------------------------------------------------------------
        | TOTAL TIME OUTSIDE
        |--------------------------------------------------------------------------
        */

        $totalMinutes = $outings->sum(function ($outing) {

            return (int) ($outing->duration_minutes ?? 0);

        });

        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;

        if ($hours > 0) {

            $totalOutside =
                $hours . ' hr ' .
                $minutes . ' min';

        } else {

            $totalOutside =
                $minutes . ' min';
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN EXCEL ROW
        |--------------------------------------------------------------------------
        */

        return [

            $attendance->employee->name ?? '',

            $attendance->employee->employee_code ?? '',

            $attendance->employee->department ?? '',

            $attendance->attendance_date
                ? Carbon::parse($attendance->attendance_date)
                    ->format('d/m/Y')
                : '',

            $attendance->check_in
                ? Carbon::parse($attendance->check_in)
                    ->format('h:i A')
                : '',

            $attendance->status ?? '',

            $goingOutTimes,

            $returningTimes,

            $totalOutside,

            $attendance->check_out
                ? Carbon::parse($attendance->check_out)
                    ->format('h:i A')
                : '',
        ];
    }

    /**
     * Excel formatting
     */
    public function styles(Worksheet $sheet)
    {
        return [

            1 => [

                'font' => [
                    'bold' => true,
                    'color' => [
                        'rgb' => 'FFFFFF',
                    ],
                ],

                'fill' => [
                    'fillType' =>
                        \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,

                    'startColor' => [
                        'rgb' => '0F172A',
                    ],
                ],

            ],

        ];
    }
}