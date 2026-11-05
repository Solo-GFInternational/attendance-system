<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Login | Attendance Management System</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa 0%, #e9eef5 100%);
        }

        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .login-card {
            width: 100%;
            max-width: 440px;
            border: none;
            border-radius: 18px;
            box-shadow: 0 15px 45px rgba(0, 0, 0, 0.10);
            overflow: hidden;
        }

        .login-header {
            background: #212529;
            color: white;
            padding: 35px 30px;
            text-align: center;
        }

        .login-icon {
            width: 75px;
            height: 75px;
            margin: 0 auto 18px;
            border-radius: 50%;
            background: rgba(255,255,255,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 34px;
        }

        .login-header h2 {
            font-weight: 700;
            margin-bottom: 6px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.75;
            font-size: 14px;
        }

        .login-body {
            padding: 35px 35px 30px;
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

        .login-button {
            min-height: 48px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }

        .login-footer {
            text-align: center;
            padding-top: 20px;
            font-size: 13px;
            color: #6c757d;
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

        .alert {
            border-radius: 10px;
            font-size: 14px;
        }

    </style>

</head>

<body>

<div class="login-wrapper">

    <div class="card login-card">

        {{-- HEADER --}}
        <div class="login-header">

            <div class="login-icon">
                🔐
            </div>

            <h2>
                GFI
            </h2>

            <p>
                Attendance Management System
            </p>

        </div>


        {{-- BODY --}}
        <div class="login-body">

            {{-- SESSION STATUS --}}
            @if (session('status'))

                <div class="alert alert-success mb-4">

                    {{ session('status') }}

                </div>

            @endif


            {{-- VALIDATION ERRORS --}}
            @if ($errors->any())

                <div class="alert alert-danger mb-4">

                    <strong>
                        Login failed
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
                  action="{{ route('login') }}">

                @csrf


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
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email address">

                </div>


                {{-- PASSWORD --}}
                <div class="mb-3">

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
                            autocomplete="current-password"
                            placeholder="Enter your password">

                        <button
                            type="button"
                            class="show-password"
                            onclick="togglePassword()"
                            title="Show password">

                            👁

                        </button>

                    </div>

                </div>


                {{-- REMEMBER ME --}}
                <div class="form-check mb-4">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        name="remember"
                        id="remember_me">

                    <label
                        class="form-check-label"
                        for="remember_me">

                        Remember me

                    </label>

                </div>


                {{-- LOGIN --}}
                <button
                    type="submit"
                    class="btn btn-primary login-button">

                    🔐 Sign In

                </button>


                {{-- FORGOT PASSWORD --}}
                @if (Route::has('password.request'))

                    <div class="text-center mt-3">

                        <a href="{{ route('password.request') }}"
                           class="text-decoration-none">

                            Forgot your password?

                        </a>

                    </div>

                @endif

            </form>


            <div class="login-footer">

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

function togglePassword()
{
    const password = document.getElementById('password');

    const button = document.querySelector('.show-password');

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