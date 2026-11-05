<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<meta name="csrf-token"
      content="{{ csrf_token() }}">

<title>Employee Management</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!-- Local Face API -->
<script src="{{ asset('js/face-api/face-api.js') }}"></script>

<style>

   /* ========================================================= */
/* GLOBAL */
/* ========================================================= */

:root {
    --gf-primary: #0d6efd;
    --gf-primary-dark: #0b5ed7;
    --gf-success: #198754;
    --gf-danger: #dc3545;
    --gf-warning: #f59f00;
    --gf-dark: #17202a;
    --gf-muted: #6c757d;
    --gf-border: #e9ecef;
    --gf-bg: #f4f7fb;
}

* {
    box-sizing: border-box;
}

body {
    background:
        linear-gradient(
            180deg,
            #eef3f9 0%,
            #f7f9fc 260px,
            #f4f7fb 100%
        );
    color: var(--gf-dark);
    font-family:
        Inter,
        system-ui,
        -apple-system,
        BlinkMacSystemFont,
        "Segoe UI",
        sans-serif;
}

/* ========================================================= */
/* MAIN CONTAINER */
/* ========================================================= */

.container-fluid {
    max-width: 1700px;
}

/* ========================================================= */
/* ADMIN NAVIGATION */
/* ========================================================= */

.admin-navbar {
    background: rgba(255, 255, 255, .96);
    border: 1px solid rgba(0, 0, 0, .05);
    border-radius: 16px;
    padding: 8px;
    box-shadow:
        0 4px 18px rgba(0, 0, 0, .05);
    backdrop-filter: blur(10px);
}

.admin-navbar .nav-link {
    color: #495057;
    font-size: 14px;
    font-weight: 600;
    border-radius: 11px;
    padding: 10px 14px;
    transition:
        background .18s ease,
        color .18s ease,
        transform .18s ease;
    text-decoration: none;
}

.admin-navbar .nav-link:hover {
    background: #f0f4f9;
    color: var(--gf-primary);
    transform: translateY(-1px);
}

.admin-navbar .nav-link.active {
    background: var(--gf-primary);
    color: #fff;
    box-shadow:
        0 4px 12px rgba(13, 110, 253, .20);
}

/* ========================================================= */
/* PAGE HEADER */
/* ========================================================= */

.page-header {
    position: relative;
    overflow: hidden;
    background: #fff;
    border: 1px solid rgba(0, 0, 0, .04);
    border-radius: 18px;
    padding: 28px 30px;
    box-shadow:
        0 5px 22px rgba(0, 0, 0, .05);
}

.page-header::after {
    content: "";
    position: absolute;
    width: 180px;
    height: 180px;
    right: -70px;
    top: -80px;
    border-radius: 50%;
    background: rgba(13, 110, 253, .05);
    pointer-events: none;
}

.page-header h2 {
    font-size: 27px;
    letter-spacing: -.5px;
    color: #182230;
}

.page-header p {
    font-size: 14px;
}

/* Header buttons */

.page-header .btn {
    min-height: 46px;
    border-radius: 11px;
    font-weight: 700;
    padding-left: 18px;
    padding-right: 18px;
}

.page-header .btn-primary {
    box-shadow:
        0 5px 14px rgba(13, 110, 253, .18);
}

/* ========================================================= */
/* ALERTS */
/* ========================================================= */

.alert {
    border-radius: 13px !important;
    padding: 14px 17px;
}

.alert-success {
    background: #ecfdf3;
    color: #146c43;
}

.alert-danger {
    background: #fff1f2;
    color: #b02a37;
}

/* ========================================================= */
/* SUMMARY CARDS */
/* ========================================================= */

.dashboard-card {
    border: 1px solid rgba(0, 0, 0, .04);
    border-radius: 16px;
    background: #fff;
    transition:
        transform .2s ease,
        box-shadow .2s ease;
}

.dashboard-card:hover {
    transform: translateY(-3px);
    box-shadow:
        0 12px 30px rgba(0, 0, 0, .08) !important;
}

.dashboard-card .card-body {
    padding: 22px !important;
}

.dashboard-card p {
    font-size: 13px;
    font-weight: 600;
    margin-bottom: 5px !important;
}

.dashboard-card h2 {
    font-size: 28px;
    letter-spacing: -.5px;
}

.stat-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    box-shadow:
        inset 0 0 0 1px rgba(0, 0, 0, .03);
}

/* ========================================================= */
/* SEARCH PANEL */
/* ========================================================= */

.filter-card {
    border: 1px solid rgba(0, 0, 0, .04);
    border-radius: 16px;
    background: #fff;
    box-shadow:
        0 5px 20px rgba(0, 0, 0, .045);
}

.filter-card .card-body {
    padding: 22px !important;
}

.filter-card h5 {
    font-size: 16px;
}

.search-control {
    min-height: 46px;
    border-radius: 11px;
    border: 1px solid #dfe4ea;
    padding-left: 15px;
    font-size: 14px;
    transition:
        border-color .2s ease,
        box-shadow .2s ease;
}

.search-control:focus {
    border-color: var(--gf-primary);
    box-shadow:
        0 0 0 3px rgba(13, 110, 253, .10);
}

.search-button {
    min-height: 46px;
    border-radius: 11px;
    font-weight: 700;
}

/* ========================================================= */
/* EMPLOYEE TABLE CARD */
/* ========================================================= */

.table-card {
    border: 1px solid rgba(0, 0, 0, .04);
    border-radius: 17px;
    overflow: hidden;
    background: #fff;
    box-shadow:
        0 5px 22px rgba(0, 0, 0, .05);
}

.table-card > .card-body {
    background: #fff;
}

.table-card h5 {
    font-size: 17px;
}

/* ========================================================= */
/* TABLE */
/* ========================================================= */

.employee-table {
    min-width: 1100px;
}

