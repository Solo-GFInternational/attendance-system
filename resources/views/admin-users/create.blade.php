<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Admin | Attendance Management System</title>

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

        .form-card {
            max-width: 700px;
            margin: 35px auto;
            border: none;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.06);
        }

        .form-control {
            min-height: 48px;
            border-radius: 10px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
        }

        .form-label {
            font-weight: 600;
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper .form-control {
            padding-right: 55px;
        }

        .show-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6c757d;
            cursor: pointer;
            font-size: 18px;
        }

        .info-box {
            background: #f0f7ff;
            border: 1px solid #d6e8ff;
            border-radius: 12px;
            padding: 15px;
        }

        .alert {
            border-radius: 10px;
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


    {{-- FORM CARD --}}

    <div class="card form-card">

        <div class="card-body p-4 p-md-5">


            {{-- HEADER --}}

            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>

                    <h2 class="fw-bold mb-1">
                        Create Administrator
                    </h2>

                    <p class="text-muted mb-0">
                        Create a new administrator account.
                    </p>

                </div>


                <a href="{{ route('admin-users.index') }}"
                   class="btn btn-outline-secondary">

                    ← Back

                </a>

            </div>


            {{-- INFORMATION --}}

            <div class="info-box mb-4">

                <div class="fw-semibold mb-1">
                    🔐 Administrator Access
                </div>

                <div class="text-muted small">

                    This account will be able to access the
                    Attendance Management System administration area.

                </div>

            </div>


            {{-- VALIDATION ERRORS --}}

            @if ($errors->any())

                <div class="alert alert-danger mb-4">

                    <strong>
                        Please correct the following:
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- FORM --}}

            <form method="POST"
                  action="{{ route('admin-users.store') }}">

                @csrf


                {{-- NAME --}}

                <div class="mb-4">

                    <label for="name"
                           class="form-label">

                        Full Name

                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        placeholder="Enter administrator name"
                        required
                        autofocus>

                    @error('name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- EMAIL --}}

                <div class="mb-4">

                    <label for="email"
                           class="form-label">

                        Email Address

                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email') }}"
                        placeholder="Enter administrator email"
                        required>

                    @error('email')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- PASSWORD --}}

                <div class="mb-4">

                    <label for="password"
                           class="form-label">

                        Password

                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter password"
                            required>

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password', this)"
                            title="Show password">

                            👁

                        </button>

                    </div>

                    <div class="form-text">
                        Password must be at least 8 characters.
                    </div>

                    @error('password')

                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                {{-- CONFIRM PASSWORD --}}

                <div class="mb-4">

                    <label for="password_confirmation"
                           class="form-label">

                        Confirm Password

                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Confirm password"
                            required>

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password_confirmation', this)"
                            title="Show password">

                            👁

                        </button>

                    </div>

                </div>


                {{-- BUTTONS --}}

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('admin-users.index') }}"
                       class="btn btn-outline-secondary px-4">

                        Cancel

                    </a>

                    <button type="submit"
                            class="btn btn-primary px-4">

                        👤 Create Administrator

                    </button>

                </div>

            </form>

        </div>

    </div>


    <div class="text-center text-muted small mt-4">

        Attendance Management System
        <span class="mx-2">•</span>
        Administrator Management

    </div>


</div>


<script>

function togglePassword(fieldId, button)
{
    const field = document.getElementById(fieldId);

    if (field.type === 'password') {

        field.type = 'text';

        button.textContent = '🙈';

    } else {

        field.type = 'password';

        button.textContent = '👁';

    }
}

</script>

</body>

</html>