<?php

namespace App\Exports;

use Illuminate\Support\Collection;
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
    /**
     * @param  Collection  $rows  the same rows the Attendance History page shows
     */
    public function __construct(private Collection $rows)
    {
    }

    public function collection()
    {
        return $this->rows;
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