.employee-table thead th {
    background: #f8fafc !important;
    border-bottom: 1px solid #e7ebef;
    color: #667085;
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .07em;
    text-transform: uppercase;
    padding-top: 14px !important;
    padding-bottom: 14px !important;
}

.employee-table tbody tr {
    transition:
        background .15s ease;
}

.employee-table tbody tr:hover {
    background: #f8fbff;
}

.employee-table td {
    border-color: #edf0f3;
    color: #343a40;
    font-size: 14px;
    padding-top: 15px !important;
    padding-bottom: 15px !important;
}

.employee-table tbody tr:last-child td {
    border-bottom: 0;
}

/* ========================================================= */
/* EMPLOYEE ID */
/* ========================================================= */

.employee-id {
    display: inline-flex;
    align-items: center;
    padding: 6px 10px;
    border-radius: 8px;
    background: #f1f5f9;
    color: #475569;
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .03em;
}

/* ========================================================= */
/* EMPLOYEE AVATAR */
/* ========================================================= */

.employee-avatar {
    width: 44px;
    height: 44px;
    min-width: 44px;
    border-radius: 13px;
    background:
        linear-gradient(
            135deg,
            #e7f0ff,
            #dbeafe
        );
    color: #0d6efd;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
    font-weight: 800;
    border: 1px solid #d8e6ff;
}

/* Employee name */

.employee-name {
    color: #1f2937;
    font-size: 14px;
    font-weight: 700;
}

.employee-meta {
    color: #98a2b3;
    font-size: 12px;
    margin-top: 2px;
}

/* ========================================================= */
/* STATUS BADGES */
/* ========================================================= */

.employee-table .badge {
    font-size: 11px;
    font-weight: 800;
    letter-spacing: .01em;
}

.status-badge {
    min-width: 88px;
    text-align: center;
}

.face-badge {
    min-width: 112px;
    text-align: center;
}

/* ========================================================= */
/* ACTION AREA */
/* ========================================================= */

.action-button {
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    padding: 7px 10px;
    transition:
        transform .15s ease,
        box-shadow .15s ease;
}

.action-button:hover {
    transform: translateY(-1px);
}

.action-group {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 6px;
    flex-wrap: nowrap;
}

.action-group form {
    margin: 0;
}

/* ========================================================= */
/* ACTION COLORS */
/* ========================================================= */

.btn-outline-success {
    border-color: #b7e4c7;
}

.btn-outline-success:hover {
    box-shadow:
        0 4px 12px rgba(25, 135, 84, .12);
}

.btn-outline-danger:hover {
    box-shadow:
        0 4px 12px rgba(220, 53, 69, .12);
}

.btn-outline-primary:hover {
    box-shadow:
        0 4px 12px rgba(13, 110, 253, .12);
}

/* ========================================================= */
/* TABLE COUNT */
/* ========================================================= */

.employee-count {
    background: #f8fafc;
    color: #475467;
    border: 1px solid #e4e7ec !important;
    border-radius: 9px !important;
    font-size: 12px;
    font-weight: 700;
}

/* ========================================================= */
/* EMPTY STATE */
/* ========================================================= */

.empty-state {
    padding: 65px 20px !important;
}

.empty-state-icon {
    width: 72px;
    height: 72px;
    margin: 0 auto 18px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f5f9;
    font-size: 30px;
}

.empty-state h5 {
    color: #344054;
}

/* ========================================================= */
/* FOOTER */
/* ========================================================= */

.page-footer {
    font-size: 12px;
    color: #98a2b3;
}

/* ========================================================= */
/* FACE REGISTRATION */
/* ========================================================= */

#faceRegistrationModal .face-camera-wrapper {
    position: relative !important;
    display: block !important;
    width: 100% !important;
    max-width: 720px !important;
    height: 430px !important;
    min-height: 430px !important;
    margin: 0 auto !important;
    overflow: hidden !important;
    border-radius: 18px;
    background: #0b0f14 !important;
    box-shadow:
        0 12px 35px rgba(0, 0, 0, .18);
}

#faceRegistrationModal #faceRegistrationVideo {
    position: relative !important;
    display: block !important;
    width: 100% !important;
    height: 430px !important;
    min-height: 430px !important;
    max-width: none !important;
    object-fit: cover !important;
    background: #0b0f14 !important;
    visibility: visible !important;
    opacity: 1 !important;
    z-index: 1 !important;
    transform: scaleX(-1);
}

/* ========================================================= */
/* CAMERA FACE GUIDE */
/* ========================================================= */

#faceRegistrationModal .face-camera-overlay {
    position: absolute;
    inset: 0;
    z-index: 2;
    pointer-events: none;
    border-radius: 18px;
}

#faceRegistrationModal .face-camera-overlay::before {
    content: "";
    position: absolute;
    width: 250px;
    height: 320px;
    left: 50%;
    top: 50%;
    transform: translate(-50%, -50%);
    border: 3px solid rgba(255, 255, 255, .75);
    border-radius: 50%;
    box-shadow:
        0 0 0 9999px rgba(0, 0, 0, .12),
        0 0 25px rgba(255, 255, 255, .12);
}

#faceRegistrationModal .face-camera-overlay::after {
    content: "";
    position: absolute;
    inset: 14px;
    border-radius: 14px;
    border: 1px solid rgba(255, 255, 255, .18);
}

/* ========================================================= */
/* FACE MODAL */
/* ========================================================= */

#faceRegistrationModal.face-modal-open {
    display: block !important;
    position: fixed !important;
    inset: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 1050 !important;
    background: rgba(0, 0, 0, .72);
}

#faceRegistrationModal.face-modal-open .modal-dialog {
    display: flex !important;
    align-items: center;
    min-height: calc(100% - 2rem);
    max-width: 820px;
}

#faceRegistrationModal.face-modal-open .modal-content {
    display: block !important;
    width: 100% !important;
    border-radius: 20px !important;
    overflow: hidden;
    border: 0 !important;
    box-shadow:
        0 25px 70px rgba(0, 0, 0, .35);
}

