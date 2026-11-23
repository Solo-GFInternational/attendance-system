{{--
    Emergency roll count sheet: employees in the office + visitors on site,
    with a tick box per person. Used by the downloadable PDF
    (attendance/pdf/emergency) and by the Emergency Roll Call page's print
    view, which is what "Download PDF" falls back to when offline.
    Styles are scoped to .rc-sheet and kept to simple CSS that DomPDF supports.

    Expects: $inOfficeList, $visitors, $currentlyOut, $selectedSite, $generatedAt
--}}

<style>
    .rc-sheet { font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px; color: #111827; }
    .rc-sheet .rc-head { width: 100%; border-bottom: 2px solid #0f1f3d; padding-bottom: 8px; margin-bottom: 12px; }
    .rc-sheet .rc-head td { border: 0; padding: 0; vertical-align: middle; background: none; }
    .rc-sheet .rc-logo { width: 46px; height: 46px; }
    .rc-sheet .rc-company { font-size: 15px; font-weight: bold; color: #0f1f3d; }
    .rc-sheet .rc-title { font-size: 13px; font-weight: bold; color: #c62f3b; margin-top: 2px; }
    .rc-sheet .rc-meta { text-align: right; font-size: 9.5px; color: #374151; line-height: 1.5; }

    .rc-sheet .rc-summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
    .rc-sheet .rc-summary td { width: 25%; border: 1px solid #d1d5db; padding: 7px 8px; text-align: center; background: #f8fafc; }
    .rc-sheet .rc-summary .rc-num { font-size: 18px; font-weight: bold; color: #0f1f3d; }
    .rc-sheet .rc-summary .rc-total .rc-num { color: #c62f3b; }
    .rc-sheet .rc-summary .rc-lbl { font-size: 8.5px; color: #4b5563; margin-top: 2px; }

    .rc-sheet h2 { font-size: 12px; margin: 14px 0 6px; color: #0f1f3d; }
    .rc-sheet h2 span { font-weight: normal; color: #4b5563; }
    .rc-sheet .rc-table { width: 100%; border-collapse: collapse; }
    .rc-sheet .rc-table th { background: #0f1f3d; color: #ffffff; font-size: 9px; text-align: left; padding: 6px 6px; border: 1px solid #0f1f3d; }
    .rc-sheet .rc-table td { border: 1px solid #d1d5db; padding: 6px 6px; vertical-align: middle; }
    .rc-sheet .rc-table tr:nth-child(even) td { background: #f8fafc; }
    .rc-sheet .rc-no { width: 22px; text-align: center; color: #6b7280; }
    .rc-sheet .rc-check { width: 64px; text-align: center; }
    .rc-sheet .rc-box { display: inline-block; width: 13px; height: 13px; border: 1.5px solid #111827; }
    .rc-sheet .rc-sub { color: #6b7280; font-size: 8.5px; }
    .rc-sheet .rc-empty { text-align: center; color: #6b7280; padding: 12px; }

    .rc-sheet .rc-sign { width: 100%; margin-top: 22px; border-collapse: collapse; }
    .rc-sheet .rc-sign td { border: 0; padding: 14px 10px 0 0; font-size: 9.5px; color: #374151; width: 33%; }
    .rc-sheet .rc-line { border-bottom: 1px solid #111827; height: 16px; margin-bottom: 3px; }
    .rc-sheet .rc-note { margin-top: 14px; font-size: 8.5px; color: #6b7280; line-height: 1.5; }
</style>

<div class="rc-sheet">

    <table class="rc-head">
        <tr>
            <td style="width: 56px;">
                @include('partials.brand-mark', ['class' => 'rc-logo'])
            </td>
            <td>
                <div class="rc-company">GOODMAN FIELDER</div>
                <div class="rc-title">EMERGENCY ROLL COUNT</div>
            </td>
            <td class="rc-meta">
                <strong>Site:</strong> {{ $selectedSite ? $selectedSite->name : 'All sites' }}<br>
                <strong>Data as at:</strong> {{ $generatedAt->format('d/m/Y h:i A') }}
            </td>
        </tr>
    </table>

    <table class="rc-summary">
        <tr>
            <td>
                <div class="rc-num">{{ $inOfficeList->count() }}</div>
                <div class="rc-lbl">Employees in office</div>
            </td>
            <td>
                <div class="rc-num">{{ $visitors->count() }}</div>
                <div class="rc-lbl">Visitors on site</div>
            </td>
            <td class="rc-total">
                <div class="rc-num">{{ $inOfficeList->count() + $visitors->count() }}</div>
                <div class="rc-lbl">Total to account for</div>
            </td>
            <td>
                <div class="rc-num">{{ $currentlyOut }}</div>
                <div class="rc-lbl">Employees signed out temporarily (not listed)</div>
            </td>
        </tr>
    </table>

    <h2>Employees in office <span>({{ $inOfficeList->count() }})</span></h2>
    <table class="rc-table">
        <thead>
            <tr>
                <th class="rc-no">#</th>
                <th>Employee</th>
                <th>Employee ID</th>
                <th>Department</th>
                <th>Signed in</th>
                <th class="rc-check">Accounted</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($inOfficeList as $i => $person)
                <tr>
                    <td class="rc-no">{{ $i + 1 }}</td>
                    <td><strong>{{ $person->employee->name }}</strong></td>
                    <td>{{ $person->employee->employee_code }}</td>
                    <td>{{ $person->employee->department ?: '—' }}</td>
                    <td>{{ $person->check_in ? \Carbon\Carbon::parse($person->check_in)->format('h:i A') : '—' }}</td>
                    <td class="rc-check"><span class="rc-box"></span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="rc-empty">No employees currently signed in to the office.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Visitors on site <span>({{ $visitors->count() }})</span></h2>
    <table class="rc-table">
        <thead>
            <tr>
                <th class="rc-no">#</th>
                <th>Visitor</th>
                <th>Company</th>
                <th>Purpose of visit</th>
                <th>Signed in</th>
                <th class="rc-check">Accounted</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($visitors as $i => $visitor)
                <tr>
                    <td class="rc-no">{{ $i + 1 }}</td>
                    <td><strong>{{ $visitor->name }}</strong></td>
                    <td>{{ $visitor->company ?: '—' }}</td>
                    <td>{{ $visitor->purpose ?: '—' }}</td>
                    <td>
                        {{ $visitor->sign_in ? $visitor->sign_in->format('h:i A') : '—' }}
                        @if ($visitor->sign_in && ! $visitor->sign_in->isToday())
                            <div class="rc-sub">{{ $visitor->sign_in->format('d/m/Y') }}</div>
                        @endif
                    </td>
                    <td class="rc-check"><span class="rc-box"></span></td>
                </tr>
            @empty
                <tr><td colspan="6" class="rc-empty">No visitors currently on site.</td></tr>
            @endforelse
        </tbody>
    </table>

    <table class="rc-sign">
        <tr>
            <td><div class="rc-line"></div>Warden name</td>
            <td><div class="rc-line"></div>Signature</td>
            <td><div class="rc-line"></div>Time roll count completed</td>
        </tr>
    </table>

    <div class="rc-note">
        Based on attendance and visitor records at the time shown above. Physically verify each person's location;
        visitors may have left without signing out. Employees signed out temporarily are not listed and should be
        confirmed separately.
    </div>

</div>
