<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token"
          content="{{ csrf_token() }}">

    <title>Attendance Dashboard</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <style>

        body {
            background: #f5f7fa;
        }


        /* =========================================================
           ADMIN NAVIGATION
        ========================================================= */

        .admin-navbar {
            background: #ffffff;
            border-radius: 14px;
            padding: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .admin-navbar .nav-link {
            color: #495057;
            font-weight: 600;
            border-radius: 10px;
            padding: 10px 14px;
            transition: .2s ease;
            text-decoration: none;
        }

        .admin-navbar .nav-link:hover {
            background: #f1f3f5;
            color: #0d6efd;
        }

        .admin-navbar .nav-link.active {
            background: #0d6efd;
            color: white;
        }

        .admin-navbar .nav-link.logout {
            color: #dc3545;
        }

        .admin-navbar .nav-link.logout:hover {
            background: #fff0f0;
            color: #dc3545;
        }


        /* =========================================================
           DASHBOARD
        ========================================================= */

        .dashboard-card {
            border: 0;
            border-radius: 14px;
            transition:
                transform .2s ease,
                box-shadow .2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow:
                0 8px 25px rgba(0, 0, 0, .08) !important;
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .page-header {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .attendance-table th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #6c757d;
            white-space: nowrap;
        }

        .attendance-table td {
            white-space: nowrap;
        }

        .employee-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #495057;
        }

        .date-badge {
            background: #212529;
            color: white;
            border-radius: 8px;
            padding: 8px 14px;
            display: inline-block;
        }


        /* =========================================================
           PHOTO BUTTON
        ========================================================= */

        .photo-button {
            border-radius: 8px;
            font-weight: 600;
            white-space: nowrap;
        }


        /* =========================================================
           PHOTO MODAL
        ========================================================= */

        .photo-modal-image {
            max-width: 100%;
            max-height: 70vh;
            object-fit: contain;
            border-radius: 12px;
        }


        /* =========================================================
           FLOATING AI CHAT BUTTON
        ========================================================= */

        .ai-chat-button {
            position: fixed;
            right: 25px;
            bottom: 25px;

            width: 62px;
            height: 62px;

            border: none;
            border-radius: 50%;

            background: #212529;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 28px;

            cursor: pointer;

            box-shadow:
                0 8px 25px rgba(0, 0, 0, .20);

            z-index: 1050;

            transition:
                transform .2s ease,
                box-shadow .2s ease,
                background .2s ease;
        }

        .ai-chat-button:hover {
            transform: scale(1.08);
            background: #0d6efd;

            box-shadow:
                0 10px 30px rgba(13, 110, 253, .30);
        }

        .ai-chat-button:active {
            transform: scale(.96);
        }


        /* =========================================================
           AI CHAT BUTTON ICONS
        ========================================================= */

        .ai-chat-open-icon {
            display: block;
        }

        .ai-chat-close-icon {
            display: none;
            font-size: 25px;
        }

        .ai-chat-button.open .ai-chat-open-icon {
            display: none;
        }

        .ai-chat-button.open .ai-chat-close-icon {
            display: block;
        }


        /* =========================================================
           AI CHAT WINDOW
        ========================================================= */

        .ai-chat-window {
            position: fixed;

            right: 25px;
            bottom: 100px;

            width: 390px;
            max-width: calc(100vw - 30px);

            height: 570px;
            max-height: calc(100vh - 130px);

            background: white;

            border-radius: 18px;

            box-shadow:
                0 15px 50px rgba(0, 0, 0, .20);

            overflow: hidden;

            z-index: 1049;

            display: flex;
            flex-direction: column;

            opacity: 0;
            visibility: hidden;

            transform:
                translateY(20px)
                scale(.96);

            transform-origin:
                bottom right;

            transition:
                opacity .2s ease,
                visibility .2s ease,
                transform .2s ease;
        }

        .ai-chat-window.open {
            opacity: 1;
            visibility: visible;

            transform:
                translateY(0)
                scale(1);
        }


        /* =========================================================
           AI CHAT HEADER
        ========================================================= */

        .ai-chat-header {
            background: #212529;
            color: white;

            padding: 16px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            flex-shrink: 0;
        }

        .ai-chat-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ai-chat-avatar {
            width: 42px;
            height: 42px;

            border-radius: 50%;

            background: rgba(255, 255, 255, .12);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 21px;
        }

        .ai-chat-title {
            font-weight: 700;
            font-size: 15px;
            margin: 0;
        }

        .ai-chat-subtitle {
            font-size: 11px;
            color: rgba(255, 255, 255, .65);
            margin-top: 2px;
        }

        .ai-chat-online {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .ai-chat-online-dot {
            width: 7px;
            height: 7px;

            border-radius: 50%;

            background: #20c997;
        }


        /* =========================================================
           CHAT MESSAGES AREA
        ========================================================= */

        .ai-chat-messages {
            flex: 1;

            padding: 18px;

            overflow-y: auto;

            background: #f8f9fa;

            scroll-behavior: smooth;
        }


        /* =========================================================
           WELCOME MESSAGE
        ========================================================= */

        .ai-welcome-message {
            display: flex;
            align-items: flex-start;
            gap: 9px;

            margin-bottom: 16px;
        }

        .ai-message-avatar {
            width: 32px;
            height: 32px;

            min-width: 32px;

            border-radius: 50%;

            background: #212529;
            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 16px;
        }

        .ai-message-bubble {
            background: white;

            border: 1px solid #e9ecef;

            border-radius: 5px 14px 14px 14px;

            padding: 11px 13px;

            font-size: 13px;

            line-height: 1.55;

            color: #343a40;

            box-shadow:
                0 2px 5px rgba(0, 0, 0, .03);

            max-width: 85%;
        }


        /* =========================================================
           USER MESSAGE
        ========================================================= */

        .ai-user-message {
            display: flex;
            justify-content: flex-end;

            margin-bottom: 16px;
        }

        .ai-user-bubble {
            background: #0d6efd;
            color: white;

            border-radius: 14px 5px 14px 14px;

            padding: 11px 13px;

            font-size: 13px;

            line-height: 1.55;

            max-width: 85%;

            white-space: pre-wrap;

            box-shadow:
                0 2px 5px rgba(0, 0, 0, .08);
        }


        /* =========================================================
           AI RESPONSE
        ========================================================= */

        .ai-response-message {
            display: flex;
            align-items: flex-start;
            gap: 9px;

            margin-bottom: 16px;
        }

        .ai-response-bubble {
            background: white;

            border: 1px solid #e9ecef;

            border-radius: 5px 14px 14px 14px;

            padding: 11px 13px;

            font-size: 13px;

            line-height: 1.6;

            color: #343a40;

            max-width: 88%;

            white-space: pre-wrap;

            box-shadow:
                0 2px 5px rgba(0, 0, 0, .03);
        }


        /* =========================================================
           TYPING INDICATOR
        ========================================================= */

        .ai-typing {
            display: flex;
            align-items: center;
            gap: 9px;

            margin-bottom: 16px;
        }

        .ai-typing-bubble {
            background: white;

            border: 1px solid #e9ecef;

            border-radius: 5px 14px 14px 14px;

            padding: 12px 15px;
        }

        .ai-typing-dots {
            display: flex;
            gap: 4px;
        }

        .ai-typing-dots span {
            width: 6px;
            height: 6px;

            background: #adb5bd;

            border-radius: 50%;

            animation: aiTyping 1.2s infinite;
        }

        .ai-typing-dots span:nth-child(2) {
            animation-delay: .15s;
        }

        .ai-typing-dots span:nth-child(3) {
            animation-delay: .30s;
        }

        @keyframes aiTyping {

            0%,
            60%,
            100% {
                opacity: .3;
                transform: translateY(0);
            }

            30% {
                opacity: 1;
                transform: translateY(-3px);
            }
        }


        /* =========================================================
           SUGGESTIONS
        ========================================================= */

        .ai-chat-suggestions {
            padding: 0 15px 10px;

            background: #f8f9fa;

            flex-shrink: 0;
        }

        .ai-chat-suggestions-title {
            font-size: 11px;
            color: #6c757d;

            margin-bottom: 7px;
        }

        .ai-chat-suggestion {
            border-radius: 20px;

            font-size: 11px;

            padding: 5px 9px;

            margin-right: 4px;
            margin-bottom: 4px;
        }


        /* =========================================================
           CHAT INPUT AREA
        ========================================================= */

        .ai-chat-input-area {
            background: white;

            border-top: 1px solid #e9ecef;

            padding: 12px;

            flex-shrink: 0;
        }

        .ai-chat-form {
            display: flex;
            gap: 7px;
        }

        .ai-chat-input {
            flex: 1;

            min-width: 0;

            border-radius: 12px;

            border: 1px solid #dee2e6;

            padding: 10px 12px;

            font-size: 13px;

            outline: none;

            transition:
                border-color .2s ease,
                box-shadow .2s ease;
        }

        .ai-chat-input:focus {
            border-color: #86b7fe;

            box-shadow:
                0 0 0 .15rem rgba(13, 110, 253, .10);
        }

        .ai-chat-send {
            width: 43px;
            height: 43px;

            border: none;

            border-radius: 12px;

            background: #0d6efd;

            color: white;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 18px;

            flex-shrink: 0;

            transition:
                background .2s ease,
                transform .2s ease;
        }

        .ai-chat-send:hover {
            background: #0b5ed7;
        }

        .ai-chat-send:active {
            transform: scale(.95);
        }

        .ai-chat-send:disabled {
            opacity: .65;
            cursor: not-allowed;
        }

        .ai-chat-disclaimer {
            text-align: center;

            font-size: 10px;

            color: #adb5bd;

            margin-top: 7px;
        }


        /* =========================================================
           MOBILE CHAT
        ========================================================= */

        @media (max-width: 575.98px) {

            .ai-chat-button {
                right: 15px;
                bottom: 15px;

                width: 56px;
                height: 56px;

                font-size: 25px;
            }

            .ai-chat-window {
                right: 10px;
                bottom: 82px;

                width: calc(100vw - 20px);

                height: calc(100vh - 105px);

                max-height: none;

                border-radius: 16px;
            }

        }

    </style>

</head>


<body>


<div class="container-fluid py-4 px-4">


    <!-- =========================================================
         ADMIN NAVIGATION
    ========================================================= -->

    <div class="admin-navbar mb-4">

        <div class="d-flex flex-wrap align-items-center gap-2">


            <!-- DASHBOARD -->

            <a href="{{ route('dashboard') }}"
               class="nav-link active">

                📊 Dashboard

            </a>


            <!-- ATTENDANCE -->

            <a href="{{ route('attendance.admin') }}"
               class="nav-link">

                🕘 Attendance

            </a>


            <!-- HISTORY -->

            <a href="{{ route('attendance.history') }}"
               class="nav-link">

                📋 History

            </a>


            <!-- OUTINGS -->

            <a href="{{ route('attendance.outings') }}"
               class="nav-link">

                🚶 Outings

            </a>


            <!-- EMPLOYEES -->

            <a href="{{ route('employees.index') }}"
               class="nav-link">

                👥 Employees

            </a>


            <!-- SITES -->

            <a href="{{ route('sites.index') }}"
               class="nav-link">

                🏢 Sites

            </a>


            <!-- VISITORS -->

            <a href="{{ route('visitors.index') }}"
               class="nav-link">

                🚪 Visitors

            </a>


            <!-- EMERGENCY -->

            <a href="{{ route('attendance.emergency') }}"
               class="nav-link">

                🚨 Emergency

            </a>


            <!-- ADMIN USERS -->

            @if(auth()->user()?->is_admin)
    <a href="{{ route('admin-users.index') }}" class="nav-link">
        👤 Admin Users
    </a>
@endif


            <!-- PROFILE -->

            <div class="ms-lg-auto">

                <a href="{{ route('profile.edit') }}"
                   class="nav-link">

                    ⚙️ Profile

                </a>

            </div>


        </div>

    </div>


    <!-- =========================================================
         HEADER
    ========================================================= -->

    <div class="page-header mb-4">

        <div class="d-flex flex-column flex-lg-row
                    justify-content-between
                    align-items-lg-center
                    gap-3">

            <div>

                <h2 class="fw-bold mb-1">

                    Attendance Dashboard

                </h2>

                <p class="text-muted mb-2">

                    Monitor today's employee attendance

                </p>

                <span class="date-badge">

                    {{ now()->format('l, d F Y') }}

                </span>

            </div>

        </div>

    </div>


    <!-- =========================================================
         SUMMARY CARDS
    ========================================================= -->

    <div class="row g-4 mb-4">


        <!-- TOTAL STAFF -->

        <div class="col-sm-6 col-xl-3">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Total Staff

                            </p>

                            <h2 class="fw-bold mb-0">

                                {{ $totalStaff }}

                            </h2>

                        </div>

                        <div class="stat-icon bg-primary-subtle">

                            👥

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- PRESENT -->

        <div class="col-sm-6 col-xl-3">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Present

                            </p>

                            <h2 class="fw-bold text-success mb-0">

                                {{ $present }}

                            </h2>

                        </div>

                        <div class="stat-icon bg-success-subtle">

                            ✓

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- LATE -->

        <div class="col-sm-6 col-xl-3">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Late

                            </p>

                            <h2 class="fw-bold text-warning mb-0">

                                {{ $late }}

                            </h2>

                        </div>

                        <div class="stat-icon bg-warning-subtle">

                            ⏰

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- ABSENT -->

        <div class="col-sm-6 col-xl-3">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Absent

                            </p>

                            <h2 class="fw-bold text-danger mb-0">

                                {{ $absent }}

                            </h2>

                        </div>

                        <div class="stat-icon bg-danger-subtle">

                            !

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- =========================================================
         CURRENTLY OUT / CHECKED OUT
    ========================================================= -->

    <div class="row g-4 mb-4">


        <!-- CURRENTLY OUT -->

        <div class="col-md-6">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Currently Out

                            </p>

                            <h2 class="fw-bold text-warning mb-0">

                                {{ $currentlyOut }}

                            </h2>

                            <small class="text-muted">

                                Employees currently outside

                            </small>

                        </div>

                        <div class="stat-icon bg-warning-subtle">

                            🚶

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- CHECKED OUT -->

        <div class="col-md-6">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">

                        <div>

                            <p class="text-muted mb-1">

                                Checked Out

                            </p>

                            <h2 class="fw-bold text-secondary mb-0">

                                {{ $checkedOut }}

                            </h2>

                            <small class="text-muted">

                                Employees who completed their day

                            </small>

                        </div>

                        <div class="stat-icon bg-secondary-subtle">

                            ✓

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>


    <!-- =========================================================
         SITE FILTER
    ========================================================= -->

    <div class="card border-0 shadow-sm mb-4"
         style="border-radius:14px;">

        <div class="card-body p-4">

            <div class="row g-3 align-items-end">

                <div class="col-md-5 col-lg-4">

                    <label
                        for="site_id"
                        class="form-label fw-semibold"
                    >

                        Site

                    </label>

                    <form
                        method="GET"
                        action="{{ route('attendance.admin') }}"
                    >

                        <select
                            name="site_id"
                            id="site_id"
                            class="form-select"
                            onchange="this.form.submit()"
                        >

                            <option value="">

                                All Sites

                            </option>

                            @foreach($sites as $site)

                                <option
                                    value="{{ $site->id }}"
                                    {{ (string) $selectedSiteId == (string) $site->id ? 'selected' : '' }}
                                >

                                    {{ $site->name }}

                                </option>

                            @endforeach

                        </select>

                    </form>

                </div>

            </div>

        </div>

    </div>


    <!-- =========================================================
         ATTENDANCE TABLE
    ========================================================= -->

    <div class="card border-0 shadow-sm"
         style="border-radius:14px; overflow:hidden;">


        <!-- TABLE HEADER -->

        <div class="card-body p-4 border-bottom">

            <div class="d-flex
                        flex-column flex-md-row
                        justify-content-between
                        align-items-md-center
                        gap-2">

                <div>

                    <h5 class="fw-bold mb-1">

                        Today's Attendance

                    </h5>

                    <p class="text-muted mb-0">

                        Attendance status for all active employees

                    </p>

                </div>

                <div>

                    <span
                        class="badge bg-light text-dark border px-3 py-2"
                    >

                        {{ $totalStaff }} Employees

                    </span>

                </div>

            </div>

        </div>


        <!-- TABLE -->

        <div class="table-responsive">

            <table
                class="table table-hover
                       align-middle
                       mb-0
                       attendance-table"
            >

                <thead class="table-light">

                    <tr>

                        <th class="px-4 py-3">

                            Employee

                        </th>

                        <th>

                            Employee ID

                        </th>

                        <th>

                            Department

                        </th>

                        <th>

                            Date

                        </th>

                        <th>

                            Check In

                        </th>

                        <th>

                            Check Out

                        </th>

                        <th>

                            Status

                        </th>

                        

                        <th>

                            Remarks

                        </th>

                    </tr>

                </thead>


                <tbody>

                @forelse($attendance as $record)

                    <tr>


                        <!-- EMPLOYEE -->

                        <td class="px-4">

                            <div class="d-flex
                                        align-items-center
                                        gap-3">

                                <div class="employee-avatar">

                                    {{ strtoupper(
                                        substr(
                                            $record->employee->name,
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>

                                <div>

                                    <div class="fw-semibold">

                                        {{ $record->employee->name }}

                                    </div>

                                    @if($record->employee->designation)

                                        <small class="text-muted">

                                            {{ $record->employee->designation }}

                                        </small>

                                    @endif

                                </div>

                            </div>

                        </td>


                        <!-- EMPLOYEE ID -->

                        <td>

                            <span class="fw-semibold">

                                {{ $record->employee->employee_code }}

                            </span>

                        </td>


                        <!-- DEPARTMENT -->

                        <td>

                            {{ $record->employee->department ?: '—' }}

                        </td>


                        <!-- DATE -->

                        <td>

                            {{ \Carbon\Carbon::parse(
                                $record->attendance_date
                            )->format('d/m/Y') }}

                        </td>


                        <!-- CHECK IN -->

                        <td>

                            @if($record->check_in)

                                <span class="fw-semibold text-success">

                                    {{ \Carbon\Carbon::parse(
                                        $record->check_in
                                    )->format('h:i A') }}

                                </span>

                            @else

                                <span class="text-muted">

                                    —

                                </span>

                            @endif

                        </td>


                        <!-- CHECK OUT -->

                        <td>

                            @if($record->check_out)

                                <span class="fw-semibold">

                                    {{ \Carbon\Carbon::parse(
                                        $record->check_out
                                    )->format('h:i A') }}

                                </span>

                            @else

                                <span class="text-muted">

                                    —

                                </span>

                            @endif

                        </td>


                        <!-- STATUS -->

                        <td>

                            @if($record->status === 'Present')

                                <span
                                    class="badge
                                           rounded-pill
                                           bg-success-subtle
                                           text-success
                                           border
                                           border-success-subtle
                                           px-3 py-2"
                                >

                                    ✓ Present

                                </span>


                            @elseif($record->status === 'Late')

                                <span
                                    class="badge
                                           rounded-pill
                                           bg-warning-subtle
                                           text-warning-emphasis
                                           border
                                           border-warning-subtle
                                           px-3 py-2"
                                >

                                    ⏰ Late

                                </span>


                            @elseif($record->status === 'Leave')

                                <span
                                    class="badge
                                           rounded-pill
                                           bg-info-subtle
                                           text-info-emphasis
                                           border
                                           border-info-subtle
                                           px-3 py-2"
                                >

                                    Leave

                                </span>


                            @else

                                <span
                                    class="badge
                                           rounded-pill
                                           bg-danger-subtle
                                           text-danger
                                           border
                                           border-danger-subtle
                                           px-3 py-2"
                                >

                                    ✕ Absent

                                </span>

                            @endif

                        </td>


                       


                        <!-- REMARKS -->

                        <td>

                            {{ $record->remarks ?: '—' }}

                        </td>


                    </tr>

                @empty

                    <tr>

                        <td
                            colspan="9"
                            class="text-center py-5"
                        >

                            <div class="text-muted">

                                <div class="fs-1 mb-2">

                                    👥

                                </div>

                                <div class="fw-semibold">

                                    No employees found

                                </div>

                                <small>

                                    Add employees from Employee Management.

                                </small>

                            </div>

                        </td>

                    </tr>

                @endforelse

                </tbody>

            </table>

        </div>

    </div>


</div>


<!-- =========================================================
     PHOTO MODAL
========================================================= -->

<div
    class="modal fade"
    id="photoModal"
    tabindex="-1"
    aria-labelledby="photoModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-dialog-centered modal-lg"
    >

        <div
            class="modal-content border-0 shadow"
            style="border-radius:16px; overflow:hidden;"
        >


            <!-- MODAL HEADER -->

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title fw-bold"
                        id="photoModalLabel"
                    >

                        📷 Sign-In Photo

                    </h5>

                    <small
                        class="text-muted"
                        id="photoEmployee"
                    ></small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <!-- MODAL BODY -->

            <div class="modal-body text-center p-4">

                <img
                    id="attendancePhoto"
                    src=""
                    alt="Employee sign-in photo"
                    class="photo-modal-image shadow-sm"
                >

            </div>


            <!-- MODAL FOOTER -->

            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >

                    Close

                </button>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     AI CHAT WINDOW
========================================================= -->

<div
    id="aiChatWindow"
    class="ai-chat-window"
    aria-hidden="true"
>


    <!-- =====================================================
         CHAT HEADER
    ===================================================== -->

    <div class="ai-chat-header">


        <div class="ai-chat-header-left">

            <div class="ai-chat-avatar">

                🤖

            </div>

            <div>

                <div class="ai-chat-title">

                    Attendance Assistant

                </div>

                <div class="ai-chat-subtitle">

                    <span class="ai-chat-online">

                        <span class="ai-chat-online-dot"></span>

                        Read-only attendance assistant

                    </span>

                </div>

            </div>

        </div>


    </div>


    <!-- =====================================================
         CHAT MESSAGES
    ===================================================== -->

    <div
        id="aiChatMessages"
        class="ai-chat-messages"
    >


        <!-- WELCOME MESSAGE -->

        <div class="ai-welcome-message">

            <div class="ai-message-avatar">

                🤖

            </div>

            <div class="ai-message-bubble">

                Hello! 👋<br><br>

                I can help you check employee attendance,
                late arrivals, absences and outings.

                <br><br>

                Try asking:

                <br>

                <strong>“Who is currently out?”</strong>

            </div>

        </div>


    </div>


    <!-- =====================================================
         SUGGESTIONS
    ===================================================== -->

    <div class="ai-chat-suggestions">

        <div class="ai-chat-suggestions-title">

            Quick questions

        </div>


        <button
            type="button"
            class="btn btn-sm btn-outline-secondary ai-chat-suggestion"
            data-question="Who is currently out?"
        >

            🚶 Currently out

        </button>


        <button
            type="button"
            class="btn btn-sm btn-outline-secondary ai-chat-suggestion"
            data-question="How many employees are late today?"
        >

            ⏰ Late today

        </button>


        <button
            type="button"
            class="btn btn-sm btn-outline-secondary ai-chat-suggestion"
            data-question="Who hasn't signed in today?"
        >

            ❌ Not signed in

        </button>


        <button
            type="button"
            class="btn btn-sm btn-outline-secondary ai-chat-suggestion"
            data-question="How long was Shahil out in September?"
        >

            🚶 Outing duration

        </button>

    </div>


    <!-- =====================================================
         CHAT INPUT
    ===================================================== -->

    <div class="ai-chat-input-area">


        <form
            id="aiChatForm"
            class="ai-chat-form"
        >


            <input
                type="text"
                id="aiChatInput"
                class="ai-chat-input"
                placeholder="Ask about attendance..."
                autocomplete="off"
                maxlength="1000"
            >


            <button
                type="submit"
                id="aiChatSend"
                class="ai-chat-send"
                aria-label="Send"
            >

                <span id="aiSendIcon">

                    ➤

                </span>

                <span
                    id="aiSendSpinner"
                    class="spinner-border spinner-border-sm d-none"
                    role="status"
                    aria-hidden="true"
                ></span>

            </button>


        </form>


        <div class="ai-chat-disclaimer">

            Read-only • Attendance data only

        </div>


    </div>


</div>


<!-- =========================================================
     FLOATING AI BUTTON
========================================================= -->

<button
    type="button"
    id="aiChatButton"
    class="ai-chat-button"
    aria-label="Open Attendance Assistant"
    aria-expanded="false"
>

    <span class="ai-chat-open-icon">

        🤖

    </span>

    <span class="ai-chat-close-icon">

        ✕

    </span>

</button>


<!-- =========================================================
     JAVASCRIPT
========================================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {


    /* =========================================================
       PHOTO MODAL
    ========================================================= */

    const photoModal =
        document.getElementById('photoModal');

    const attendancePhoto =
        document.getElementById('attendancePhoto');

    const photoEmployee =
        document.getElementById('photoEmployee');


    if (photoModal) {

        photoModal.addEventListener(
            'show.bs.modal',
            function (event) {

                const button =
                    event.relatedTarget;

                const photo =
                    button.getAttribute(
                        'data-photo'
                    );

                const employee =
                    button.getAttribute(
                        'data-employee'
                    );


                attendancePhoto.src =
                    photo;

                photoEmployee.textContent =
                    employee;

            }
        );


        photoModal.addEventListener(
            'hidden.bs.modal',
            function () {

                attendancePhoto.src = '';

                photoEmployee.textContent = '';

            }
        );

    }


    /* =========================================================
       AI CHAT ELEMENTS
    ========================================================= */

    const aiChatButton =
        document.getElementById(
            'aiChatButton'
        );

    const aiChatWindow =
        document.getElementById(
            'aiChatWindow'
        );

    const aiChatMessages =
        document.getElementById(
            'aiChatMessages'
        );

    const aiChatForm =
        document.getElementById(
            'aiChatForm'
        );

    const aiChatInput =
        document.getElementById(
            'aiChatInput'
        );

    const aiChatSend =
        document.getElementById(
            'aiChatSend'
        );

    const aiSendIcon =
        document.getElementById(
            'aiSendIcon'
        );

    const aiSendSpinner =
        document.getElementById(
            'aiSendSpinner'
        );


    /* =========================================================
       OPEN / CLOSE CHAT
    ========================================================= */

    if (
        aiChatButton &&
        aiChatWindow
    ) {

        aiChatButton.addEventListener(
            'click',
            function () {

                const isOpen =
                    aiChatWindow.classList.contains(
                        'open'
                    );


                if (isOpen) {

                    closeAIChat();

                } else {

                    openAIChat();

                }

            }
        );

    }


    function openAIChat()
    {

        aiChatWindow.classList.add(
            'open'
        );

        aiChatButton.classList.add(
            'open'
        );

        aiChatButton.setAttribute(
            'aria-expanded',
            'true'
        );

        aiChatWindow.setAttribute(
            'aria-hidden',
            'false'
        );


        setTimeout(
            function () {

                aiChatInput.focus();

            },
            200
        );

    }


    function closeAIChat()
    {

        aiChatWindow.classList.remove(
            'open'
        );

        aiChatButton.classList.remove(
            'open'
        );

        aiChatButton.setAttribute(
            'aria-expanded',
            'false'
        );

        aiChatWindow.setAttribute(
            'aria-hidden',
            'true'
        );

    }


    /* =========================================================
       ADD USER MESSAGE
    ========================================================= */

    function addUserMessage(question)
    {

        const wrapper =
            document.createElement('div');

        wrapper.className =
            'ai-user-message';


        const bubble =
            document.createElement('div');

        bubble.className =
            'ai-user-bubble';

        bubble.textContent =
            question;


        wrapper.appendChild(
            bubble
        );


        aiChatMessages.appendChild(
            wrapper
        );


        scrollChatToBottom();

    }


    /* =========================================================
       ADD AI MESSAGE
    ========================================================= */

    function addAIMessage(answer)
    {

        const wrapper =
            document.createElement('div');

        wrapper.className =
            'ai-response-message';


        const avatar =
            document.createElement('div');

        avatar.className =
            'ai-message-avatar';

        avatar.textContent =
            '🤖';


        const bubble =
            document.createElement('div');

        bubble.className =
            'ai-response-bubble';

        bubble.textContent =
            answer;


        wrapper.appendChild(
            avatar
        );

        wrapper.appendChild(
            bubble
        );


        aiChatMessages.appendChild(
            wrapper
        );


        scrollChatToBottom();

    }


    /* =========================================================
       ADD TYPING INDICATOR
    ========================================================= */

    function showTyping()
    {

        removeTyping();


        const wrapper =
            document.createElement('div');

        wrapper.id =
            'aiTypingIndicator';

        wrapper.className =
            'ai-typing';


        const avatar =
            document.createElement('div');

        avatar.className =
            'ai-message-avatar';

        avatar.textContent =
            '🤖';


        const bubble =
            document.createElement('div');

        bubble.className =
            'ai-typing-bubble';


        const dots =
            document.createElement('div');

        dots.className =
            'ai-typing-dots';


        for (
            let i = 0;
            i < 3;
            i++
        ) {

            const dot =
                document.createElement('span');

            dots.appendChild(
                dot
            );

        }


        bubble.appendChild(
            dots
        );

        wrapper.appendChild(
            avatar
        );

        wrapper.appendChild(
            bubble
        );


        aiChatMessages.appendChild(
            wrapper
        );


        scrollChatToBottom();

    }


    /* =========================================================
       REMOVE TYPING INDICATOR
    ========================================================= */

    function removeTyping()
    {

        const typing =
            document.getElementById(
                'aiTypingIndicator'
            );


        if (typing) {

            typing.remove();

        }

    }


    /* =========================================================
       SCROLL CHAT
    ========================================================= */

    function scrollChatToBottom()
    {

        aiChatMessages.scrollTop =
            aiChatMessages.scrollHeight;

    }


    /* =========================================================
       SEND QUESTION
    ========================================================= */

    async function askAI(question)
    {

        if (!question) {

            return;

        }


        addUserMessage(
            question
        );


        showTyping();


        aiChatSend.disabled =
            true;

        aiChatInput.disabled =
            true;

        aiSendIcon.classList.add(
            'd-none'
        );

        aiSendSpinner.classList.remove(
            'd-none'
        );


        try {

            const response =
                await fetch(
                    "{{ route('attendance.ai.ask') }}",
                    {
                        method: 'POST',

                        headers: {

                            'Content-Type':
                                'application/json',

                            'Accept':
                                'application/json',

                            'X-CSRF-TOKEN':
                                document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    ?.getAttribute(
                                        'content'
                                    )

                        },

                        body:
                            JSON.stringify({
                                question:
                                    question
                            })

                    }
                );


            let data = null;


            try {

                data =
                    await response.json();

            } catch (jsonError) {

                throw new Error(
                    'The server returned an invalid response.'
                );

            }


            if (!response.ok) {

                throw new Error(
                    data?.message ||
                    'Unable to process the question.'
                );

            }


            if (!data?.success) {

                throw new Error(
                    data?.message ||
                    'The AI could not answer the question.'
                );

            }


            removeTyping();


            addAIMessage(
                data.answer ||
                'The AI did not return an answer.'
            );


        } catch (error) {

            console.error(
                'Attendance AI error:',
                error
            );


            removeTyping();


            addAIMessage(
                error.message ||
                'Unable to get an answer right now. Please try again.'
            );

        } finally {

            aiChatSend.disabled =
                false;

            aiChatInput.disabled =
                false;

            aiSendIcon.classList.remove(
                'd-none'
            );

            aiSendSpinner.classList.add(
                'd-none'
            );


            setTimeout(
                function () {

                    aiChatInput.focus();

                },
                50
            );

        }

    }


    /* =========================================================
       CHAT FORM
    ========================================================= */

    if (aiChatForm) {

        aiChatForm.addEventListener(
            'submit',
            async function (event) {

                event.preventDefault();


                const question =
                    aiChatInput.value.trim();


                if (!question) {

                    aiChatInput.focus();

                    return;

                }


                aiChatInput.value = '';


                await askAI(
                    question
                );

            }
        );

    }


    /* =========================================================
       SUGGESTION BUTTONS
    ========================================================= */

    document
        .querySelectorAll(
            '.ai-chat-suggestion'
        )
        .forEach(
            function (button) {

                button.addEventListener(
                    'click',
                    async function () {

                        const question =
                            this.dataset.question;


                        if (!question) {

                            return;

                        }


                        await askAI(
                            question
                        );

                    }
                );

            }
        );


    /* =========================================================
       ENTER KEY
    ========================================================= */

    if (aiChatInput) {

        aiChatInput.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Enter' &&
                    !event.shiftKey
                ) {

                    event.preventDefault();

                    aiChatForm.requestSubmit();

                }

            }
        );

    }


    /* =========================================================
       ESCAPE CLOSES CHAT
    ========================================================= */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape' &&
                aiChatWindow.classList.contains(
                    'open'
                )
            ) {

                closeAIChat();

            }

        }
    );


});

</script>


</body>

</html>