#faceRegistrationModal .modal-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
    background: #fff;
}

#faceRegistrationModal .modal-title {
    font-size: 20px;
    letter-spacing: -.2px;
}

#faceRegistrationModal .face-registration-employee {
    font-size: 14px;
    font-weight: 600;
    color: #6c757d;
}

#faceRegistrationModal .modal-body {
    padding: 24px;
    background: #f8f9fa;
}

#faceRegistrationModal .face-status {
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 20px;
    padding: 12px 16px;
}

#faceRegistrationModal #captureFaceButton {
    min-width: 260px;
    min-height: 52px;
    border-radius: 12px;
    font-size: 16px;
    font-weight: 700;
    box-shadow:
        0 5px 15px rgba(25, 135, 84, .15);
    transition: all .2s ease;
}

#faceRegistrationModal #captureFaceButton:not(:disabled):hover {
    transform: translateY(-1px);
    box-shadow:
        0 8px 20px rgba(25, 135, 84, .22);
}

#faceRegistrationModal #captureFaceButton:disabled {
    cursor: not-allowed;
    opacity: .65;
}

#faceRegistrationModal .face-help {
    font-size: 13px;
    line-height: 1.7;
    color: #6c757d;
}

#faceRegistrationModal .face-help div:first-child {
    font-weight: 600;
    color: #495057;
}

#faceRegistrationModal .modal-footer {
    padding: 16px 24px;
    background: #fff;
    border-top: 1px solid #e9ecef;
}

#faceRegistrationModal .modal-footer .btn {
    min-width: 100px;
    border-radius: 10px;
    font-weight: 600;
}

/* ========================================================= */
/* MANUAL MODAL SUPPORT */
/* ========================================================= */

#faceRegistrationModal {
    display: none;
}

#faceRegistrationModal.face-modal-open {
    display: block !important;
    overflow-x: hidden;
    overflow-y: auto;
}

#faceRegistrationModal.face-modal-open .modal-dialog {
    transform: none;
}

body.modal-open {
    overflow: hidden;
}

.face-modal-backdrop {
    position: fixed;
    inset: 0;
    z-index: 1040;
    background: rgba(0, 0, 0, .5);
}

#faceRegistrationModal {
    z-index: 1050;
}

/* ========================================================= */
/* RESPONSIVE */
/* ========================================================= */

@media (max-width: 991px) {

    .admin-navbar .nav-link {
        font-size: 13px;
        padding: 9px 11px;
    }

    .page-header {
        padding: 23px;
    }

    .page-header h2 {
        font-size: 24px;
    }

}

@media (max-width: 767px) {

    .container-fluid {
        padding-left: 12px !important;
        padding-right: 12px !important;
    }

    .admin-navbar {
        overflow-x: auto;
        flex-wrap: nowrap;
    }

    .admin-navbar > div {
        flex-wrap: nowrap !important;
        width: max-content;
    }

    .page-header {
        padding: 20px;
        border-radius: 15px;
    }

    .page-header .btn {
        width: 100%;
    }

    .page-header .d-flex.gap-2 {
        flex-direction: column;
        width: 100%;
    }

    .dashboard-card .card-body {
        padding: 18px !important;
    }

    .filter-card .card-body {
        padding: 18px !important;
    }

    .search-button {
        width: 100%;
    }

    .table-card {
        border-radius: 14px;
    }

    #faceRegistrationModal .modal-dialog {
        margin: .5rem;
        min-height: calc(100% - 1rem);
    }

    #faceRegistrationModal .modal-content {
        border-radius: 16px !important;
    }

    #faceRegistrationModal .modal-header {
        padding: 16px;
    }

    #faceRegistrationModal .modal-body {
        padding: 16px;
    }

    #faceRegistrationModal .face-camera-wrapper {
        height: 360px !important;
        min-height: 360px !important;
        border-radius: 14px;
    }

    #faceRegistrationModal #faceRegistrationVideo {
        height: 360px !important;
        min-height: 360px !important;
    }

    #faceRegistrationModal .face-camera-overlay::before {
        width: 210px;
        height: 270px;
    }

    #faceRegistrationModal #captureFaceButton {
        width: 100%;
        min-width: 0;
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

        <a href="{{ route('dashboard') }}"
           class="nav-link">

            📊 Dashboard

        </a>

        <a href="{{ route('attendance.admin') }}"
           class="nav-link">

            🕘 Attendance

        </a>

        <a href="{{ route('attendance.history') }}"
           class="nav-link">

            📋 History

        </a>

        <a href="{{ route('attendance.outings') }}"
           class="nav-link">

            🚶 Outings

        </a>

        <a href="{{ route('employees.index') }}"
           class="nav-link active">

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

    <div class="d-flex
                flex-column flex-lg-row
                justify-content-between
                align-items-lg-center
                gap-3">

        <div>

            <h2 class="fw-bold mb-1">

                Employee Management

            </h2>

            <p class="text-muted mb-0">

                Manage employees and register face recognition

            </p>

        </div>

        <div>

            <div class="d-flex gap-2">

                <a href="{{ route('employees.import') }}"
                   class="btn btn-outline-success btn-lg">

                    📥 Import CSV

                </a>

                <a href="{{ route('employees.create') }}"
                   class="btn btn-primary btn-lg">

                    + Add Employee

                </a>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- SUCCESS MESSAGE -->
<!-- ========================================================= -->

