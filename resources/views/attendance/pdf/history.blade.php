<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Attendance History Report</title>

    <style>
        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #111827;
        }

        .header {
            text-align: center;
            margin-bottom: 18px;
            border-bottom: 2px solid #111827;
            padding-bottom: 12px;
        }

        .company {
            font-size: 20px;
            font-weight: bold;
        }

        .title {
            font-size: 15px;
            margin-top: 5px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 9px;
            color: #4b5563;
            margin-top: 5px;
        }

        .summary {
            width: 100%;
            margin-bottom: 15px;
        }

        .summary td {
            padding: 4px 6px;
        }

        .summary .label {
            font-weight: bold;
            width: 100px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #0f172a;
            color: white;
            font-weight: bold;
            padding: 7px 5px;
            border: 1px solid #0f172a;
            text-align: center;
        }

        td {
            padding: 6px 5px;
            border: 1px solid #d1d5db;
            vertical-align: middle;
        }

        tr:nth-child(even) td {
            background: #f8fafc;
        }

        .center {
            text-align: center;
        }

        .late {
            color: #b91c1c;
            font-weight: bold;
        }

        .present {
            color: #166534;
            font-weight: bold;
        }

        .footer {
            margin-top: 18px;
            text-align: center;
            font-size: 8px;
            color: #6b7280;
        }
    </style>
</head>

<body>

    <!-- HEADER -->

    <div class="header">

        <div class="company">
            GOODMAN FIELDER
        </div>

        <div class="title">
            Attendance History Report
        </div>

        <div class="subtitle">
            Generated:
            {{ $generatedAt->format('d/m/Y h:i A') }}
        </div>

    </div>


    <!-- REPORT SUMMARY -->

    <table class="summary">

        <tr>

            <td class="label">
                Report From:
            </td>

            <td>
                {{ $dateFrom ?: 'All Dates' }}
            </td>

            <td class="label">
                Report To:
            </td>

            <td>
                {{ $dateTo ?: 'All Dates' }}
            </td>

        </tr>

        <tr>

            <td class="label">
                Total Records:
            </td>

            <td colspan="3">
                {{ $attendances->count() }}
            </td>

        </tr>

    </table>


    <!-- ATTENDANCE TABLE -->

    <table>

        <thead>

            <tr>

                <th>Employee</th>
                <th>Employee Code</th>
                <th>Department</th>
                <th>Date</th>
                <th>Sign In</th>
                <th>Status</th>
                <th>Going Out</th>
                <th>Returning</th>
                <th>Total Outside</th>
                <th>Sign Out</th>

            </tr>

        </thead>


        <tbody>

            @forelse($attendances as $attendance)

                @php

                    $outings = $attendance->outings ?? collect();

                    /*
                    |--------------------------------------------------------------------------
                    | Going Out Times
                    |--------------------------------------------------------------------------
                    */

                    $goingOut = $outings
                        ->pluck('out_time')
                        ->filter()
                        ->map(function ($time) {
                            return \Carbon\Carbon::parse($time)
                                ->format('h:i A');
                        })
                        ->implode(', ');


                    /*
                    |--------------------------------------------------------------------------
                    | Returning Times
                    |--------------------------------------------------------------------------
                    */

                    $returning = $outings
                        ->pluck('return_time')
                        ->filter()
                        ->map(function ($time) {
                            return \Carbon\Carbon::parse($time)
                                ->format('h:i A');
                        })
                        ->implode(', ');


                    /*
                    |--------------------------------------------------------------------------
                    | Total Time Outside
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

                @endphp


                <tr>

                    <!-- Employee -->

                    <td>
                        {{ $attendance->employee->name ?? '' }}
                    </td>


                    <!-- Employee Code -->

                    <td class="center">
                        {{ $attendance->employee->employee_code ?? '' }}
                    </td>


                    <!-- Department -->

                    <td>
                        {{ $attendance->employee->department ?? '' }}
                    </td>


                    <!-- Date -->

                    <td class="center">

                        @if($attendance->attendance_date)

                            {{ \Carbon\Carbon::parse(
                                $attendance->attendance_date
                            )->format('d/m/Y') }}

                        @endif

                    </td>


                    <!-- Sign In -->

                    <td class="center">

                        @if($attendance->check_in)

                            {{ \Carbon\Carbon::parse(
                                $attendance->check_in
                            )->format('h:i A') }}

                        @endif

                    </td>


                    <!-- Status -->

                    <td class="center
                        {{ strtolower($attendance->status ?? '') === 'late'
                            ? 'late'
                            : 'present'
                        }}"
                    >

                        {{ $attendance->status ?? '' }}

                    </td>


                    <!-- Going Out -->

                    <td class="center">

                        {{ $goingOut ?: '-' }}

                    </td>


                    <!-- Returning -->

                    <td class="center">

                        {{ $returning ?: '-' }}

                    </td>


                    <!-- Total Outside -->

                    <td class="center">

                        {{ $totalOutside }}

                    </td>


                    <!-- Sign Out -->

                    <td class="center">

                        @if($attendance->check_out)

                            {{ \Carbon\Carbon::parse(
                                $attendance->check_out
                            )->format('h:i A') }}

                        @endif

                    </td>

                </tr>


            @empty

                <tr>

                    <td
                        colspan="10"
                        style="text-align:center; padding:20px;"
                    >
                        No attendance records found.
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <!-- FOOTER -->

    <div class="footer">

        Goodman Fielder Attendance System
        &nbsp; | &nbsp;
        Attendance History Report

    </div>

</body>
</html>