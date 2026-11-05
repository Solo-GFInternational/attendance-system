<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Registration | Attendance Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9eef5 100%);
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .register-card {
            width: 100%;
            max-width: 470px;
            border: none;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.10);
            overflow: hidden;
        }

        .register-header {
            background: #212529;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .register-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .register-header h2 {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .register-header p {
            margin: 0;
            opacity: 0.75;
            font-size: 14px;
        }

        .register-body {
            padding: 32px 35px 30px;
            background: white;
        }

        .form-label {
            font-weight: 600;
            font-size: 14px;
        }

        .form-control {
            min-height: 48px;
            border-radius: 10px;
            padding-left: 15px;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12);
        }

        .register-button {
            min-height: 48px;
            border-radius: 10px;
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

        .register-footer {
            text-align: center;
            padding-top: 20px;
            font-size: 13px;
            color: #6c757d;
        }

        .alert {
            border-radius: 10px;
            font-size: 14px;
        }

    </style>

</head>

<body>

<div class="register-wrapper">

    <div class="card register-card">

        {{-- HEADER --}}
        <div class="register-header">

            <div class="register-icon">
                👤
            </div>

            <h2>
                Create Admin Account
            </h2>

            <p>
                Attendance Management System
            </p>

        </div>


        {{-- BODY --}}
        <div class="register-body">

            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())

                <div class="alert alert-danger mb-4">

                    <strong>
                        Please check the following:
                    </strong>

                    <ul class="mb-0 mt-2 ps-3">

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form method="POST"
                  action="{{ route('register') }}">

                @csrf


                {{-- NAME --}}
                <div class="mb-4">

                    <label for="name"
                           class="form-label">

                        Full Name

                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Enter full name">

                </div>


                {{-- EMAIL --}}
                <div class="mb-4">

                    <label for="email"
                           class="form-label">

                        Email Address

                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email') }}"
                        required
                        autocomplete="username"
                        placeholder="Enter email address">

                </div>


                {{-- PASSWORD --}}
                <div class="mb-4">

                    <label for="password"
                           class="form-label">

                        Password

                    </label>

                    <div class="password-wrapper">

                        <input
                            id="password"
                            type="password"
                            name="password"
                            class="form-control"
                            required
                            autocomplete="new-password"
                            placeholder="Create a password">

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password', this)"
                            title="Show password">

                            👁

                        </button>

                    </div>

                </div>


                {{-- CONFIRM PASSWORD --}}
                <div class="mb-4">

                    <label for="password_confirmation"
                           class="form-label">

                        Confirm Password

                    </label>

                    <div class="password-wrapper">

                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required
                            autocomplete="new-password"
                            placeholder="Confirm your password">

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword('password_confirmation', this)"
                            title="Show password">

                            👁

                        </button>

                    </div>

                </div>


                {{-- ACTIONS --}}
                <div class="d-flex justify-content-between align-items-center">

                    <a href="{{ route('login') }}"
                       class="text-decoration-none">

                        ← Back to Login

                    </a>


                    <button
                        type="submit"
                        class="btn btn-primary register-button px-4">

                        Create Account

                    </button>

                </div>

            </form>


            <div class="register-footer">

                <div>
                    Secure Administrator Access
                </div>

                <div class="mt-1">
                    Attendance Management System
                </div>

            </div>

        </div>

    </div>

</div>


<script>

function togglePassword(fieldId, button)
{
    const password = document.getElementById(fieldId);

    if (password.type === 'password') {

        password.type = 'text';

        button.textContent = '🙈';

    } else {

        password.type = 'password';

        button.textContent = '👁';

    }
}

</script>

</body>

</html>