@if(session('success'))

    <div class="alert alert-success
                alert-dismissible
                fade show
                border-0
                shadow-sm"
         role="alert">

        <strong>Success!</strong>

        {{ session('success') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


<!-- ========================================================= -->
<!-- ERROR MESSAGE -->
<!-- ========================================================= -->

@if(session('error'))

    <div class="alert alert-danger
                alert-dismissible
                fade show
                border-0
                shadow-sm"
         role="alert">

        <strong>Error!</strong>

        {{ session('error') }}

        <button type="button"
                class="btn-close"
                data-bs-dismiss="alert">
        </button>

    </div>

@endif


<!-- ========================================================= -->
<!-- VALIDATION ERRORS -->
<!-- ========================================================= -->

@if($errors->any())

    <div class="alert alert-danger
                border-0
                shadow-sm">

        <strong>Please check the following:</strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>{{ $error }}</li>

            @endforeach

        </ul>

    </div>

@endif


<!-- ========================================================= -->
<!-- SUMMARY -->
<!-- ========================================================= -->

<!-- ========================================================= -->
<!-- SUMMARY -->
<!-- ========================================================= -->

<div class="row g-4 mb-4">

    <!-- TOTAL -->

    <div class="col-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted">
                            Total Employees
                        </p>

                        <h2 class="fw-bold mb-0">
                            {{ $employees->count() }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-primary-subtle">
                        👥
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ACTIVE -->

    <div class="col-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted">
                            Active
                        </p>

                        <h2 class="fw-bold text-success mb-0">
                            {{ $employees->where('status', 'Active')->count() }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-success-subtle">
                        ✓
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- INACTIVE -->

    <div class="col-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted">
                            Inactive
                        </p>

                        <h2 class="fw-bold text-secondary mb-0">
                            {{ $employees->where('status', '!=', 'Active')->count() }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-secondary-subtle">
                        ⏸
                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- FACE REGISTERED -->

    <div class="col-6 col-xl-3">

        <div class="card dashboard-card h-100">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <p class="text-muted">
                            Face Registered
                        </p>

                        <h2 class="fw-bold text-success mb-0">
                            {{ $employees->whereNotNull('face_descriptor')->count() }}
                        </h2>

                    </div>

                    <div class="stat-icon bg-success-subtle">
                        📷
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<!-- ========================================================= -->
<!-- SEARCH -->
<!-- ========================================================= -->

<div class="card filter-card mb-4">

    <div class="card-body p-4">

        <div class="mb-3">

            <h5 class="fw-bold mb-1">

                Search Employees

            </h5>

            <p class="text-muted mb-0">

                Search by employee ID, name, department or designation

            </p>

        </div>

        <form method="GET"
      action="{{ route('employees.index') }}">

    <div class="row g-3">

        {{-- SITE --}}
        <div class="col-md-3">

            <label for="site_id"
                   class="form-label fw-semibold">

                Site

            </label>

            <select
                name="site_id"
                id="site_id"
                class="form-select search-control"
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


        {{-- SEARCH --}}
        <div class="col-md-7">

            <label for="search"
                   class="form-label fw-semibold">

                Search

            </label>

            <input
                type="text"
                name="search"
                id="search"
                class="form-control search-control"
                placeholder="Search by Employee ID, name, department or designation..."
                value="{{ request('search') }}"
            >

        </div>


        {{-- BUTTON --}}
        <div class="col-md-2 d-flex align-items-end">

            <button
                type="submit"
                class="btn btn-primary search-button w-100"
            >

                🔍 Search

            </button>

        </div>

    </div>

</form>

    </div>

</div>


<!-- ========================================================= -->
<!-- EMPLOYEE TABLE -->
<!-- ========================================================= -->

<div class="card table-card">

    <div class="card-body p-4 border-bottom">

        <div class="d-flex
                    flex-column flex-md-row
                    justify-content-between
                    align-items-md-center
                    gap-2">

            <div>

                <h5 class="fw-bold mb-1">

                    Employees

                </h5>

                <p class="text-muted mb-0">

                    Manage employee records, face recognition and account status

                </p>

            </div>

            <span class="employee-count px-3 py-2">

    {{ $employees->count() }}

    {{ $employees->count() == 1 ? 'Employee' : 'Employees' }}

</span>

        </div>

    </div>


    <div class="table-responsive">

        <table class="table table-hover
                      align-middle
                      mb-0
                      employee-table">

            <thead class="table-light">

                <tr>

                    <th class="px-4 py-3">
                        Employee ID
                    </th>

                    <th>
                        Employee
                    </th>

                    <th>
                        Department
                    </th>

                    <th>
                        Designation
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Face
                    </th>

                    <th class="text-end px-4">
                        Actions
                    </th>

                </tr>

            </thead>


            <tbody>

            @forelse($employees as $employee)

                <tr>

                    <!-- EMPLOYEE ID -->

                    <td class="px-4">

                        <span class="employee-id">
    {{ $employee->employee_code }}
</span>

                    </td>


                    <!-- EMPLOYEE -->

                    <td>

                        <div class="d-flex
                                    align-items-center
                                    gap-3">

                            <div class="employee-avatar">

                                {{ strtoupper(
                                    substr(
                                        $employee->name,
                                        0,
                                        1
                                    )
                                ) }}

                            </div>

                            <div>

                                <div class="employee-name">
    {{ $employee->name }}
</div>

<div class="employee-meta">
    Employee
</div>
                            </div>

                        </div>

                    </td>


                    <!-- DEPARTMENT -->

                    <td>

                        {{ $employee->department ?: '—' }}

                    </td>


                    <!-- DESIGNATION -->

                    <td>

                        {{ $employee->designation ?: '—' }}

                    </td>


                    <!-- STATUS -->

                    <td>

                        @if($employee->status === 'Active')

                            <span class="badge
             rounded-pill
             bg-secondary-subtle
             text-secondary
             border
             border-secondary-subtle
             px-3 py-2
             status-badge">

                                ✓ Active

                            </span>

                        @else

                            <span class="badge
                                         rounded-pill
                                         bg-secondary-subtle
                                         text-secondary
                                         border
                                         border-secondary-subtle
                                         px-3 py-2">

                                ⏸ Inactive

                            </span>

                        @endif

                    </td>


                    <!-- FACE STATUS -->

                    <td>

                        @if($employee->face_descriptor)

                            <span class="badge
                                         rounded-pill
                                         bg-success-subtle
                                         text-success
                                         border
                                         border-success-subtle
                                         px-3 py-2
                                         face-badge">

                                ✓ Registered

                            </span>

                        @else

                            <span class="badge
                                         rounded-pill
                                         bg-warning-subtle
                                         text-warning-emphasis
                                         border
                                         border-warning-subtle
                                         px-3 py-2
                                         face-badge">

                                ⚠ Not Registered

                            </span>

                        @endif

                    </td>


                    <!-- ACTIONS -->

                    <td class="text-end px-4">

                        <div class="action-group">
                            <!-- REGISTER / UPDATE FACE -->

                            <button
                                type="button"
                                class="btn btn-sm btn-outline-success action-button"
                                onclick="openFaceRegistration(
                                    {{ $employee->id }},
                                    @js($employee->name),
                                    @js($employee->employee_code),
                                    {{ $employee->face_descriptor ? 'true' : 'false' }}
                                )"
                            >

                                @if($employee->face_descriptor)

                                    📷 Update Face

                                @else

                                    📷 Register Face

                                @endif

                            </button>


                            <!-- REMOVE FACE -->

                            @if($employee->face_descriptor)

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'employees.face.remove',
                                        $employee
                                    ) }}"
                                    onsubmit="return confirm(
                                        'Are you sure you want to remove the registered face for {{ addslashes($employee->name) }}?'
                                    );"
                                >

                                    @csrf

                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger action-button"
                                    >

                                        Remove Face

                                    </button>

                                </form>

                            @endif


                            <!-- EDIT -->

                            <a
                                href="{{ route(
                                    'employees.edit',
                                    $employee
                                ) }}"
                                class="btn btn-sm btn-outline-primary action-button"
                            >

                                Edit

                            </a>


                            <!-- TOGGLE STATUS -->

                            <form
                                method="POST"
                                action="{{ route(
                                    'employees.toggle-status',
                                    $employee
                                ) }}"
                            >

                                @csrf

                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-warning action-button"
                                >

                                    @if($employee->status === 'Active')

                                        Disable

                                    @else

                                        Activate

                                    @endif

                                </button>

                            </form>


                            <!-- DELETE -->

                            <form
                                method="POST"
                                action="{{ route(
                                    'employees.destroy',
                                    $employee
                                ) }}"
                                onsubmit="return confirm(
                                    'Are you sure you want to delete this employee?'
                                );"
                            >

                                @csrf

                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="btn btn-sm btn-outline-danger action-button"
                                >

                                    Delete

                                </button>

                            </form>

                        </div>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7"
    class="text-center empty-state">

                        <div class="text-muted">

    <div class="empty-state-icon">
        👥
    </div>

                            <h5 class="fw-bold">
                                No employees found
                            </h5>

                            <p class="text-muted mb-3">
                                Add your first employee
                                to get started.
                            </p>

                            <a
                                href="{{ route('employees.create') }}"
                                class="btn btn-primary"
                            >

                                + Add Employee

                            </a>

                        </div>

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>

