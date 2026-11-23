<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f1f3d">
    <meta name="description" content="Goodman Fielder Emergency Roll Call">

    <link rel="manifest" href="/manifest.json">

    <title>Emergency Roll Call | Goodman Fielder</title>

    {{-- Everything is inline so the page still renders from the offline cache. --}}
    @include('partials.kiosk-theme')

    <style>

        /* =====================================================
           CONNECTION STATUS (in the header)
        ===================================================== */

        .connection-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 44px;
            padding: 0 16px;
            border-radius: 12px;
            background: rgba(19, 128, 63, .22);
            border: 1px solid rgba(110, 231, 160, .45);
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .04em;
            white-space: nowrap;
        }

        .connection-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #4ade80;
            box-shadow: 0 0 0 4px rgba(74, 222, 128, .25);
        }

        .connection-status.offline {
            background: rgba(198, 47, 59, .28);
            border-color: rgba(252, 165, 165, .5);
        }

        .connection-status.offline .connection-dot {
            background: #facc15;
            box-shadow: 0 0 0 4px rgba(250, 204, 21, .25);
        }

        .refresh-btn {
            height: 44px;
            min-height: 44px;
            background: #ffffff;
            color: var(--navy);
        }

        .refresh-btn:hover:not(:disabled) {
            background: #e8edf6;
        }


        /* =====================================================
           OFFLINE BANNER + SNAPSHOT MESSAGE
        ===================================================== */

        .offline-banner,
        .snapshot-message {
            display: none;
        }

        .offline-banner.show,
        .snapshot-message.show {
            display: flex;
        }

        .offline-banner small {
            display: block;
            margin-top: 3px;
            font-weight: 400;
        }


        /* =====================================================
           SYNC BAR
        ===================================================== */

        .sync-info {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 8px 24px;
            padding: 14px 20px;
            margin-bottom: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 12px;
            box-shadow: var(--shadow);
            color: var(--muted);
            font-size: 14px;
        }

        .sync-info span.item {
            display: inline-flex;
            align-items: center;
            gap: 7px;
        }

        .sync-info svg {
            width: 16px;
            height: 16px;
        }

        .sync-info strong {
            color: var(--text);
        }


        /* =====================================================
           SUMMARY CARDS
        ===================================================== */

        .cards {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .card {
            --accent: var(--blue);

            position: relative;
            padding: 20px 22px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: var(--accent);
        }

        .card .number {
            font-size: 38px;
            font-weight: 700;
            line-height: 1.1;
            color: var(--accent);
            font-variant-numeric: tabular-nums;
        }

        .card .label {
            margin-top: 6px;
            font-size: 13px;
            font-weight: 600;
            color: var(--muted);
        }

        .total-people  { --accent: var(--blue); }
        .in-office     { --accent: var(--green); }
        .out           { --accent: #c2570c; }
        .visitors-card { --accent: var(--violet); }
        .not-signed    { --accent: var(--red); }


        /* =====================================================
           ROLL CALL TABLES
        ===================================================== */

        .roll-call {
            padding: 0;
            margin-bottom: 24px;
            overflow: hidden;
        }

        .roll-call .k-card-head {
            padding: 20px 24px;
            margin: 0;
            border-bottom: 1px solid var(--border);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f8fafc;
            color: var(--muted);
            padding: 12px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .05em;
            text-transform: uppercase;
            white-space: nowrap;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 20px;
            border-bottom: 1px solid #eef1f5;
            font-size: 15px;
            vertical-align: middle;
        }

        tbody tr:last-child td {
            border-bottom: 0;
        }

        tbody tr:hover {
            background: #fafbfd;
        }

        .employee-name,
        .visitor-name {
            font-weight: 600;
        }

        .employee-id {
            margin-top: 2px;
            color: var(--muted);
            font-size: 13px;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 11px;
            border-radius: 999px;
            font-size: 12.5px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
        }

        .status-in       { background: var(--green-soft);  color: var(--green); }
        .status-out      { background: #fdf0e6;            color: #b4530a; }
        .status-not      { background: var(--red-soft);    color: var(--red); }
        .status-checkout { background: #eef1f5;            color: #4b5563; }
        .status-visitor  { background: var(--violet-soft); color: var(--violet); }

        .outing-info {
            font-size: 13px;
            color: #9a4a0b;
            line-height: 1.5;
        }

        .muted {
            color: #9aa5b5;
        }

        .visitor-purpose {
            max-width: 360px;
        }

        .empty {
            text-align: center;
            padding: 36px 20px;
            color: var(--muted);
        }


        /* =====================================================
           IMPORTANT NOTICE
        ===================================================== */

        .warning {
            line-height: 1.6;
            font-weight: 400;
        }

        .warning p {
            margin: 0;
        }

        .warning p + p {
            margin-top: 8px;
        }


        /* =====================================================
           PRINT: only the roll count sheet
        ===================================================== */

        .print-sheet {
            display: none;
        }

        @media print {
            @page { size: A4 portrait; margin: 12mm; }

            body { background: #ffffff !important; }

            body > *:not(.print-sheet) { display: none !important; }

            .print-sheet { display: block !important; }

            .print-sheet * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .rc-sheet .rc-table tr { break-inside: avoid; }
        }


        @media (max-width: 1200px) {
            .cards {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 800px) {
            .cards {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 520px) {
            .cards {
                grid-template-columns: 1fr;
            }
        }

    </style>

</head>

<body class="k-wide">

    @push('kiosk-hero-actions')
        <div id="connectionStatus" class="connection-status">
            <span id="connectionDot" class="connection-dot"></span>
            <span id="connectionText">CHECKING CONNECTION…</span>
        </div>

        <button id="refreshButton" class="k-btn sm refresh-btn" onclick="manualRefresh()">
            @include('partials.kiosk-icon', ['name' => 'refresh'])
            Refresh
        </button>

        <button id="pdfButton" class="k-btn sm refresh-btn" onclick="downloadRollCountPdf()"
                title="Employees in office and visitors on site, with tick boxes for the roll count">
            @include('partials.kiosk-icon', ['name' => 'download'])
            Roll Count PDF
        </button>
    @endpush

    @include('partials.kiosk-header', [
        'variant'  => 'emergency',
        'title'    => 'Emergency Roll Call',
        'subtitle' => ($selectedSite ? $selectedSite->name : 'All sites') . ' · Employee & visitor accountability',
        'icon'     => 'alert',
    ])


    <main class="k-main">

        {{-- Offline banner --}}
        <div id="offlineBanner" class="k-alert error offline-banner">
            @include('partials.kiosk-icon', ['name' => 'wifi'])
            <div>
                Offline emergency mode
                <small>No connection to the Attendance System. Showing the latest information saved on this device.</small>
            </div>
        </div>

        {{-- Last sync --}}
        <div class="sync-info">
            <span class="item">
                @include('partials.kiosk-icon', ['name' => 'clock'])
                <strong>Information as at:</strong>
                <span id="lastSync">{{ $generatedAt->format('d/m/Y h:i A') }}</span>
            </span>
            <span class="item">
                @include('partials.kiosk-icon', ['name' => 'wifi'])
                <strong>Data mode:</strong>
                <span id="dataMode">CHECKING…</span>
            </span>
        </div>

        {{-- Offline snapshot message --}}
        <div id="snapshotMessage" class="k-alert warning snapshot-message">
            @include('partials.kiosk-icon', ['name' => 'info'])
            <div>
                Showing the information saved on this device at
                <strong>{{ $generatedAt->format('d/m/Y h:i A') }}</strong>.
                People may have signed in or out since then. "Roll Count PDF" will open the print screen;
                choose <strong>Save as PDF</strong> or a printer.
            </div>
        </div>


        {{-- Summary --}}
        <div class="cards">

            <div class="card total-people">
                <div class="number">{{ $peopleOnSite }}</div>
                <div class="label">People on site</div>
            </div>

            <div class="card in-office">
                <div class="number">{{ $inOffice }}</div>
                <div class="label">Employees in office</div>
            </div>

            <div class="card out">
                <div class="number">{{ $currentlyOut }}</div>
                <div class="label">Employees currently out</div>
            </div>

            <div class="card visitors-card">
                <div class="number">{{ $visitorsOnSite }}</div>
                <div class="label">Visitors on site</div>
            </div>

            <div class="card not-signed">
                <div class="number">{{ $notSignedIn }}</div>
                <div class="label">Employees not signed in</div>
            </div>

        </div>


        {{-- Employee roll call --}}
        <section class="k-card roll-call">

            <div class="k-card-head">
                <div class="k-card-icon">@include('partials.kiosk-icon', ['name' => 'users'])</div>
                <div>
                    <h2>Employee Roll Call</h2>
                    <p>Current employee attendance and location status.</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th>Check in</th>
                            <th>Check out</th>
                            <th>Outing information</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rollCall as $person)
                            <tr>
                                <td>
                                    <div class="employee-name">{{ $person->employee->name }}</div>
                                    <div class="employee-id">ID: {{ $person->employee->employee_code }}</div>
                                </td>

                                <td>{{ $person->employee->department ?: '—' }}</td>

                                <td>
                                    @if ($person->status === 'in_office')
                                        <span class="status status-in">In office</span>
                                    @elseif ($person->status === 'out')
                                        <span class="status status-out">Currently out</span>
                                    @elseif ($person->status === 'not_signed_in')
                                        <span class="status status-not">Not signed in</span>
                                    @elseif ($person->status === 'checked_out')
                                        <span class="status status-checkout">Checked out</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($person->check_in)
                                        {{ \Carbon\Carbon::parse($person->check_in)->format('h:i A') }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($person->check_out)
                                        {{ \Carbon\Carbon::parse($person->check_out)->format('h:i A') }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($person->active_outing)
                                        <div class="outing-info">
                                            <strong>Out since {{ \Carbon\Carbon::parse($person->active_outing->out_time)->format('h:i A') }}</strong>
                                            @if ($person->active_outing->reason)
                                                <br>{{ $person->active_outing->reason }}
                                            @endif
                                        </div>
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="empty">No active employees found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>


        {{-- Visitor roll call --}}
        <section class="k-card roll-call">

            <div class="k-card-head">
                <div class="k-card-icon violet">@include('partials.kiosk-icon', ['name' => 'badge'])</div>
                <div>
                    <h2>Visitor Roll Call</h2>
                    <p>Visitors currently signed in and expected to be on site.</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Visitor</th>
                            <th>Company</th>
                            <th>Purpose of visit</th>
                            <th>Sign in</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($visitors as $visitor)
                            <tr>
                                <td><div class="visitor-name">{{ $visitor->name }}</div></td>
                                <td>{{ $visitor->company ?: '—' }}</td>
                                <td class="visitor-purpose">{{ $visitor->purpose }}</td>
                                <td>
                                    @if ($visitor->sign_in)
                                        {{ $visitor->sign_in->format('h:i A') }}
                                    @else
                                        <span class="muted">—</span>
                                    @endif
                                </td>
                                <td><span class="status status-visitor">On site</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty">No visitors currently on site.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </section>


        {{-- Important notice --}}
        <div class="k-alert warning warning">
            @include('partials.kiosk-icon', ['name' => 'alert'])
            <div>
                <p>
                    <strong>Important:</strong>
                    This roll call is based on the attendance and visitor records in the system.
                    During an emergency, physically verify each employee's and visitor's location.
                    Employees who have not signed in may not necessarily be absent, and visitors shown
                    above may have left without signing out.
                </p>
                <p>
                    <strong>Offline mode:</strong>
                    If this device loses its network connection, the information shown is the latest
                    information successfully synchronised before the connection was lost.
                </p>
            </div>
        </div>

    </main>


    {{-- Printable roll count sheet. Hidden on screen; used by "Roll Count PDF"
         when the server cannot be reached (it is part of the offline copy). --}}
    <div class="print-sheet" aria-hidden="true">
        @include('attendance.partials.roll-count-sheet')
    </div>


<script>

    /* ============================================================
       EMERGENCY PAGE SETTINGS

       - The page loads fresh data each time it is opened (no timed
         auto-refresh). The service worker keeps the last copy for
         offline use.
       - "Online" means the Attendance System server answered, not
         just that the device has Wi-Fi.
    ============================================================ */

    const DATA_GENERATED_AT = {{ $generatedAt->getTimestamp() * 1000 }};
    // Relative paths so the saved offline copy works whichever address
    // (IP, hostname, http or https) the tablet used to open it.
    const HEALTH_URL = '/up';
    const PDF_URL = '/attendance/emergency/pdf';
    const STALE_AFTER_MS = 60 * 1000;
    const RELOAD_GUARD_KEY = 'gfi_emergency_reloaded_at';

    let serverOnline = null;


    /* ============================================================
       SERVER CHECK
    ============================================================ */

    async function checkServer() {

        if (!navigator.onLine) {
            return false;
        }

        const controller = new AbortController();
        const timer = setTimeout(function () { controller.abort(); }, 5000);

        try {
            const response = await fetch(HEALTH_URL + '?t=' + Date.now(), {
                cache: 'no-store',
                signal: controller.signal,
            });
            return response.ok;
        } catch (error) {
            return false;
        } finally {
            clearTimeout(timer);
        }
    }


    /* ============================================================
       CONNECTION STATUS
    ============================================================ */

    function setOnlineStatus(isOnline) {

        serverOnline = isOnline;

        const status = document.getElementById('connectionStatus');
        const text = document.getElementById('connectionText');
        const banner = document.getElementById('offlineBanner');
        const mode = document.getElementById('dataMode');
        const snapshotMessage = document.getElementById('snapshotMessage');

        if (isOnline) {
            status.classList.remove('offline');
            text.textContent = 'LIVE — CONNECTED';
            banner.classList.remove('show');
            mode.textContent = 'LIVE';
            snapshotMessage.classList.remove('show');
        } else {
            status.classList.add('offline');
            text.textContent = 'OFFLINE — SAVED DATA';
            banner.classList.add('show');
            mode.textContent = 'OFFLINE (saved on this device)';
            snapshotMessage.classList.add('show');
        }
    }


    /* ============================================================
       RELOAD FOR FRESH DATA
       Guarded so a failing server can never cause a reload loop.
    ============================================================ */

    function dataIsStale() {
        return Date.now() - DATA_GENERATED_AT > STALE_AFTER_MS;
    }

    function reloadForFreshData() {

        let last = 0;

        try {
            last = parseInt(sessionStorage.getItem(RELOAD_GUARD_KEY) || '0', 10);
        } catch (error) {}

        if (Date.now() - last < 15000) {
            return;
        }

        try {
            sessionStorage.setItem(RELOAD_GUARD_KEY, String(Date.now()));
        } catch (error) {}

        window.location.reload();
    }

    async function refreshStatus(reloadIfStale) {

        const online = await checkServer();

        setOnlineStatus(online);

        if (online && reloadIfStale && dataIsStale()) {
            reloadForFreshData();
        }
    }


    /* ============================================================
       BUTTONS
    ============================================================ */

    async function manualRefresh() {

        const button = document.getElementById('refreshButton');
        button.disabled = true;

        const online = await checkServer();
        setOnlineStatus(online);

        if (online) {
            window.location.reload();
            return;
        }

        button.disabled = false;
        alert('OFFLINE MODE\n\nThe Attendance System cannot be reached.\n\nThe Emergency Roll Call information saved on this device is being displayed.');
    }

    async function downloadRollCountPdf() {

        const button = document.getElementById('pdfButton');
        button.disabled = true;

        const online = await checkServer();
        setOnlineStatus(online);
        button.disabled = false;

        if (online) {
            // Fresh PDF generated by the server (downloads; page stays open).
            window.location.href = PDF_URL;
            return;
        }

        // Offline: print the saved roll count sheet (choose "Save as PDF").
        window.print();
    }


    /* ============================================================
       EVENTS
    ============================================================ */

    // Opened again with the Back button (page restored from memory).
    window.addEventListener('pageshow', function (event) {
        if (event.persisted) {
            refreshStatus(true);
        }
    });

    // Tablet woken up / tab brought back to the front.
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            refreshStatus(true);
        }
    });

    // Connection restored: load the latest information.
    window.addEventListener('online', function () {
        setTimeout(function () { refreshStatus(true); }, 1500);
    });

    window.addEventListener('offline', function () {
        setOnlineStatus(false);
    });


    /* ============================================================
       INITIAL STATE
       If this copy came from the offline cache but the server is
       reachable again, load the latest information.
    ============================================================ */

    refreshStatus(true);


    /* ============================================================
       SERVICE WORKER REGISTRATION (offline copy)
    ============================================================ */

    if ('serviceWorker' in navigator) {

        window.addEventListener('load', function () {

            navigator.serviceWorker
                .register('/sw.js')
                .then(function (registration) {
                    registration.update();
                })
                .catch(function (error) {
                    console.error('Service worker registration failed:', error);
                });
        });
    }

</script>

</body>
</html>
