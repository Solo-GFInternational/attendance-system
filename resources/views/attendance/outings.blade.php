<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Outing History</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        body {
            background: #f5f7fa;
        }

        /* ========================================================= */
        /* ADMIN NAVIGATION */
        /* ========================================================= */

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

        /* ========================================================= */
        /* PAGE HEADER */
        /* ========================================================= */

        .page-header {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .date-badge {
            background: #212529;
            color: white;
            border-radius: 8px;
            padding: 8px 14px;
            display: inline-block;
        }

        /* ========================================================= */
        /* DASHBOARD CARDS */
        /* ========================================================= */

        .dashboard-card {
            border: 0;
            border-radius: 14px;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, .08) !important;
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

        /* ========================================================= */
        /* FILTER CARD */
        /* ========================================================= */

        .filter-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .04);
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 6px;
        }

        .filter-control {
            border-radius: 9px;
            min-height: 42px;
        }

        .filter-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .15);
        }

        .filter-button,
        .clear-button {
            min-height: 42px;
            border-radius: 9px;
            font-weight: 600;
        }

        /* ========================================================= */
        /* TABLE */
        /* ========================================================= */

        .table-card {
            border: 0;
            border-radius: 14px;
            overflow: hidden;
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

            flex-shrink: 0;
        }

        .record-count {
            background: #f8f9fa;
            color: #495057;

            border: 1px solid #dee2e6;

            border-radius: 8px;

            padding: 8px 12px;

            font-size: 13px;
            font-weight: 600;
        }

        @media (max-width: 991px) {

            .admin-navbar .nav-link {
                padding: 9px 11px;
                font-size: 14px;
            }

        }

    </style>

</head>


<body>


<div class="container-fluid py-4 px-4">


    <!-- ========================================================= -->
    <!-- ADMIN NAVIGATION -->
    <!-- ========================================================= -->

    <div class="admin-navbar mb-4">

        <div class="d-flex flex-wrap align-items-center gap-2">


            <!-- DASHBOARD -->

            <a href="{{ route('dashboard') }}"
               class="nav-link">

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
               class="nav-link active">

                🚶 Outings

            </a>


            <!-- EMPLOYEES -->

            <a href="{{ route('employees.index') }}"
               class="nav-link">

                👥 Employees

            </a>

            @if(auth()->user()?->is_admin)
    <a href="{{ route('admin-users.index') }}" class="nav-link">
        👤 Admin Users
    </a>
