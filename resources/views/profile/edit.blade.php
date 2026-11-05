<x-app-layout>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1 fw-bold text-dark">
                    Profile
                </h2>
                <p class="mb-0 text-muted small">
                    Manage your account information and security settings
                </p>
            </div>
        </div>
    </x-slot>

    <div class="container-fluid py-4">

        <div class="container">

            {{-- Page Header --}}
            <div class="mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        <div class="d-flex align-items-center">

                            <div
                                class="rounded-circle d-flex align-items-center justify-content-center me-3"
                                style="
                                    width:60px;
                                    height:60px;
                                    background:#f1f5f9;
                                    font-size:26px;
                                "
                            >
                                👤
                            </div>

                            <div>
                                <h4 class="mb-1 fw-bold">
                                    {{ Auth::user()->name }}
                                </h4>

                                <div class="text-muted">
                                    {{ Auth::user()->email }}
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>


            {{-- Profile Information --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <div class="d-flex align-items-center">

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="
                                width:42px;
                                height:42px;
                                background:#eff6ff;
                                color:#2563eb;
                                font-size:18px;
                            "
                        >
                            👤
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Profile Information
                            </h5>

                            <small class="text-muted">
                                Update your name and email address
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="profile-form">

                        @include('profile.partials.update-profile-information-form')

                    </div>

                </div>

            </div>


            {{-- Password --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <div class="d-flex align-items-center">

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="
                                width:42px;
                                height:42px;
                                background:#fef3c7;
                                color:#d97706;
                                font-size:18px;
                            "
                        >
                            🔐
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold">
                                Change Password
                            </h5>

                            <small class="text-muted">
                                Keep your account secure with a strong password
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="profile-form">

                        @include('profile.partials.update-password-form')

                    </div>

                </div>

            </div>


            {{-- Delete Account --}}
            <div class="card border-0 shadow-sm mb-4">

                <div class="card-header bg-white border-bottom py-3">

                    <div class="d-flex align-items-center">

                        <div
                            class="rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="
                                width:42px;
                                height:42px;
                                background:#fef2f2;
                                color:#dc2626;
                                font-size:18px;
                            "
                        >
                            ⚠️
                        </div>

                        <div>
                            <h5 class="mb-0 fw-bold text-danger">
                                Delete Account
                            </h5>

                            <small class="text-muted">
                                Permanently delete your account and all associated data
                            </small>
                        </div>

                    </div>

                </div>

                <div class="card-body p-4">

                    <div class="profile-form">

                        @include('profile.partials.delete-user-form')

                    </div>

                </div>

            </div>

        </div>

    </div>


    <style>

        .profile-form {
            width: 100%;
        }

        .profile-form label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }

        .profile-form input {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .profile-form input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
            outline: none;
        }

        .profile-form button {
            border-radius: 7px;
            font-weight: 600;
            padding: 9px 18px;
        }

        .profile-form .text-sm {
            font-size: 13px;
        }

        @media (max-width: 768px) {

            .container {
                max-width: 100%;
            }

            .card-body {
                padding: 20px !important;
            }

        }

    </style>

</x-app-layout>