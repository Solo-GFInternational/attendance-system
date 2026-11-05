<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Attendance History</title>

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
       PAGE HEADER
       ========================================================= */

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


    /* =========================================================
       DOWNLOAD BUTTONS
       ========================================================= */

    .download-button {
        border-radius: 9px;
        font-weight: 600;
        min-height: 42px;
        padding: 9px 16px;
        white-space: nowrap;
        transition: .2s ease;
    }

    .download-button:hover {
        transform: translateY(-1px);
    }

    .download-pdf {
        background: #dc3545;
        border-color: #dc3545;
        color: white;
    }

    .download-pdf:hover {
        background: #bb2d3b;
        border-color: #b02a37;
        color: white;
    }

    .download-excel {
        background: #198754;
        border-color: #198754;
        color: white;
    }

    .download-excel:hover {
        background: #157347;
        border-color: #146c43;
        color: white;
    }


    /* =========================================================
       DASHBOARD CARDS
       ========================================================= */

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


    /* =========================================================
       FILTER CARD
       ========================================================= */

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

    .filter-button {
        min-height: 42px;
        border-radius: 9px;
        font-weight: 600;
    }

    .clear-button {
        min-height: 42px;
        border-radius: 9px;
        font-weight: 600;
    }


    /* =========================================================
       TABLE
       ========================================================= */

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


    /* =========================================================
       PHOTO BUTTON
       ========================================================= */

    .photo-button {
        border-radius: 8px;
        font-weight: 600;
        font-size: 13px;
        white-space: nowrap;
    }

    .photo-button:hover {
        transform: translateY(-1px);
    }

    .photo-modal-image {
        width: 100%;
        max-height: 70vh;
        object-fit: contain;
        border-radius: 10px;
    }

    .photo-loading {
        display: none;
    }

    .photo-error {
        display: none;
    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 991px) {

        .admin-navbar .nav-link {
            padding: 9px 11px;
            font-size: 14px;
        }

    }

    @media (max-width: 767px) {

        .page-header {
            padding: 18px;
        }

        .download-buttons {
            width: 100%;
        }

        .download-buttons .download-button {
            flex: 1;
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

        <a href="{{ route('dashboard') }}"
           class="nav-link">
            📊 Dashboard
        </a>

        <a href="{{ route('attendance.admin') }}"
           class="nav-link">
            🕘 Attendance
        </a>

        <a href="{{ route('attendance.history') }}"
           class="nav-link active">
            📋 History
        </a>

        <a href="{{ route('attendance.outings') }}"
           class="nav-link">
            🚶 Outings
        </a>

        <a href="{{ route('employees.index') }}"
           class="nav-link">
            👥 Employees
        </a>
        <a href="{{ route('sites.index') }}"
   class="nav-link">

    🏢 Sites

</a>

        <a href="{{ route('visitors.index') }}"
           class="nav-link">
            🚪 Visitors
        </a>

        <a href="{{ route('attendance.emergency') }}"
           class="nav-link">
            🚨 Emergency
        </a>
        @if(auth()->user()?->is_admin)
    <a href="{{ route('admin-users.index') }}" class="nav-link">
        👤 Admin Users
    </a>
@endif

        <div class="ms-lg-auto">

            <a href="{{ route('profile.edit') }}"
               class="nav-link">
                ⚙️ Profile
            </a>

        </div>

    </div>

</div>


<!-- =========================================================
     PAGE HEADER
     ========================================================= -->

<div class="page-header mb-4">

    <div class="d-flex flex-column flex-lg-row
                justify-content-between
                align-items-lg-center
                gap-3">

        <div>

            <h2 class="fw-bold mb-1">
                Attendance History
            </h2>

            <p class="text-muted mb-2">
                View and filter employee attendance records
            </p>

            <span class="date-badge">

                {{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}

            </span>

        </div>


        <div class="d-flex flex-column flex-sm-row
                    align-items-sm-center
                    gap-2">

            <div class="download-buttons d-flex gap-2">

                <a href="{{ route('attendance.history.pdf', request()->query()) }}"
                   class="btn download-button download-pdf">
                    📄 Download PDF
                </a>

                <a href="{{ route('attendance.history.excel', request()->query()) }}"
                   class="btn download-button download-excel">
                    📊 Download Excel
                </a>

            </div>

            <span class="badge bg-light text-dark border px-3 py-2">

                {{ $totalRecords }}

                {{ $totalRecords == 1 ? 'Record' : 'Records' }}

            </span>

        </div>

    </div>

</div>


<!-- =========================================================
     SUMMARY CARDS
     ========================================================= -->

<div class="row g-4 mb-4">


    <!-- ATTENDANCE RECORDS -->

    <div class="col-sm-6 col-xl-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <p class="text-muted mb-1">
                            Attendance Records
                        </p>

                        <h2 class="fw-bold mb-0">
                            {{ $totalRecords }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-primary-subtle">
                        📋
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


    <!-- OUTINGS -->

    <div class="col-sm-6 col-xl-3">

        <div class="card dashboard-card shadow-sm h-100">

            <div class="card-body p-4">

                <div class="d-flex
                            justify-content-between
                            align-items-center">

                    <div>

                        <p class="text-muted mb-1">
                            Outings
                        </p>

                        <h2 class="fw-bold text-secondary mb-0">
                            {{ $totalOutings }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-secondary-subtle">
                        🚶
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- =========================================================
     FILTERS
     ========================================================= -->

<div class="card filter-card mb-4">

    <div class="card-body p-4">

        <div class="mb-3">

            <h5 class="fw-bold mb-1">
                Search & Filter
            </h5>

            <p class="text-muted mb-0">
                Narrow down attendance records
            </p>

        </div>


        <form method="GET"
              action="{{ route('attendance.history') }}">

            <div class="row g-3">


                <!-- DATE -->

                <div class="col-md-6 col-lg-2">

                    <label for="date"
                           class="form-label filter-label">
                        Date
                    </label>

                    <input
                        type="date"
                        id="date"
                        name="date"
                        value="{{ $date }}"
                        class="form-control filter-control"
                    >

                </div>


                <!-- EMPLOYEE -->

                <div class="col-md-6 col-lg-4">

                    <label for="search"
                           class="form-label filter-label">
                        Employee
                    </label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search name or employee ID..."
                        class="form-control filter-control"
                    >

                </div>

<!-- SITE -->
<div class="col-md-6 col-lg-2">

    <label for="site_id"
           class="form-label filter-label">

        Site

    </label>

    <select
        id="site_id"
        name="site_id"
        class="form-select filter-control"
    >

        <option value="">
            All Sites
        </option>

        @foreach($sites as $site)

            <option
                value="{{ $site->id }}"
                {{ (string) request('site_id') === (string) $site->id ? 'selected' : '' }}
            >
                {{ $site->name }}
            </option>

        @endforeach

    </select>

</div>
                <!-- DEPARTMENT -->

                <div class="col-md-6 col-lg-2">

                    <label for="department"
                           class="form-label filter-label">
                        Department
                    </label>

                    <select
                        id="department"
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


                <!-- STATUS -->

                <div class="col-md-6 col-lg-2">

                    <label for="status"
                           class="form-label filter-label">
                        Status
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="form-select filter-control"
                    >

                        <option value="">
                            All Status
                        </option>

                        <option
                            value="Present"
                            {{ request('status') == 'Present' ? 'selected' : '' }}
                        >
                            Present
                        </option>

                        <option
                            value="Late"
                            {{ request('status') == 'Late' ? 'selected' : '' }}
                        >
                            Late
                        </option>

                        <option
                            value="Absent"
                            {{ request('status') == 'Absent' ? 'selected' : '' }}
                        >
                            Absent
                        </option>

                        <option
                            value="Leave"
                            {{ request('status') == 'Leave' ? 'selected' : '' }}
                        >
                            Leave
                        </option>

                    </select>

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
                            🔍 Filter
                        </button>

                        <a
                            href="{{ route('attendance.history') }}"
                            class="btn btn-light border clear-button"
                        >
                            Clear
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


<!-- =========================================================
     ATTENDANCE TABLE
     ========================================================= -->

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
                    Attendance Records
                </h5>

                <p class="text-muted mb-0">
                    Attendance records for the selected date
                </p>

            </div>

            <div>

                <span class="record-count">

                    {{ $totalRecords }}

                    {{ $totalRecords == 1 ? 'Record' : 'Records' }}

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


            <!-- TABLE HEADER -->

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
                        Outings
                    </th>

                    <th>
                        Photo
                    </th>

                </tr>

            </thead>


            <!-- TABLE BODY -->

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

                            <span class="badge
                                         rounded-pill
                                         bg-success-subtle
                                         text-success
                                         border
                                         border-success-subtle
                                         px-3 py-2">

                                ✓ Present

                            </span>


                        @elseif($record->status === 'Late')

                            <span class="badge
                                         rounded-pill
                                         bg-warning-subtle
                                         text-warning-emphasis
                                         border
                                         border-warning-subtle
                                         px-3 py-2">

                                ⏰ Late

                            </span>


                        @elseif($record->status === 'Absent')

                            <span class="badge
                                         rounded-pill
                                         bg-danger-subtle
                                         text-danger
                                         border
                                         border-danger-subtle
                                         px-3 py-2">

                                ✕ Absent

                            </span>


                        @elseif($record->status === 'Leave')

                            <span class="badge
                                         rounded-pill
                                         bg-info-subtle
                                         text-info-emphasis
                                         border
                                         border-info-subtle
                                         px-3 py-2">

                                Leave

                            </span>


                        @else

                            <span class="badge
                                         rounded-pill
                                         bg-secondary-subtle
                                         text-secondary
                                         border
                                         border-secondary-subtle
                                         px-3 py-2">

                                {{ $record->status }}

                            </span>

                        @endif

                    </td>


                    <!-- OUTINGS -->

                    <td>

                        @if($record->outings->count() > 0)

                            <span class="badge
                                         bg-light
                                         text-dark
                                         border
                                         px-3 py-2">

                                🚶 {{ $record->outings->count() }}

                            </span>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </td>


                    <!-- PHOTO -->

                    <td>

                        @if($record->sign_in_photo)

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary photo-button"
                                data-bs-toggle="modal"
                                data-bs-target="#photoModal"
                                data-photo="{{ asset('storage/' . ltrim($record->sign_in_photo, '/')) }}"
                                data-name="{{ $record->employee->name }}"
                                data-date="{{ \Carbon\Carbon::parse($record->attendance_date)->format('d/m/Y') }}"
                            >
                                📷 View Photo
                            </button>

                        @else

                            <span class="text-muted">
                                No Photo
                            </span>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="9"
                        class="text-center py-5">

                        <div class="text-muted">

                            <div class="fs-1 mb-2">
                                📋
                            </div>

                            <div class="fw-semibold">
                                No attendance records found
                            </div>

                            <small>
                                Try changing the date or filters.
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


<div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content border-0 shadow">


        <!-- MODAL HEADER -->

        <div class="modal-header">

            <div>

                <h5
                    class="modal-title fw-bold"
                    id="photoModalLabel"
                >
                    Sign-In Photo
                </h5>

                <small
                    class="text-muted"
                    id="photoModalDetails"
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


            <!-- LOADING -->

            <div
                id="photoLoading"
                class="photo-loading py-5"
            >

                <div
                    class="spinner-border text-primary"
                    role="status"
                >

                    <span class="visually-hidden">
                        Loading...
                    </span>

                </div>

                <div class="mt-2 text-muted">
                    Loading photo...
                </div>

            </div>


            <!-- ERROR -->

            <div
                id="photoError"
                class="photo-error py-5"
            >

                <div class="fs-1 mb-2">
                    ⚠️
                </div>

                <div class="fw-semibold">
                    Photo could not be loaded
                </div>

                <small class="text-muted">
                    Please check the storage link on the server.
                </small>

            </div>


            <!-- PHOTO -->

            <img
                id="attendancePhoto"
                src=""
                alt="Employee Sign-In Photo"
                class="photo-modal-image"
            >

        </div>

    </div>

</div>


</div>

<!-- =========================================================
     PHOTO MODAL JAVASCRIPT
     ========================================================= -->

<script>

document.addEventListener('DOMContentLoaded', function () {

    const photoModal =
        document.getElementById('photoModal');

    const image =
        document.getElementById('attendancePhoto');

    const details =
        document.getElementById('photoModalDetails');

    const loading =
        document.getElementById('photoLoading');

    const error =
        document.getElementById('photoError');


    if (!photoModal || !image) {
        return;
    }


    photoModal.addEventListener(
        'show.bs.modal',
        function (event) {

            const button =
                event.relatedTarget;

            if (!button) {
                return;
            }


            const photo =
                button.getAttribute('data-photo');

            const name =
                button.getAttribute('data-name');

            const date =
                button.getAttribute('data-date');


            details.textContent =
                name + ' • ' + date;


            image.style.display = 'none';

            error.style.display = 'none';

            loading.style.display = 'block';


            image.onload = function () {

                loading.style.display = 'none';

                error.style.display = 'none';

                image.style.display = 'block';

            };


            image.onerror = function () {

                loading.style.display = 'none';

                image.style.display = 'none';

                error.style.display = 'block';

            };


            image.src = photo;

        }
    );


    photoModal.addEventListener(
        'hidden.bs.modal',
        function () {

            image.src = '';

            image.style.display = 'none';

            loading.style.display = 'none';

            error.style.display = 'none';

            details.textContent = '';

        }
    );

});

</script>

</body>

</html>