@endif

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


            <!-- PROFILE -->

            <div class="ms-lg-auto">

                <a href="{{ route('profile.edit') }}"
                   class="nav-link">

                    ⚙️ Profile

                </a>

            </div>


        </div>

    </div>



    <!-- ========================================================= -->
    <!-- PAGE HEADER -->
    <!-- ========================================================= -->

    <div class="page-header mb-4">


        <div class="d-flex flex-column flex-lg-row
                    justify-content-between
                    align-items-lg-center
                    gap-3">


            <div>

                <h2 class="fw-bold mb-1">

                    Outing History

                </h2>


                <p class="text-muted mb-2">

                    Employee going-out and return records

                </p>


                <span class="date-badge">

                    {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}

                </span>

            </div>


            <div>

                <span class="record-count">

                    {{ $totalOutings }}

                    {{ $totalOutings == 1 ? 'Outing' : 'Outings' }}

                </span>

            </div>


        </div>

    </div>



    <!-- ========================================================= -->
    <!-- SUMMARY CARDS -->
    <!-- ========================================================= -->

    <div class="row g-4 mb-4">


        <!-- TOTAL OUTINGS -->

        <div class="col-sm-6 col-xl-4">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">


                        <div>

                            <p class="text-muted mb-1">

                                Total Outings

                            </p>


                            <h2 class="fw-bold mb-0">

                                {{ $totalOutings }}

                            </h2>

                        </div>


                        <div class="stat-icon bg-primary-subtle">

                            🚶

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- CURRENTLY OUT -->

        <div class="col-sm-6 col-xl-4">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">


                        <div>

                            <p class="text-muted mb-1">

                                Currently Out

                            </p>


                            <h2 class="fw-bold text-danger mb-0">

                                {{ $currentlyOut }}

                            </h2>

                        </div>


                        <div class="stat-icon bg-danger-subtle">

                            🔴

                        </div>

                    </div>

                </div>

            </div>

        </div>



        <!-- TOTAL TIME -->

        <div class="col-sm-6 col-xl-4">

            <div class="card dashboard-card shadow-sm h-100">

                <div class="card-body p-4">

                    <div class="d-flex
                                justify-content-between
                                align-items-center">


                        <div>

                            <p class="text-muted mb-1">

                                Total Time Outside

                            </p>


                            <h2 class="fw-bold text-primary mb-0">

                                {{ $totalHours }}h {{ $remainingMinutes }}m

                            </h2>

                        </div>


                        <div class="stat-icon bg-primary-subtle">

                            ⏱️

                        </div>

                    </div>

                </div>

            </div>

        </div>


    </div>



    <!-- ========================================================= -->
    <!-- FILTERS -->
    <!-- ========================================================= -->

    <div class="card filter-card mb-4">


        <div class="card-body p-4">


            <div class="mb-3">

                <h5 class="fw-bold mb-1">

                    Search & Filter

                </h5>


                <p class="text-muted mb-0">

                    Find employee outing records

                </p>

            </div>


            <form method="GET"
                  action="{{ route('attendance.outings') }}">


                <div class="row g-3">


                    <!-- DATE -->

                    <div class="col-md-6 col-lg-3">

                        <label class="form-label filter-label">

                            Date

                        </label>


                        <input
                            type="date"
                            name="date"
                            value="{{ $date }}"
                            class="form-control filter-control"
                        >

                    </div>



                    <!-- EMPLOYEE -->

                    <div class="col-md-6 col-lg-3">

                        <label class="form-label filter-label">

                            Employee

                        </label>


                        <select
                            name="employee_id"
                            class="form-select filter-control"
                        >

                            <option value="">

                                All Employees

                            </option>


                            @foreach($employees as $employee)

                                <option
                                    value="{{ $employee->id }}"
                                    {{ request('employee_id') == $employee->id ? 'selected' : '' }}
                                >

                                    {{ $employee->name }}

                                </option>

                            @endforeach


                        </select>

                    </div>



                    <!-- DEPARTMENT -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label filter-label">

                            Department

                        </label>


                        <select
                            name="department"
                            class="form-select filter-control"
                        >

                            <option value="">

                                All Departments

                            </option>


                            @foreach($departments as $department)

                                <option
                                    value="{{ $department }}"
                                    {{ request('department') == $department ? 'selected' : '' }}
                                >

                                    {{ $department }}

                                </option>

                            @endforeach


                        </select>

                    </div>



                    <!-- SEARCH -->

                    <div class="col-md-6 col-lg-2">

                        <label class="form-label filter-label">

                            Search

                        </label>


                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control filter-control"
                            placeholder="Name or ID"
                        >

                    </div>



                    <!-- BUTTONS -->

                    <div class="col-md-12 col-lg-2">


                        <label class="form-label filter-label">

                            &nbsp;

                        </label>


                        <div class="d-flex gap-2">


                            <button
                                type="submit"
                                class="btn btn-primary filter-button flex-grow-1"
                            >

                                🔎 Search

                            </button>


                            <a
                                href="{{ route('attendance.outings') }}"
                                class="btn btn-light border clear-button"
                            >

                                Reset

                            </a>


                        </div>


                    </div>


                </div>

            </form>


        </div>

    </div>



    <!-- ========================================================= -->
    <!-- OUTING TABLE -->
    <!-- ========================================================= -->

    <div class="card table-card">


        <!-- TABLE HEADER -->

        <div class="card-body p-4 border-bottom">


            <div class="d-flex
                        flex-column flex-md-row
                        justify-content-between
                        align-items-md-center
                        gap-2">


                <div>

                    <h5 class="fw-bold mb-1">

                        Outing Records

                    </h5>


                    <p class="text-muted mb-0">

                        Every time an employee left and returned

                    </p>

                </div>


                <div>

                    <span class="record-count">

                        {{ $totalOutings }}

                        {{ $totalOutings == 1 ? 'Record' : 'Records' }}

                    </span>

                </div>


            </div>


        </div>



        <!-- TABLE -->

        <div class="table-responsive">


            <table class="table table-hover
                          align-middle
                          mb-0
                          attendance-table">


                <thead class="table-light">

                    <tr>

                        <th class="px-4 py-3">
                            #
                        </th>

                        <th>
                            Employee
                        </th>

                        <th>
                            Employee ID
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Went Out
                        </th>

                        <th>
                            Returned
                        </th>

                        <th>
                            Duration
                        </th>

                        <th>
                            Reason
                        </th>

                        <th>
                            Status
                        </th>

                    </tr>

                </thead>


                <tbody>


                @forelse($outings as $index => $outing)


                    <tr>


                        <!-- NUMBER -->

                        <td class="px-4">

                            {{ $index + 1 }}

                        </td>



                        <!-- EMPLOYEE -->

                        <td>


                            <div class="d-flex
                                        align-items-center
                                        gap-3">


                                <div class="employee-avatar">

                                    {{ strtoupper(
                                        substr(
                                            $outing->employee->name,
                                            0,
                                            1
                                        )
                                    ) }}

                                </div>


                                <div>

                                    <div class="fw-semibold">

                                        {{ $outing->employee->name }}

                                    </div>


                                    @if($outing->employee->designation)

                                        <small class="text-muted">

                                            {{ $outing->employee->designation }}

                                        </small>

                                    @endif

                                </div>


                            </div>


                        </td>



                        <!-- EMPLOYEE ID -->

                        <td>

                            <span class="fw-semibold">

                                {{ $outing->employee->employee_code }}

                            </span>

                        </td>



                        <!-- DEPARTMENT -->

                        <td>

                            {{ $outing->employee->department ?: '—' }}

                        </td>



                        <!-- WENT OUT -->

                        <td>

                            <span class="fw-semibold text-danger">

                                🚗

                                {{ \Carbon\Carbon::parse(
                                    $outing->out_time
                                )->format('h:i A') }}

                            </span>

                        </td>



                        <!-- RETURNED -->

                        <td>

                            @if($outing->return_time)

                                <span class="fw-semibold text-success">

                                    🟢

                                    {{ \Carbon\Carbon::parse(
                                        $outing->return_time
                                    )->format('h:i A') }}

                                </span>

                            @else

                                <span class="badge
                                             rounded-pill
                                             bg-danger-subtle
                                             text-danger
                                             border
                                             border-danger-subtle
                                             px-3 py-2">

                                    Currently Out

                                </span>

                            @endif

                        </td>



                        <!-- DURATION -->

                        <td>

                            @if($outing->duration_minutes !== null)

                                @php

                                    $hours = intdiv(
                                        $outing->duration_minutes,
                                        60
                                    );

                                    $minutes =
                                        $outing->duration_minutes % 60;

                                @endphp


                                <strong>

                                    @if($hours > 0)

                                        {{ $hours }}h {{ $minutes }}m

                                    @else

                                        {{ $minutes }} min

                                    @endif

                                </strong>

                            @else

                                <span class="text-muted">

                                    —

                                </span>

                            @endif

                        </td>



                        <!-- REASON -->

                        <td>

                            {{ $outing->reason ?: '—' }}

                        </td>



                        <!-- STATUS -->

                        <td>

                            @if($outing->return_time)

                                <span class="badge
                                             rounded-pill
                                             bg-success-subtle
                                             text-success
                                             border
                                             border-success-subtle
                                             px-3 py-2">

                                    ✓ Returned

                                </span>

                            @else

                                <span class="badge
                                             rounded-pill
                                             bg-danger-subtle
                                             text-danger
                                             border
                                             border-danger-subtle
                                             px-3 py-2">

                                    🔴 Out

                                </span>

                            @endif

                        </td>


                    </tr>


                @empty


                    <tr>

                        <td colspan="9"
                            class="text-center py-5">


                            <div class="text-muted">


                                <div class="fs-1 mb-3">

                                    🚶

                                </div>


                                <h5 class="fw-bold">

                                    No outing records

                                </h5>


                                <p class="text-muted mb-0">

                                    There are no outing records
                                    matching your filters.

                                </p>


                            </div>


                        </td>

                    </tr>


                @endforelse


                </tbody>

            </table>

        </div>


    </div>



    <!-- ========================================================= -->
    <!-- FOOTER -->
    <!-- ========================================================= -->

    <div class="text-center text-muted mt-4 mb-3">

        Showing {{ $totalOutings }}

        {{ $totalOutings == 1 ? 'outing record' : 'outing records' }}

    </div>


</div>


</body>

</html>