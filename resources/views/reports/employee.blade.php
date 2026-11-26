@php
    use App\Services\EmployeeAttendanceReport as Report;
    use App\Services\EmployeeReportWorkbook as Workbook;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Report</title>
    @include('partials.theme-init')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .report-card { border: 0; border-radius: 14px; box-shadow: 0 2px 10px rgba(0, 0, 0, .04); }
        .report-table th { font-size: 13px; color: var(--gf-muted); white-space: nowrap; }
        .report-table .no-record td { color: var(--gf-muted); background: var(--gf-subtle); }
        .report-stat { border: 1px solid var(--gf-border); border-radius: 10px; padding: 12px 14px; height: 100%; }
        .report-stat strong { display: block; font-size: 1.4rem; }
    </style>
</head>

<body class="admin-ui">

<div class="container-fluid py-4 px-4">

    @include('partials.admin-nav')

    <div class="card report-card mb-4">
        <div class="card-body p-4">

            <h2 class="fw-bold mb-1">Employee Report</h2>
            <p class="text-muted mb-4">One person's attendance between two dates</p>

            @if ($problems)
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($problems as $problem)
                            <li>{{ $problem }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="GET" action="{{ route('reports.employee') }}">
                <div class="row g-3">

                    <div class="col-md-6 col-xl-3">
                        <label for="employee_search" class="form-label fw-semibold">Find employee</label>
                        <input type="search" id="employee_search" class="form-control" autocomplete="off"
                               placeholder="Type a name, ID or site">
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <label for="employee_id" class="form-label fw-semibold">Employee</label>
                        <select id="employee_id" name="employee_id" class="form-select" required>
                            <option value="">-- Select employee --</option>
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected($values['employee_id'] === (string) $employee->id)>
                                    {{ $employee->name }} ({{ $employee->employee_code }}){{ $employee->site ? ' - ' . $employee->site->name : '' }}{{ $employee->status === 'Active' ? '' : ' - Inactive' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-xl-2">
                        <label for="from" class="form-label fw-semibold">From</label>
                        <input type="date" id="from" name="from" class="form-control" value="{{ $values['from'] }}" required>
                    </div>

                    <div class="col-6 col-xl-2">
                        <label for="to" class="form-label fw-semibold">To</label>
                        <input type="date" id="to" name="to" class="form-control" value="{{ $values['to'] }}" required>
                    </div>

                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 mt-3">
                    @foreach ($ranges as $label => [$start, $end])
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-from="{{ $start->format('Y-m-d') }}"
                                data-to="{{ $end->format('Y-m-d') }}">{{ $label }}</button>
                    @endforeach

                    <div class="form-check ms-md-3">
                        <input class="form-check-input" type="checkbox" id="hide_empty" name="hide_empty" value="1" @checked($values['hide_empty'])>
                        <label class="form-check-label" for="hide_empty">Hide days with no record</label>
                    </div>

                    <button type="submit" class="btn btn-primary ms-md-auto px-4">Show Report</button>
                </div>
            </form>

        </div>
    </div>

    @if ($report)
        @php
            $summary = $report['summary'];
            $query = [
                'employee_id' => $report['employee']->id,
                'from' => $report['from']->format('Y-m-d'),
                'to' => $report['to']->format('Y-m-d'),
                'hide_empty' => $report['includeEmptyDays'] ? null : 1,
            ];
        @endphp

        <div class="card report-card">
            <div class="card-body p-4">

                <div class="d-flex flex-wrap justify-content-between gap-3 mb-4">
                    <div>
                        <h4 class="fw-bold mb-1">{{ $report['employee']->name }}</h4>
                        <div class="text-muted">
                            {{ collect([$report['employee']->employee_code, $report['employee']->department, $report['employee']->designation, $report['employee']->site?->name])->filter()->implode(' | ') }}
                        </div>
                        <div class="fw-semibold mt-1">{{ $report['from']->format('d/m/Y') }} to {{ $report['to']->format('d/m/Y') }}</div>
                    </div>
                    <div class="d-flex gap-2 align-items-start">
                        <a href="{{ route('reports.employee.pdf', $query) }}" class="btn btn-danger">Download PDF</a>
                        <a href="{{ route('reports.employee.excel', $query) }}" class="btn btn-success">Download Excel</a>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    @foreach ([
                        'Days attended' => $summary['daysAttended'],
                        'Late' => $summary['late'],
                        'Leave' => $summary['leave'],
                        'Absent' => $summary['absent'],
                        'No record (Mon-Fri)' => $summary['noRecordWeekdays'],
                        'No sign out' => $summary['missingSignOut'],
                        'Outings' => $summary['outings'],
                        'Time outside (h:mm)' => Report::duration($summary['outsideMinutes']),
                        'Hours on site (h:mm)' => Report::duration($summary['onSiteMinutes']),
                    ] as $label => $value)
                        <div class="col-6 col-md-4 col-xl-2">
                            <div class="report-stat"><strong>{{ $value }}</strong><span class="text-muted">{{ $label }}</span></div>
                        </div>
                    @endforeach
                </div>

                <div class="table-responsive">
                    <table class="table align-middle report-table mb-2">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th><th>Status</th><th>Sign In</th><th>Sign Out</th>
                                <th>Outings</th><th>Time Outside</th><th>Hours On Site</th><th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($report['rows'] as $row)
                                <tr @class(['no-record' => ! $row['hasRecord']])>
                                    <td class="text-nowrap">{{ $row['date']->format('D d/m/Y') }}</td>
                                    <td @class(['text-nowrap', 'text-danger fw-semibold' => in_array($row['status'], ['Late', 'Absent'])])>{{ $row['status'] }}</td>
                                    <td class="text-nowrap">{{ $row['checkIn']?->format('h:i A') ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $row['checkOut']?->format('h:i A') ?? '-' }}</td>
                                    <td>
                                        @forelse ($row['outings'] as $outing)
                                            <div>{{ Workbook::outingText($outing) }}</div>
                                        @empty
                                            -
                                        @endforelse
                                    </td>
                                    <td>{{ Report::duration($row['outsideMinutes']) }}</td>
                                    <td>{{ Report::duration($row['onSiteMinutes']) }}</td>
                                    <td>{{ collect([$row['note'], $row['remarks']])->filter()->implode('. ') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted py-5">No attendance records in this period.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <small class="text-muted">
                    Hours on site is sign in to sign out, less time outside. Days without a sign out are not counted in it.
                    "No record" means nobody signed in that day; it may be a day off, leave or public holiday.
                </small>

            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const select = document.getElementById('employee_id');
        const search = document.getElementById('employee_search');
        const options = Array.from(select.options);

        search.addEventListener('input', function () {
            const words = search.value.toLowerCase().split(/\s+/).filter(Boolean);
            const current = select.value;
            const matches = options.slice(1).filter(function (option) {
                const text = option.textContent.toLowerCase();
                return words.every(function (word) { return text.includes(word); });
            });

            select.replaceChildren(options[0], ...matches);

            if (words.length && matches.length === 1) {
                select.value = matches[0].value;
            } else {
                select.value = matches.some(function (option) { return option.value === current; }) ? current : '';
            }
        });

        document.querySelectorAll('[data-from]').forEach(function (button) {
            button.addEventListener('click', function () {
                document.getElementById('from').value = button.dataset.from;
                document.getElementById('to').value = button.dataset.to;
            });
        });
    });
</script>

</body>
</html>
