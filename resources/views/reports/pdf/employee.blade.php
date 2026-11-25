@php
    use App\Services\EmployeeAttendanceReport as Report;
    use App\Services\EmployeeReportWorkbook as Workbook;
    $employee = $report['employee'];
    $summary = $report['summary'];
@endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Employee Attendance Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 9px; color: #111827; }
        .header { text-align: center; margin-bottom: 12px; border-bottom: 2px solid #111827; padding-bottom: 8px; }
        .title { font-size: 15px; font-weight: bold; }
        .muted { color: #4b5563; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th { background: #0f172a; color: white; padding: 6px 5px; border: 1px solid #0f172a; text-align: left; }
        td { padding: 5px; border: 1px solid #d1d5db; vertical-align: top; }
        tr { page-break-inside: avoid; }
        .details td { border: 0; padding: 3px 5px; }
        .label { font-weight: bold; }
        .empty td { color: #6b7280; background: #f8fafc; }
        .alert { color: #b91c1c; font-weight: bold; }
        .signature td { border: 0; padding-top: 28px; }
    </style>
</head>
<body>

<div class="header">
    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/goodman-fielder-mark.png'))) }}" alt="" style="width: 44px; height: 44px;">
    <div class="title">Goodman Fielder - Employee Attendance Report</div>
    <div class="muted">{{ $report['from']->format('d/m/Y') }} to {{ $report['to']->format('d/m/Y') }} | Generated {{ $generatedAt->format('d/m/Y h:i A') }}</div>
</div>

<table class="details">
    <tr>
        <td class="label">Employee</td><td>{{ $employee->name }}</td>
        <td class="label">Days attended</td><td>{{ $summary['daysAttended'] }}</td>
        <td class="label">Outings</td><td>{{ $summary['outings'] }}</td>
    </tr>
    <tr>
        <td class="label">Employee ID</td><td>{{ $employee->employee_code }}</td>
        <td class="label">Late</td><td>{{ $summary['late'] }}</td>
        <td class="label">Time outside</td><td>{{ Report::duration($summary['outsideMinutes']) }}</td>
    </tr>
    <tr>
        <td class="label">Department</td><td>{{ $employee->department ?: '-' }}</td>
        <td class="label">Leave / Absent</td><td>{{ $summary['leave'] }} / {{ $summary['absent'] }}</td>
        <td class="label">Hours on site</td><td>{{ Report::duration($summary['onSiteMinutes']) }}</td>
    </tr>
    <tr>
        <td class="label">Designation / Site</td><td>{{ $employee->designation ?: '-' }} / {{ $employee->site?->name ?? '-' }}</td>
        <td class="label">No record (Mon-Fri)</td><td>{{ $summary['noRecordWeekdays'] }}</td>
        <td class="label">No sign out</td><td>{{ $summary['missingSignOut'] }}</td>
    </tr>
</table>

<table>
    <thead>
        <tr>
            <th>Date</th><th>Status</th><th>Sign In</th><th>Sign Out</th>
            <th>Outings</th><th>Time Outside</th><th>Hours On Site</th><th>Note</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($report['rows'] as $row)
            <tr class="{{ $row['hasRecord'] ? '' : 'empty' }}">
                <td>{{ $row['date']->format('D d/m/Y') }}</td>
                <td class="{{ in_array($row['status'], ['Late', 'Absent']) ? 'alert' : '' }}">{{ $row['status'] }}</td>
                <td>{{ $row['checkIn']?->format('h:i A') ?? '-' }}</td>
                <td>{{ $row['checkOut']?->format('h:i A') ?? '-' }}</td>
                <td>@forelse ($row['outings'] as $outing)<div>{{ Workbook::outingText($outing) }}</div>@empty - @endforelse</td>
                <td>{{ Report::duration($row['outsideMinutes']) }}</td>
                <td>{{ Report::duration($row['onSiteMinutes']) }}</td>
                <td>{{ collect([$row['note'], $row['remarks']])->filter()->implode('. ') }}</td>
            </tr>
        @empty
            <tr><td colspan="8" style="text-align: center; padding: 20px;">No attendance records in this period.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="muted">
    Times are h:mm. Hours on site is sign in to sign out, less time outside; days without a sign out are not counted in it.
    "No record" means there was no sign in that day; it may be a day off, leave or public holiday.
</div>

</body>
</html>