</div>


<!-- FOOTER -->

<div class="text-center page-footer mt-4 mb-3">

    Employee Management
    <span class="mx-2">•</span>
    Goodman Fielder Attendance System

</div>


</div>

<!-- ============================================================= -->

<!-- FACE REGISTRATION BACKDROP -->

<!-- ============================================================= -->

<div
    id="faceRegistrationBackdrop"
    class="face-modal-backdrop"
    style="display:none;"
    onclick="closeFaceRegistration()"
></div>

<!-- ============================================================= -->

<!-- FACE REGISTRATION MODAL -->

<!-- ============================================================= -->

<div
    class="modal fade"
    id="faceRegistrationModal"
    tabindex="-1"
    aria-labelledby="faceRegistrationModalLabel"
    aria-hidden="true"
>


<div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content border-0 shadow-lg">


        <!-- MODAL HEADER -->

        <div class="modal-header">

            <div>

                <h5
                    class="modal-title fw-bold"
                    id="faceRegistrationModalLabel"
                >

                    📷 Register Employee Face

                </h5>

                <div
                    id="faceRegistrationEmployee"
                    class="face-registration-employee mt-1"
                >

                    Employee

                </div>

            </div>


            <button
                type="button"
                class="btn-close"
                aria-label="Close"
                onclick="closeFaceRegistration()"
            ></button>

        </div>


        <!-- MODAL BODY -->

        <div class="modal-body">

            <!-- STATUS -->

            <div
                id="faceRegistrationStatus"
                class="alert alert-info text-center face-status"
                role="alert"
            >

                Starting camera...

            </div>


            <!-- CAMERA -->

            <div class="face-camera-wrapper">

                <video
    id="faceRegistrationVideo"
    autoplay
    muted
    playsinline
></video>

                <div class="face-camera-overlay"></div>

            </div>


            <!-- CAPTURE -->

            <div class="text-center mt-4">

                <button
                    type="button"
                    id="captureFaceButton"
                    class="btn btn-success btn-lg px-5"
                    disabled
                    onclick="captureEmployeeFace()"
                >

                    📸 Capture & Register Face

                </button>

            </div>


            <!-- HELP -->

            <div class="text-center text-muted face-help mt-3">

                <div>
                    Look directly at the camera.
                </div>

                <div>
                    Make sure your whole face is visible.
                </div>

                <div>
                    Only one person should be visible in the camera.
                </div>

            </div>

        </div>


        <!-- MODAL FOOTER -->

        <div class="modal-footer">

            <button
                type="button"
                class="btn btn-secondary"
                onclick="closeFaceRegistration()"
            >

                Cancel

            </button>

        </div>

    </div>

</div>

</div>

<!-- ============================================================= -->

<!-- FACE REGISTRATION JAVASCRIPT -->

<!-- ============================================================= -->

