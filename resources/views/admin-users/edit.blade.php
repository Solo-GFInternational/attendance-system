
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User | Attendance Management System</title>

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
            max-width: 750px;
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

        .account-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            background: #e9f2ff;
            color: #0d6efd;
        }

        .form-check-input {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .form-check-label {
            margin-left: 5px;
            font-weight: 600;
            cursor: pointer;
        }
    </style>
</head>

<body>
<div class="container-fluid px-4 py-4">

    {{-- NAVIGATION --}}
    <div class="navbar-custom px-3 py-2 mb-4">
        <div class="d-flex flex-wrap align-items-center gap-2">

            <a href="{{ route('dashboard') }}" class="nav-link">
                📊 Dashboard
            </a>

            <a href="{{ route('attendance.employee') }}" class="nav-link">
                🕘 Attendance
            </a>

            <a href="{{ route('attendance.history') }}" class="nav-link">
                📋 History
            </a>

            <a href="{{ route('attendance.outings') }}" class="nav-link">
                🚶 Outings
            </a>

            <a href="{{ route('employees.index') }}" class="nav-link">
                👥 Employees
            </a>

            <a href="{{ route('visitors.index') }}" class="nav-link">
                🚪 Visitors
            </a>

            <a href="{{ route('attendance.emergency') }}" class="nav-link">
                🚨 Emergency
            </a>

            @if(auth()->user()?->is_admin)
                <a href="{{ route('admin-users.index') }}"
                   class="nav-link active">
                    👤 Admin Users
                </a>
            @endif

            <a href="{{ route('profile.edit') }}" class="nav-link">
                ⚙️ Profile
            </a>

            <div class="ms-auto">
                <form method="POST" action="{{ route('logout') }}">
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
            <div class="d-flex justify-content-between align-items-start mb-4 gap-3">
                <div>
                    <h2 class="fw-bold mb-1">Edit User Account</h2>

                    <p class="text-muted mb-2">
                        Update account details, permissions or password.
                    </p>

                    @if($user->is_admin)
                        <span class="account-badge">
                            👑 Administrator Account
                        </span>
                    @else
                        <span class="account-badge">
                            👤 Regular User Account
                        </span>
                    @endif
                </div>

                <a href="{{ route('admin-users.index') }}"
                   class="btn btn-outline-secondary flex-shrink-0">
                    ← Back
                </a>
            </div>

            {{-- INFORMATION --}}
            <div class="info-box mb-4">
                <div class="fw-semibold mb-1">
                    🔐 Account Security
                </div>

                <div class="text-muted small">
                    Leave both password fields blank to keep the current
                    password. Administrator access grants elevated
                    permissions. Only grant it to trusted users.
                </div>
            </div>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Please correct the following:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- EDIT FORM --}}
            <form method="POST"
                  action="{{ route('admin-users.update', ['user' => $user->id]) }}">

                @csrf
                @method('PUT')

                {{-- NAME --}}
                <div class="mb-4">
                    <label for="name" class="form-label">
                        Full Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user->name) }}"
                        placeholder="Enter full name"
                        required
                        autofocus
                    >

                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- EMAIL --}}
                <div class="mb-4">
                    <label for="email" class="form-label">
                        Email Address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user->email) }}"
                        placeholder="Enter email address"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- NEW PASSWORD --}}
                <div class="mb-4">
                    <label for="password" class="form-label">
                        New Password
                        <span class="text-muted fw-normal">(optional)</span>
                    </label>

                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter a new password"
                            autocomplete="new-password"
                            minlength="8"
                        >

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password', this)"
                            title="Show password"
                            aria-label="Show or hide new password"
                        >👁</button>
                    </div>

                    <div class="form-text">
                        Use at least 8 characters. Leave blank to keep the
                        existing password.
                    </div>

                    @error('password')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">
                        Confirm New Password
                    </label>

                    <div class="password-wrapper">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            placeholder="Re-enter the new password"
                            autocomplete="new-password"
                            minlength="8"
                        >

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password_confirmation', this)"
                            title="Show password"
                            aria-label="Show or hide password confirmation"
                        >👁</button>
                    </div>
                </div>

                {{-- ADMINISTRATOR PERMISSIONS --}}
                <div class="info-box mb-4">
                    <div class="form-check">
                        <input
                            type="checkbox"
                            id="is_admin"
                            name="is_admin"
                            value="1"
                            class="form-check-input"
                            {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                        >

                        <label for="is_admin" class="form-check-label">
                            Administrator Access
                        </label>
                    </div>

                    <div class="text-muted small mt-2">
                        Tick this option to grant administrator privileges.
                        Untick it to make the account a regular user.
                    </div>
                </div>

                {{-- BUTTONS --}}
                <div class="d-flex flex-wrap justify-content-end gap-2">
                    <a href="{{ route('admin-users.index') }}"
                       class="btn btn-outline-secondary px-4">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary px-4">
                        💾 Save Changes
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
    function togglePassword(fieldId, button) {
        const field = document.getElementById(fieldId);

        if (field.type === 'password') {
            field.type = 'text';
            button.textContent = '🙈';
            button.title = 'Hide password';
        } else {
            field.type = 'password';
            button.textContent = '👁';
            button.title = 'Show password';
        }
    }
</script>

</body>
</html>
