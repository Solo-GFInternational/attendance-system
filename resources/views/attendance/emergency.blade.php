<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="theme-color"
        content="#991b1b"
    >

    <meta
        name="description"
        content="Goodman Fielder Emergency Roll Call"
    >

    <link
        rel="manifest"
        href="/manifest.json"
    >

    <title>Emergency Roll Call</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }

        /* ==============================
           HEADER
        ============================== */

        .header {
            background: #991b1b;
            color: white;
            padding: 24px 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 38px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .header p {
            margin: 8px 0 0;
            font-size: 18px;
        }

        /* ==============================
           CONNECTION STATUS
        ============================== */

        .connection-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            margin-top: 15px;

            padding: 9px 18px;

            border-radius: 999px;

            font-size: 14px;
            font-weight: 800;

            background: rgba(255,255,255,0.18);
            color: white;
        }

        .connection-dot {
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #22c55e;
        }

        .connection-status.offline {
            background: #7f1d1d;
        }

        .connection-status.offline .connection-dot {
            background: #facc15;
        }

        /* ==============================
           OFFLINE BANNER
        ============================== */

        .offline-banner {
            display: none;

            background: #7f1d1d;
            color: white;

            padding: 16px 20px;

            text-align: center;

            font-size: 16px;
            font-weight: 700;
        }

        .offline-banner.show {
            display: block;
        }

        .offline-banner small {
            display: block;
            margin-top: 5px;
            font-weight: 400;
            opacity: 0.9;
        }

        /* ==============================
           CONTAINER
        ============================== */

        .container {
            max-width: 1500px;
            margin: 0 auto;
            padding: 25px;
        }

        /* ==============================
           TOP BAR
        ============================== */

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 20px;

            gap: 15px;
            flex-wrap: wrap;
        }

        .time {
            font-size: 18px;
            font-weight: 600;
        }

        .refresh-btn {
            background: #0f172a;
            color: white;

            border: none;

            padding: 12px 20px;

            border-radius: 8px;

            font-size: 16px;
            font-weight: 600;

            cursor: pointer;
        }

        .refresh-btn:hover {
            background: #1e293b;
        }

        .refresh-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ==============================
           SYNC INFORMATION
        ============================== */

        .sync-info {
            background: white;

            border-radius: 10px;

            padding: 14px 18px;

            margin-bottom: 20px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);

            color: #475569;

            font-size: 14px;
        }

        .sync-info strong {
            color: #0f172a;
        }

        /* ==============================
           SUMMARY CARDS
        ============================== */

        .cards {
            display: grid;

            grid-template-columns:
                repeat(5, 1fr);

            gap: 18px;

            margin-bottom: 25px;
        }

        .card {
            background: white;

            border-radius: 12px;

            padding: 25px;

            text-align: center;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.08);

            border-top: 6px solid;
        }

        .card .number {
            font-size: 40px;

            font-weight: 800;

            margin-bottom: 5px;
        }

        .card .label {
            font-size: 15px;

            font-weight: 700;

            text-transform: uppercase;

            color: #475569;
        }

        .total-people {
            border-color: #2563eb;
        }

        .total-people .number {
            color: #2563eb;
        }

        .in-office {
            border-color: #16a34a;
        }

        .in-office .number {
            color: #16a34a;
        }

        .out {
            border-color: #ea580c;
        }

        .out .number {
            color: #ea580c;
        }

        .visitors-card {
            border-color: #7c3aed;
        }

        .visitors-card .number {
            color: #7c3aed;
        }

        .not-signed {
            border-color: #dc2626;
        }

        .not-signed .number {
            color: #dc2626;
        }

        /* ==============================
           SECTIONS
        ============================== */

        .roll-call {
            background: white;

            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.08);

            margin-bottom: 25px;
        }

        .roll-call-header {
            padding: 20px 24px;

            border-bottom:
                1px solid #e2e8f0;
        }

        .roll-call-header h2 {
            margin: 0;

            font-size: 24px;
        }

        .roll-call-header p {
            margin: 6px 0 0;

            color: #64748b;

            font-size: 14px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;
        }

        th {
            background: #0f172a;

            color: white;

            padding: 15px;

            text-align: left;

            font-size: 14px;

            text-transform: uppercase;

            white-space: nowrap;
        }

        td {
            padding: 15px;

            border-bottom:
                1px solid #e2e8f0;

            font-size: 15px;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .employee-name,
        .visitor-name {
            font-weight: 700;
        }

        .employee-id {
            color: #64748b;

            font-size: 13px;

            margin-top: 3px;
        }

        /* ==============================
           STATUS BADGES
        ============================== */

        .status {
            display: inline-block;

            padding: 8px 13px;

            border-radius: 999px;

            font-weight: 700;

            font-size: 13px;

            white-space: nowrap;
        }

        .status-in {
            background: #dcfce7;

            color: #166534;
        }

        .status-out {
            background: #ffedd5;

            color: #9a3412;
        }

        .status-not {
            background: #fee2e2;

            color: #991b1b;
        }

        .status-checkout {
            background: #e2e8f0;

            color: #475569;
        }

        .status-visitor {
            background: #ede9fe;

            color: #6d28d9;
        }

        .outing-info {
            font-size: 13px;

            color: #9a3412;

            line-height: 1.5;
        }

        .empty {
            text-align: center;

            padding: 40px;

            color: #64748b;
        }

        /* ==============================
           WARNING
        ============================== */

        .warning {
            margin-top: 20px;

            background: #fff7ed;

            border: 1px solid #fed7aa;

            color: #9a3412;

            padding: 15px 18px;

            border-radius: 8px;

            font-size: 14px;
        }

        .visitor-purpose {
            max-width: 350px;
        }

        /* ==============================
           OFFLINE SNAPSHOT MESSAGE
        ============================== */

        .snapshot-message {
            display: none;

            background: #fef3c7;

            border: 1px solid #f59e0b;

            color: #92400e;

            padding: 12px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;
        }

        .snapshot-message.show {
            display: block;
        }

        /* ==============================
           MOBILE
        ============================== */

        @media (max-width: 1200px) {

            .cards {
                grid-template-columns:
                    repeat(3, 1fr);
            }

        }

        @media (max-width: 800px) {

            .cards {
                grid-template-columns:
                    repeat(2, 1fr);
            }

        }

        @media (max-width: 600px) {

            .cards {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 28px;
            }

            .container {
                padding: 15px;
            }

            .card .number {
                font-size: 36px;
            }

            .time {
                font-size: 15px;
            }

        }

    </style>

</head>

<body>

<!-- =========================================
     HEADER
========================================= -->

<div class="header">

    <h1>🚨 EMERGENCY ROLL CALL</h1>

    @if($selectedSite)

        <p>
            {{ $selectedSite->name }}
            &nbsp; | &nbsp;
            Employee &amp; Visitor Accountability &amp; Evacuation Status
        </p>

    @else

        <p>
            ALL SITES
            &nbsp; | &nbsp;
            Employee &amp; Visitor Accountability &amp; Evacuation Status
        </p>

    @endif

    <div
        id="connectionStatus"
        class="connection-status"
    >

        <span
            id="connectionDot"
            class="connection-dot"
        ></span>

        <span id="connectionText">
            CHECKING CONNECTION...
        </span>

    </div>

</div>


<!-- =========================================
     OFFLINE BANNER
========================================= -->

<div
    id="offlineBanner"
    class="offline-banner"
>

    🔴 OFFLINE EMERGENCY MODE

    <small>
        No connection to the Attendance System.
        Showing the latest information saved on this device.
    </small>

</div>


<div class="container">


    <!-- =====================================
         TOP BAR
    ====================================== -->

    <div class="top-bar">

        <div class="time">

            📅 {{ now()->format('l, d F Y') }}

            &nbsp; | &nbsp;

            🕐

            <span id="currentTime">

                {{ now()->format('h:i:s A') }}

            </span>

        </div>


        <button
            id="refreshButton"
            class="refresh-btn"
            onclick="manualRefresh()"
        >

            🔄 Refresh Now

        </button>

    </div>


    <!-- =====================================
         LAST SYNC INFORMATION
    ====================================== -->

    <div class="sync-info">

        <strong>
            Last Information Update:
        </strong>

        <span id="lastSync">
            Checking...
        </span>

        &nbsp;&nbsp;|&nbsp;&nbsp;

        <strong>
            Data Mode:
        </strong>

        <span id="dataMode">
            LIVE
        </span>

    </div>


    <!-- =====================================
         OFFLINE SNAPSHOT MESSAGE
    ====================================== -->

    <div
        id="snapshotMessage"
        class="snapshot-message"
    >

        💾 The latest Emergency Roll Call information was saved on
        this device before the network connection was lost.

    </div>


    <!-- =====================================
         SUMMARY CARDS
    ===================================== -->

    <div class="cards">


        <div class="card total-people">

            <div class="number">
                {{ $peopleOnSite }}
            </div>

            <div class="label">
                People On Site
            </div>

        </div>


        <div class="card in-office">

            <div class="number">
                {{ $inOffice }}
            </div>

            <div class="label">
                Employees In Office
            </div>

        </div>


        <div class="card out">

            <div class="number">
                {{ $currentlyOut }}
            </div>

            <div class="label">
                Employees Currently Out
            </div>

        </div>


        <div class="card visitors-card">

            <div class="number">
                {{ $visitorsOnSite }}
            </div>

            <div class="label">
                Visitors On Site
            </div>

        </div>


        <div class="card not-signed">

            <div class="number">
                {{ $notSignedIn }}
            </div>

            <div class="label">
                Employees Not Signed In
            </div>

        </div>

    </div>


    <!-- =====================================
         EMPLOYEE ROLL CALL
    ====================================== -->

    <div class="roll-call">

        <div class="roll-call-header">

            <h2>
                👨‍💼 Employee Roll Call
            </h2>

            <p>
                Current employee attendance and location status.
            </p>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            Employee
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Check In
                        </th>

                        <th>
                            Check Out
                        </th>

                        <th>
                            Outing Information
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($rollCall as $person)

                    <tr>

                        <td>

                            <div class="employee-name">

                                {{ $person->employee->name }}

                            </div>

                            <div class="employee-id">

                                ID:
                                {{ $person->employee->employee_code }}

                            </div>

                        </td>


                        <td>

                            {{ $person->employee->department ?: '—' }}

                        </td>


                        <td>

                            @if($person->status === 'in_office')

                                <span
                                    class="status status-in"
                                >
                                    ✓ IN OFFICE
                                </span>

                            @elseif($person->status === 'out')

                                <span
                                    class="status status-out"
                                >
                                    ⚠ CURRENTLY OUT
                                </span>

                            @elseif($person->status === 'not_signed_in')

                                <span
                                    class="status status-not"
                                >
                                    ✕ NOT SIGNED IN
                                </span>

                            @elseif($person->status === 'checked_out')

                                <span
                                    class="status status-checkout"
                                >
                                    ✓ CHECKED OUT
                                </span>

                            @endif

                        </td>


                        <td>

                            @if($person->check_in)

                                {{ \Carbon\Carbon::parse($person->check_in)->format('h:i A') }}

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if($person->check_out)

                                {{ \Carbon\Carbon::parse($person->check_out)->format('h:i A') }}

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            @if($person->active_outing)

                                <div class="outing-info">

                                    <strong>
                                        Currently Out
                                    </strong>

                                    <br>

                                    Out:

                                    {{ \Carbon\Carbon::parse($person->active_outing->out_time)->format('h:i A') }}

                                    @if($person->active_outing->reason)

                                        <br>

                                        Reason:

                                        {{ $person->active_outing->reason }}

                                    @endif

                                </div>

                            @else

                                —

                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="6"
                            class="empty"
                        >

                            No active employees found.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- =====================================
         VISITOR ROLL CALL
    ====================================== -->

    <div class="roll-call">

        <div class="roll-call-header">

            <h2>
                👥 Visitor Roll Call
            </h2>

            <p>
                Visitors currently signed in and physically expected
                to be on site.
            </p>

        </div>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            Visitor
                        </th>

                        <th>
                            Company
                        </th>

                        <th>
                            Purpose of Visit
                        </th>

                        <th>
                            Sign In
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($visitors as $visitor)

                    <tr>

                        <td>

                            <div class="visitor-name">

                                {{ $visitor->name }}

                            </div>

                        </td>


                        <td>

                            {{ $visitor->company ?: '—' }}

                        </td>


                        <td class="visitor-purpose">

                            {{ $visitor->purpose }}

                        </td>


                        <td>

                            @if($visitor->sign_in)

                                {{ $visitor->sign_in->format('h:i A') }}

                            @else

                                —

                            @endif

                        </td>


                        <td>

                            <span
                                class="status status-visitor"
                            >

                                ✓ VISITOR ON SITE

                            </span>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="5"
                            class="empty"
                        >

                            <div style="font-size: 40px;">
                                👤
                            </div>

                            No visitors currently on site.

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


    <!-- =====================================
         WARNING
    ====================================== -->

    <div class="warning">

        <strong>
            Important:
        </strong>

        This roll call is based on the attendance and visitor records
        in the system.

        During an emergency, physically verify each employee and visitor's
        location.

        Employees who have not signed in may not necessarily be absent.

        Visitors shown above have signed in but may have already left the
        premises without signing out.

        <br><br>

        <strong>
            Offline Mode:
        </strong>

        If this device is disconnected from the network, the information
        displayed is the latest information successfully synchronized
        before the connection was lost.

    </div>


</div>


<script>

    /* ============================================================
       EMERGENCY PAGE SETTINGS
    ============================================================ */

    const EMERGENCY_STORAGE_KEY =
        'gfi_emergency_last_sync';

    const EMERGENCY_CACHE_NAME =
        'gfi-emergency-v4';

    const EMERGENCY_URL =
        '/attendance/emergency';


    /* ============================================================
       CURRENT TIME
    ============================================================ */

    function updateClock() {

        const now = new Date();

        let hours = now.getHours();

        let minutes = now.getMinutes();

        let seconds = now.getSeconds();

        const ampm =
            hours >= 12
                ? 'PM'
                : 'AM';

        hours = hours % 12;

        if (hours === 0) {
            hours = 12;
        }

        hours =
            String(hours).padStart(2, '0');

        minutes =
            String(minutes).padStart(2, '0');

        seconds =
            String(seconds).padStart(2, '0');

        const clock =
            document.getElementById('currentTime');

        if (clock) {

            clock.textContent =
                `${hours}:${minutes}:${seconds} ${ampm}`;

        }

    }

    updateClock();

    setInterval(
        updateClock,
        1000
    );


    /* ============================================================
       CONNECTION STATUS
    ============================================================ */

    function setOnlineStatus(isOnline) {

        const status =
            document.getElementById('connectionStatus');

        const text =
            document.getElementById('connectionText');

        const banner =
            document.getElementById('offlineBanner');

        const mode =
            document.getElementById('dataMode');

        const refreshButton =
            document.getElementById('refreshButton');

        const snapshotMessage =
            document.getElementById('snapshotMessage');


        if (isOnline) {

            status.classList.remove('offline');

            text.textContent =
                'LIVE — CONNECTED';

            banner.classList.remove('show');

            mode.textContent =
                'LIVE';

            refreshButton.disabled =
                false;

            snapshotMessage.classList.remove('show');

        } else {

            status.classList.add('offline');

            text.textContent =
                'OFFLINE — SAVED DATA';

            banner.classList.add('show');

            mode.textContent =
                'OFFLINE';

            refreshButton.disabled =
                true;

            snapshotMessage.classList.add('show');

        }

    }


    /* ============================================================
       LAST SYNC
    ============================================================ */

    function updateLastSync() {

        const lastSync =
            localStorage.getItem(
                EMERGENCY_STORAGE_KEY
            );

        const element =
            document.getElementById('lastSync');


        if (!element) {
            return;
        }


        if (lastSync) {

            const date =
                new Date(lastSync);

            if (!isNaN(date.getTime())) {

                element.textContent =
                    date.toLocaleString();

            } else {

                element.textContent =
                    'Saved information available';

            }

        } else {

            element.textContent =
                'No previous synchronization recorded';

        }

    }


    /* ============================================================
       SAVE SUCCESSFUL ONLINE SYNCHRONIZATION
    ============================================================ */

    function recordSync() {

        try {

            localStorage.setItem(
                EMERGENCY_STORAGE_KEY,
                new Date().toISOString()
            );

        } catch (error) {

            console.error(
                'Unable to save synchronization time:',
                error
            );

        }

        updateLastSync();

    }


    /* ============================================================
       MANUAL REFRESH
    ============================================================ */

    function manualRefresh() {

        if (!navigator.onLine) {

            alert(
                'OFFLINE MODE\n\nThe network connection is unavailable.\n\nThe latest Emergency Roll Call information saved on this device is being displayed.'
            );

            return;

        }

        window.location.reload();

    }


    /* ============================================================
       NETWORK ONLINE
    ============================================================ */

    window.addEventListener(
        'online',
        function () {

            setOnlineStatus(true);

            /*
             * Give the network a moment to reconnect.
             */
            setTimeout(
                function () {

                    window.location.reload();

                },
                1500
            );

        }
    );


    /* ============================================================
       NETWORK OFFLINE
    ============================================================ */

    window.addEventListener(
        'offline',
        function () {

            setOnlineStatus(false);

        }
    );


    /* ============================================================
       INITIAL CONNECTION STATUS
    ============================================================ */

    setOnlineStatus(
        navigator.onLine
    );

    updateLastSync();


    /* ============================================================
       RECORD ONLINE LOAD
    ============================================================ */

    if (navigator.onLine) {

        recordSync();

    }


    /* ============================================================
       SERVICE WORKER REGISTRATION
    ============================================================ */

    if ('serviceWorker' in navigator) {

        window.addEventListener(
            'load',
            function () {

                navigator.serviceWorker
    .register('/sw.js')
                    .then(
                        function (registration) {

                            console.log(
                                'GFI Emergency Service Worker registered.',
                                registration.scope
                            );

                            /*
                             * Check for a newer service worker.
                             */
                            registration.update();

                        }
                    )
                    .catch(
                        function (error) {

                            console.error(
                                'Service worker registration failed:',
                                error
                            );

                        }
                    );

            }
        );

    }


    /* ============================================================
       ONLINE AUTO REFRESH
       
       Every 10 seconds while connected.
       
       IMPORTANT:
       It does NOT refresh while offline.
       ============================================================ */

    setInterval(
        function () {

            if (navigator.onLine) {

                window.location.reload();

            }

        },
        10000
    );


    /* ============================================================
       PAGE VISIBILITY
       
       When tablet screen wakes up, check connection again.
       ============================================================ */

    document.addEventListener(
        'visibilitychange',
        function () {

            if (!document.hidden) {

                setOnlineStatus(
                    navigator.onLine
                );

                updateLastSync();

            }

        }
    );

</script>

</body>
</html>