<script>

    /*
    |--------------------------------------------------------------------------
    | VARIABLES
    |--------------------------------------------------------------------------
    */

    let faceRegistrationStream = null;

    let faceRegistrationEmployeeId = null;

    let faceRegistrationRunning = false;

    let faceRegistrationDetection = null;

    let faceRegistrationModelsLoaded = false;

    let faceRegistrationDetectionBusy = false;


    /*
    |--------------------------------------------------------------------------
    | OPEN FACE REGISTRATION
    |--------------------------------------------------------------------------
    */

    async function openFaceRegistration(
        employeeId,
        employeeName,
        employeeCode,
        alreadyRegistered
    ) {

        console.log(
            'Opening face registration:',
            employeeId,
            employeeName,
            employeeCode,
            alreadyRegistered
        );


        faceRegistrationEmployeeId = employeeId;

        faceRegistrationDetection = null;


        const employeeElement =
            document.getElementById(
                'faceRegistrationEmployee'
            );


        const status =
            document.getElementById(
                'faceRegistrationStatus'
            );


        const captureButton =
            document.getElementById(
                'captureFaceButton'
            );


        if (!employeeElement ||
            !status ||
            !captureButton
        ) {

            console.error(
                'Face registration HTML elements not found.'
            );

            return;

        }


        employeeElement.textContent =
            employeeName + ' • ' + employeeCode;


        captureButton.disabled = true;

        captureButton.innerHTML =
            '📸 Capture & Register Face';


        status.className =
            'alert alert-info text-center face-status';


        status.textContent =
            'Loading face recognition...';


        /*
        |--------------------------------------------------------------------------
        | SHOW MODAL
        |--------------------------------------------------------------------------
        */

        showFaceRegistrationModal();


        /*
        |--------------------------------------------------------------------------
        | START FACE REGISTRATION
        |--------------------------------------------------------------------------
        */

        try {

    await startFaceRegistrationCamera();

} catch (error) {

            console.error(
                'Face registration failed:',
                error
            );


            status.className =
                'alert alert-danger text-center face-status';


            status.textContent =
                'Unable to start face registration: ' +
                error.message;


            captureButton.disabled = true;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SHOW MODAL
    |--------------------------------------------------------------------------
    */

    function showFaceRegistrationModal() {

        const modal =
            document.getElementById(
                'faceRegistrationModal'
            );


        const backdrop =
            document.getElementById(
                'faceRegistrationBackdrop'
            );


        if (!modal) {

            console.error(
                'Face registration modal not found.'
            );

            return;

        }


        /*
        |----------------------------------------------------------------------
        | Show backdrop
        |----------------------------------------------------------------------
        */

        if (backdrop) {

            backdrop.style.display = 'block';

        }


        /*
        |----------------------------------------------------------------------
        | Show modal
        |----------------------------------------------------------------------
        */

        modal.style.display = 'block';

        modal.classList.add('show');

        modal.classList.add('face-modal-open');

        modal.removeAttribute('aria-hidden');

        modal.setAttribute(
            'aria-modal',
            'true'
        );

        modal.setAttribute(
            'role',
            'dialog'
        );


        document.body.classList.add(
            'modal-open'
        );


        /*
        |----------------------------------------------------------------------
        | Prevent background scrolling
        |----------------------------------------------------------------------
        */

        document.body.style.paddingRight =
            getScrollbarWidth() + 'px';

    }


    /*
    |--------------------------------------------------------------------------
    | GET SCROLLBAR WIDTH
    |--------------------------------------------------------------------------
    */

    function getScrollbarWidth() {

        return Math.max(
            0,
            window.innerWidth -
            document.documentElement.clientWidth
        );

    }


    /*
    |--------------------------------------------------------------------------
    | LOAD FACE API MODELS
    |--------------------------------------------------------------------------
    */

    async function loadFaceRegistrationModels() {

        if (faceRegistrationModelsLoaded) {

            return;

        }


        /*
        |----------------------------------------------------------------------
        | CHECK LIBRARY
        |----------------------------------------------------------------------
        */

        if (typeof faceapi === 'undefined') {

            throw new Error(
                'Face recognition library could not be loaded.'
            );

        }


        console.log(
            'Loading face recognition models...'
        );


        /*
        |----------------------------------------------------------------------
        | Tiny Face Detector
        |----------------------------------------------------------------------
        */

        await faceapi.nets.tinyFaceDetector.loadFromUri(
            '/models'
        );


        /*
        |----------------------------------------------------------------------
        | Face Landmarks
        |----------------------------------------------------------------------
        */

        await faceapi.nets.faceLandmark68Net.loadFromUri(
            '/models'
        );


        /*
        |----------------------------------------------------------------------
        | Face Recognition
        |----------------------------------------------------------------------
        */

        await faceapi.nets.faceRecognitionNet.loadFromUri(
            '/models'
        );


        faceRegistrationModelsLoaded = true;


        console.log(
            'Face recognition models loaded successfully.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | START CAMERA
    |--------------------------------------------------------------------------
    */

    async function startFaceRegistrationCamera() {

    const video =
        document.getElementById(
            'faceRegistrationVideo'
        );

    if (!video) {

        throw new Error(
            'Face registration video element was not found.'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Stop previous registration camera
    |--------------------------------------------------------------------------
    */

    if (faceRegistrationStream) {

        faceRegistrationStream
            .getTracks()
            .forEach(
                track => track.stop()
            );

        faceRegistrationStream = null;

    }

    /*
    |--------------------------------------------------------------------------
    | Check camera support
    |--------------------------------------------------------------------------
    */

    if (
        !navigator.mediaDevices ||
        !navigator.mediaDevices.getUserMedia
    ) {

        throw new Error(
            'Camera access is not supported by this browser.'
        );

    }

    /*
    |--------------------------------------------------------------------------
    | Start camera
    |--------------------------------------------------------------------------
    */

    faceRegistrationStream =
        await navigator.mediaDevices.getUserMedia({

            video: {
                facingMode: 'user',

                width: {
                    ideal: 640
                },

                height: {
                    ideal: 480
                }
            },

            audio: false

        });

    /*
    |--------------------------------------------------------------------------
    | Attach camera to registration video
    |--------------------------------------------------------------------------
    */

    video.srcObject =
        faceRegistrationStream;

    /*
    |--------------------------------------------------------------------------
    | Make sure browser displays video
    |--------------------------------------------------------------------------
    */

    video.autoplay = true;
    video.muted = true;
    video.playsInline = true;

    video.style.display = 'block';

    /*
    |--------------------------------------------------------------------------
    | Wait for video metadata
    |--------------------------------------------------------------------------
    */

    await new Promise((resolve) => {

        if (video.readyState >= 2) {

            resolve();

            return;

        }

        video.onloadedmetadata = function () {

            resolve();

        };

    });

    /*
    |--------------------------------------------------------------------------
    | Start video
    |--------------------------------------------------------------------------
    */

    await video.play();

    /*
    |--------------------------------------------------------------------------
    | Confirm video dimensions
    |--------------------------------------------------------------------------
    */

    console.log(
        'Registration camera:',
        video.videoWidth,
        'x',
        video.videoHeight
    );

    /*
    |--------------------------------------------------------------------------
    | Start registration
    |--------------------------------------------------------------------------
    */

    faceRegistrationRunning = true;

    const status =
        document.getElementById(
            'faceRegistrationStatus'
        );

    if (status) {

        status.className =
            'alert alert-warning text-center face-status';

        status.textContent =
            'Camera ready. Checking your face...';

    }

    /*
    |--------------------------------------------------------------------------
    | Load face models
    |--------------------------------------------------------------------------
    */

    await loadFaceRegistrationModels();

    /*
    |--------------------------------------------------------------------------
    | Start detection
    |--------------------------------------------------------------------------
    */

    detectFaceForRegistration();

} 


    /*
    |--------------------------------------------------------------------------
    | FACE DETECTION
    |--------------------------------------------------------------------------
    */

    async function detectFaceForRegistration() {

        if (!faceRegistrationRunning) {

            return;

        }


        /*
        |----------------------------------------------------------------------
        | PREVENT OVERLAPPING DETECTIONS
        |----------------------------------------------------------------------
        */

        if (faceRegistrationDetectionBusy) {

            setTimeout(
                detectFaceForRegistration,
                200
            );

            return;

        }


        const video =
            document.getElementById(
                'faceRegistrationVideo'
            );


        if (
            !video ||
            video.readyState < 2
        ) {

            setTimeout(
                detectFaceForRegistration,
                200
            );

            return;

        }


        faceRegistrationDetectionBusy = true;


        try {

            /*
            |------------------------------------------------------------------
            | DETECT ALL FACES
            |------------------------------------------------------------------
            */

            const detections =
                await faceapi
                    .detectAllFaces(
                        video,
                        new faceapi.TinyFaceDetectorOptions({

                            inputSize: 320,

                            scoreThreshold: 0.5

                        })
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptors();


            const status =
                document.getElementById(
                    'faceRegistrationStatus'
                );


            const captureButton =
                document.getElementById(
                    'captureFaceButton'
                );


            if (!status ||
                !captureButton
            ) {

                return;

            }


            /*
            |------------------------------------------------------------------
            | NO FACE
            |------------------------------------------------------------------
            */

            if (detections.length === 0) {

                faceRegistrationDetection = null;


                status.className =
                    'alert alert-warning text-center face-status';


                status.textContent =
                    'No face detected. Look directly at the camera.';


                captureButton.disabled = true;

            }


            /*
            |------------------------------------------------------------------
            | MORE THAN ONE FACE
            |------------------------------------------------------------------
            */

            else if (detections.length > 1) {

                faceRegistrationDetection = null;


                status.className =
                    'alert alert-danger text-center face-status';


                status.textContent =
                    'Multiple faces detected. Please make sure only one person is in the camera.';


                captureButton.disabled = true;

            }


            /*
            |------------------------------------------------------------------
            | EXACTLY ONE FACE
            |------------------------------------------------------------------
            */

            else {

                const detection =
                    detections[0];


                faceRegistrationDetection =
                    detection;


                const box =
                    detection.detection.box;


                const videoWidth =
                    video.videoWidth || 640;


                const videoHeight =
                    video.videoHeight || 480;


                const faceWidthRatio =
                    box.width / videoWidth;


                const faceHeightRatio =
                    box.height / videoHeight;


                /*
                |------------------------------------------------------------------
                | FACE TOO SMALL
                |------------------------------------------------------------------
                */

                if (
                    faceWidthRatio < 0.15 ||
                    faceHeightRatio < 0.15
                ) {

                    status.className =
                        'alert alert-warning text-center face-status';


                    status.textContent =
                        'Face detected, but you are too far away. Please move closer.';


                    captureButton.disabled = true;

                }


                /*
                |------------------------------------------------------------------
                | FACE READY
                |------------------------------------------------------------------
                */

                else {

                    status.className =
                        'alert alert-success text-center face-status';


                    status.textContent =
                        '✓ One clear face detected. Ready to register.';


                    captureButton.disabled = false;

                }

            }

        } catch (error) {

            console.error(
                'Face detection error:',
                error
            );

        }


        faceRegistrationDetectionBusy =
            false;


        /*
        |----------------------------------------------------------------------
        | CONTINUE DETECTION
        |----------------------------------------------------------------------
        */

        if (faceRegistrationRunning) {

            setTimeout(
                detectFaceForRegistration,
                250
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CAPTURE EMPLOYEE FACE
    |--------------------------------------------------------------------------
    */

    async function captureEmployeeFace() {

    /*
    |----------------------------------------------------------------------
    | BASIC CHECKS
    |----------------------------------------------------------------------
    */

    if (
        !faceRegistrationDetection ||
        !faceRegistrationEmployeeId
    ) {

        console.warn(
            'Face capture attempted without valid detection or employee.'
        );

        return;

    }


    const button =
        document.getElementById(
            'captureFaceButton'
        );


    const status =
        document.getElementById(
            'faceRegistrationStatus'
        );


    /*
    |----------------------------------------------------------------------
    | DISABLE BUTTON
    |----------------------------------------------------------------------
    */

    button.disabled = true;

    button.innerHTML =
        '⏳ Registering Face...';


    status.className =
        'alert alert-info text-center face-status';


    status.textContent =
        'Saving face registration...';


    /*
    |----------------------------------------------------------------------
    | GET DESCRIPTOR
    |----------------------------------------------------------------------
    */

    const descriptor =
        Array.from(
            faceRegistrationDetection.descriptor
        );


    /*
    |----------------------------------------------------------------------
    | CHECK DESCRIPTOR
    |----------------------------------------------------------------------
    */

    if (descriptor.length !== 128) {

        status.className =
            'alert alert-danger text-center face-status';


        status.textContent =
            'Invalid face descriptor. Please try again.';


        button.disabled = false;

        button.innerHTML =
            '📸 Capture & Register Face';

        return;

    }


   




        /*
        |----------------------------------------------------------------------
        | SEND TO LARAVEL
        |----------------------------------------------------------------------
        */

        try {

            const response =
                await fetch(
                    `/employees/${faceRegistrationEmployeeId}/face`,
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

                        body: JSON.stringify({

    face_descriptor:
        JSON.stringify(descriptor)

})

                    }
                );


            /*
            |------------------------------------------------------------------
            | READ RESPONSE
            |------------------------------------------------------------------
            */

            let data = {};


            try {

                data =
                    await response.json();

            } catch (jsonError) {

                console.warn(
                    'Could not read JSON response.',
                    jsonError
                );

                data = {};

            }


            /*
            |------------------------------------------------------------------
            | SERVER ERROR
            |------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Face registration failed.'
                );

            }


            /*
            |------------------------------------------------------------------
            | SUCCESS
            |------------------------------------------------------------------
            */

            status.className =
                'alert alert-success text-center face-status';


            status.textContent =
                '✓ Face registered successfully!';


            button.innerHTML =
                '✓ Registered';


            /*
            |------------------------------------------------------------------
            | STOP CAMERA
            |------------------------------------------------------------------
            */

            stopFaceRegistrationCamera();


            /*
            |------------------------------------------------------------------
            | CLOSE AND RELOAD
            |------------------------------------------------------------------
            */

            setTimeout(
                function () {

                    closeFaceRegistration();

                    window.location.reload();

                },
                1200
            );


        } catch (error) {

            console.error(
                'Registration error:',
                error
            );


            status.className =
                'alert alert-danger text-center face-status';


            status.textContent =
                error.message ||
                'Face registration failed.';


            button.disabled = false;

            button.innerHTML =
                '📸 Capture & Register Face';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | STOP CAMERA
    |--------------------------------------------------------------------------
    */

    function stopFaceRegistrationCamera() {

        faceRegistrationRunning = false;

        faceRegistrationDetection = null;

        faceRegistrationDetectionBusy = false;


        if (faceRegistrationStream) {

            faceRegistrationStream
                .getTracks()
                .forEach(
                    track => track.stop()
                );

            faceRegistrationStream = null;

        }


        const video =
            document.getElementById(
                'faceRegistrationVideo'
            );


        if (video) {

            video.pause();

            video.srcObject = null;

        }


        console.log(
            'Face registration camera stopped.'
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE FACE REGISTRATION
    |--------------------------------------------------------------------------
    */

    function closeFaceRegistration() {

        console.log(
            'Closing face registration.'
        );


        stopFaceRegistrationCamera();


        faceRegistrationEmployeeId =
            null;


        const button =
            document.getElementById(
                'captureFaceButton'
            );


        if (button) {

            button.disabled = true;

            button.innerHTML =
                '📸 Capture & Register Face';

        }


        const modal =
            document.getElementById(
                'faceRegistrationModal'
            );


        const backdrop =
            document.getElementById(
                'faceRegistrationBackdrop'
            );


        /*
        |----------------------------------------------------------------------
        | Hide modal
        |----------------------------------------------------------------------
        */

        if (modal) {

            modal.classList.remove(
                'show'
            );

            modal.classList.remove(
                'face-modal-open'
            );

            modal.style.display =
                'none';

            modal.setAttribute(
                'aria-hidden',
                'true'
            );

            modal.removeAttribute(
                'aria-modal'
            );

            modal.removeAttribute(
                'role'
            );

        }


        /*
        |----------------------------------------------------------------------
        | Hide backdrop
        |----------------------------------------------------------------------
        */

        if (backdrop) {

            backdrop.style.display =
                'none';

        }


        /*
        |----------------------------------------------------------------------
        | Restore body
        |----------------------------------------------------------------------
        */

        document.body.classList.remove(
            'modal-open'
        );

        document.body.style.paddingRight =
            '';

    }


    /*
    |--------------------------------------------------------------------------
    | ESC KEY
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {

            if (
                event.key === 'Escape'
            ) {

                const modal =
                    document.getElementById(
                        'faceRegistrationModal'
                    );


                if (
                    modal &&
                    modal.classList.contains(
                        'face-modal-open'
                    )
                ) {

                    closeFaceRegistration();

                }

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | PAGE LOAD
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        function () {

            console.log(
                'Employee Management face registration ready.'
            );


            /*
            |------------------------------------------------------------------
            | Make sure modal is initially hidden
            |------------------------------------------------------------------
            */

            const modal =
                document.getElementById(
                    'faceRegistrationModal'
                );


            if (modal) {

                modal.style.display =
                    'none';

            }

        }
    );

</script>

</body>

</html>
