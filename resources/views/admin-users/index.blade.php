<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Users | Attendance Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        body {
            background: #f5f7fa;
        }

        .navbar-custom {
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .nav-link {
            font-weight: 600;
            color: #495057;
            border-radius: 8px;
            padding: 9px 12px !important;
        }

        .nav-link:hover {
            background: #f1f3f5;
            color: #0d6efd;
        }

        .nav-link.active {
            background: #0d6efd;
            color: white !important;
        }

        .page-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .summary-card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
        }

        .admin-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #e7f1ff;
            color: #0d6efd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 16px;
        }

        .table th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #6c757d;
            white-space: nowrap;
        }

        .table td {
            vertical-align: middle;
        }

        .action-btn {
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
        }

        .footer {
            color: #6c757d;
            font-size: 13px;
        }

    </style>

</head>


<body>

<div class="container-fluid px-4 py-4">


    {{-- NAVIGATION --}}

    <div class="navbar-custom px-3 py-2 mb-4">

        <div class="d-flex flex-wrap align-items-center gap-2">

            <a href="{{ route('dashboard') }}"
               class="nav-link">

                📊 Dashboard

            </a>

            <a href="{{ route('sites.index') }}"
   class="nav-link">

    🏢 Sites

</a>


            <a href="{{ route('attendance.employee') }}"
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
               class="nav-link">

                👥 Employees

            </a>


            <a href="{{ route('visitors.index') }}"
               class="nav-link">

                🚪 Visitors

            </a>


            <a href="{{ route('attendance.emergency') }}"
               class="nav-link">

                🚨 Emergency

            </a>


            <a href="{{ route('admin-users.index') }}"
               class="nav-link active">

                👤 Admin Users

            </a>


            <a href="{{ route('profile.edit') }}"
               class="nav-link">

                ⚙️ Profile

            </a>


            <div class="ms-auto">

                <form method="POST"
                      action="{{ route('logout') }}">

                    @csrf

                    <button type="submit"
                            class="btn btn-outline-danger btn-sm">

                        🚪 Logout

                    </button>

                </form>

            </div>

        </div>

    </div>


    {{-- PAGE HEADER --}}

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

        <div>

            <h2 class="fw-bold mb-1">
                Admin Users
            </h2>

            <p class="text-muted mb-0">
                Manage administrator accounts for the attendance system.
            </p>

        </div>


        <a href="{{ route('admin-users.create') }}"
           class="btn btn-primary btn-lg">

            + Create Admin

        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- ERROR MESSAGE --}}

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- SUMMARY --}}

    <div class="row g-4 mb-4">

        <div class="col-md-4">

            <div class="card summary-card">

                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Total Administrators
                    </div>

                    <h3 class="fw-bold mb-0">
                        {{ $users->count() }}
                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card summary-card">

                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Logged In Account
                    </div>

                    <h3 class="fw-bold mb-0">

                        {{ auth()->user()->name }}

                    </h3>

                </div>

            </div>

        </div>


        <div class="col-md-4">

            <div class="card summary-card">

                <div class="card-body">

                    <div class="text-muted small mb-1">
                        Access Level
                    </div>

                    <h3 class="fw-bold mb-0">
                        Administrator
                    </h3>

                </div>

            </div>

        </div>

    </div>


    {{-- ADMIN TABLE --}}

    <div class="card page-card">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th class="px-4">
                                Administrator
                            </th>

                            <th>
                                Email
                            </th>

                            <th>
                                Created
                            </th>

                            <th class="text-end px-4">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                    @forelse($users as $user)

                        <tr>

                            {{-- ADMIN --}}

                            <td class="px-4">

                                <div class="d-flex align-items-center gap-3">

                                    <div class="admin-avatar">

                                        {{ strtoupper(substr($user->name, 0, 1)) }}

                                    </div>

                                    <div>

                                        <div class="fw-semibold">

                                            {{ $user->name }}

                                            @if($user->id === auth()->id())

                                                <span class="badge bg-primary ms-1">
                                                    You
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- EMAIL --}}

                            <td>

                                {{ $user->email }}

                            </td>


                            {{-- CREATED --}}

                            <td>

                                {{ $user->created_at
                                    ? $user->created_at->format('d M Y')
                                    : '—'
                                }}

                            </td>


                            {{-- ACTIONS --}}

                            <td class="text-end px-4">

                                <div class="d-flex justify-content-end gap-2">

                                    {{-- EDIT --}}

                                    <a href="{{ route(
                                        'admin-users.edit',
                                        $user
                                    ) }}"
                                       class="btn btn-sm btn-outline-primary action-btn">

                                        ✏️ Edit

                                    </a>


                                    {{-- DELETE --}}

                                    @if($user->id !== auth()->id())

                                        <form method="POST"
                                              action="{{ route(
                                                  'admin-users.destroy',
                                                  $user
                                              ) }}"
                                              onsubmit="return confirm(
                                                  'Are you sure you want to delete this administrator account?'
                                              );">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger action-btn">

                                                🗑 Delete

                                            </button>

                                        </form>

                                    @else

                                        <button type="button"
                                                class="btn btn-sm btn-outline-secondary action-btn"
                                                disabled>

                                            🔒 Current Account

                                        </button>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4"
                                class="text-center py-5">

                                <div class="fs-1 mb-3">
                                    👤
                                </div>

                                <h5 class="fw-bold">
                                    No administrators found
                                </h5>

                                <p class="text-muted mb-3">
                                    Create an administrator account to get started.
                                </p>

                                <a href="{{ route('admin-users.create') }}"
                                   class="btn btn-primary">

                                    + Create Admin

                                </a>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    {{-- FOOTER --}}

    <div class="text-center footer mt-4">

        Attendance Management System

        <span class="mx-2">•</span>

        Administrator Management

    </div>


</div>

</body>

</html>