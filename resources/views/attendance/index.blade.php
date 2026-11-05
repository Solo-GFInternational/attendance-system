<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="csrf-token"
        content="{{ csrf_token() }}"
    >

    <title>Employee Attendance | Goodman Fielder</title>

    <script src="{{ asset('js/face-api/face-api.js') }}"></script>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }


        /* =========================================================
           HEADER
        ========================================================= */

        .header {
            background: #0f172a;
            color: white;
            padding: 22px 30px;
        }

        .header-inner {
            max-width: 1200px;
            margin: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .header-title {
            font-size: 25px;
            font-weight: 700;
        }

        .header-subtitle {
            margin-top: 5px;
            color: #cbd5e1;
            font-size: 14px;
        }

        .clock {
            text-align: right;
            white-space: nowrap;
        }

        .clock-time {
            font-size: 23px;
            font-weight: 700;
        }

        .clock-date {
            margin-top: 4px;
            font-size: 13px;
            color: #cbd5e1;
        }


        /* =========================================================
           MAIN
        ========================================================= */

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
        }

        .back-button {
            display: inline-block;
            background: #334155;
            color: white;
            text-decoration: none;
            padding: 11px 17px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        .back-button:hover {
            background: #1e293b;
        }


        /* =========================================================
           ALERTS
        ========================================================= */

        .alert {
            padding: 15px 18px;
            border-radius: 9px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }


        /* =========================================================
           AUTO RETURN TO MAIN MENU
        ========================================================= */

        .auto-return-box {
            display: none;
            margin-bottom: 20px;
            padding: 15px 18px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 9px;
            color: #1e40af;
            font-weight: 600;
            text-align: center;
        }

        .auto-return-box strong {
            font-size: 18px;
        }

        .auto-return-countdown {
            font-size: 22px;
            font-weight: 700;
            margin-left: 5px;
        }


        /* =========================================================
           CARD
        ========================================================= */

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow:
                0 2px 8px rgba(15, 23, 42, 0.06);
            border: 1px solid #e2e8f0;
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 18px;
        }


        /* =========================================================
           EMPLOYEE SEARCH
        ========================================================= */

        .employee-search-wrapper {
            width: 100%;
        }

        .employee-search-label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 7px;
            color: #334155;
        }

        .employee-search {
            width: 100%;
            padding: 16px 17px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 17px;
            background: white;
            color: #0f172a;
            outline: none;
            margin-bottom: 10px;
        }

        .employee-search:focus {
            border-color: #2563eb;
            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .employee-search::placeholder {
            color: #94a3b8;
        }


        /* =========================================================
           EMPLOYEE SELECT
        ========================================================= */

        .employee-select {
            width: 100%;
            padding: 16px;
            border: 1px solid #cbd5e1;
            border-radius: 9px;
            font-size: 17px;
            background: white;
            outline: none;
            color: #0f172a;
        }

        .employee-select:focus {
            border-color: #2563eb;
            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .employee-select option {
            padding: 12px;
        }


        /* =========================================================
           SEARCH MESSAGE
        ========================================================= */

        .employee-search-message {
            display: none;
            padding: 14px;
            margin-top: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            color: #64748b;
            text-align: center;
            font-size: 14px;
        }

        .employee-search-count {
            margin-top: 8px;
            color: #64748b;
            font-size: 13px;
        }


        /* =========================================================
           EMPLOYEE STATUS
        ========================================================= */

        .employee-status {
            margin-top: 18px;
            padding: 16px;
            border-radius: 9px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            display: none;
        }

        .employee-name {
            font-size: 20px;
            font-weight: 700;
        }

        .employee-code {
            margin-top: 4px;
            color: #64748b;
            font-size: 14px;
        }

        .status-message {
            margin-top: 12px;
            font-size: 15px;
            color: #475569;
        }


        /* =========================================================
           ACTION BUTTONS
        ========================================================= */

        .actions {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .action-button {
            border: none;
            border-radius: 10px;
            padding: 18px;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            min-height: 62px;
        }

        .action-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .sign-in {
            background: #16a34a;
            color: white;
        }

        .sign-in:hover:not(:disabled) {
            background: #15803d;
        }

        .going-out {
            background: #f59e0b;
            color: white;
        }

        .going-out:hover:not(:disabled) {
            background: #d97706;
        }

        .returning {
            background: #2563eb;
            color: white;
        }

        .returning:hover:not(:disabled) {
            background: #1d4ed8;
        }

        .sign-out {
            background: #dc2626;
            color: white;
        }

        .sign-out:hover:not(:disabled) {
            background: #b91c1c;
        }


        /* =========================================================
           REASON
        ========================================================= */

        .reason-box {
            display: none;
            margin-top: 15px;
            padding: 16px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
        }

        .reason-box label {
            display: block;
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 7px;
            color: #92400e;
        }

        .reason-required {
            color: #dc2626;
            margin-left: 3px;
        }

        .reason-box input {
            width: 100%;
            padding: 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 15px;
            outline: none;
            background: white;
        }

        .reason-box input:focus {
            border-color: #f59e0b;
            box-shadow:
                0 0 0 3px rgba(245, 158, 11, 0.12);
        }

        .reason-box input.invalid {
            border-color: #dc2626;
            box-shadow:
                0 0 0 3px rgba(220, 38, 38, 0.10);
        }

        .reason-help {
            margin-top: 6px;
            color: #92400e;
            font-size: 12px;
        }

        .reason-error {
            display: none;
            margin-top: 8px;
            color: #dc2626;
            font-size: 13px;
            font-weight: 700;
        }


        /* =========================================================
           HISTORY
        ========================================================= */

        .history {
            margin-top: 25px;
        }

        .history-title {
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .outing-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 13px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .outing-row:last-child {
            border-bottom: none;
        }

        .outing-reason {
            color: #64748b;
            font-size: 13px;
            margin-top: 3px;
        }

        .outing-time {
            text-align: right;
            font-weight: 600;
            white-space: nowrap;
        }


        /* =========================================================
           QUICK LINKS
        ========================================================= */

        .quick-links {
            margin-top: 25px;
            display: grid;
            grid-template-columns:
                repeat(3, minmax(0, 1fr));
            gap: 14px;
        }

        .quick-link {
            text-decoration: none;
            background: white;
            border: 1px solid #e2e8f0;
            color: #0f172a;
            border-radius: 10px;
            padding: 17px;
            text-align: center;
            font-weight: 700;
        }

        .quick-link:hover {
            background: #f8fafc;
        }


        /* =========================================================
           CAMERA MODAL
        ========================================================= */

        .camera-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.88);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .camera-modal {
            width: 100%;
            max-width: 650px;
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .camera-header {
            background: #0f172a;
            color: white;
            padding: 18px 22px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .camera-title {
            font-size: 18px;
            font-weight: 700;
        }

        .camera-subtitle {
            margin-top: 4px;
            color: #cbd5e1;
            font-size: 13px;
        }

        .camera-close {
            background: transparent;
            border: none;
            color: white;
            font-size: 27px;
            cursor: pointer;
        }

        .camera-body {
            padding: 20px;
        }

        .camera-preview {
            width: 100%;
            aspect-ratio: 4 / 3;
            background: #020617;
            border-radius: 10px;
            object-fit: cover;
            display: block;
        }

        .photo-preview {
            display: none;
            width: 100%;
            aspect-ratio: 4 / 3;
            background: #020617;
            border-radius: 10px;
            object-fit: cover;
        }

        .camera-instruction {
            text-align: center;
            margin: 15px 0;
            color: #475569;
            font-size: 14px;
        }


        /* =========================================================
           FACE STATUS
        ========================================================= */

        .face-status {
            text-align: center;
            margin: 15px 0 8px;
            padding: 12px;
            border-radius: 8px;
            background: #fef2f2;
            color: #991b1b;
            font-size: 15px;
            font-weight: 700;
            transition:
                background 0.2s ease,
                color 0.2s ease;
        }


        /* =========================================================
           CAMERA BUTTONS
        ========================================================= */

        .camera-actions {
            display: grid;
            grid-template-columns:
                repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .camera-button {
            border: none;
            border-radius: 9px;
            padding: 15px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }

        .camera-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .take-photo {
            background: #16a34a;
            color: white;
        }

        .confirm-photo {
            background: #2563eb;
            color: white;
        }

        .retake-photo {
            background: #64748b;
            color: white;
        }

        .cancel-photo {
            background: #e2e8f0;
            color: #0f172a;
        }

        .camera-error {
            display: none;
            background: #fee2e2;
            color: #991b1b;
            padding: 13px;
            border-radius: 8px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .saving-message {
            display: none;
            text-align: center;
            padding: 15px;
            font-weight: 700;
            color: #2563eb;
        }


        /* =========================================================
           FOOTER
        ========================================================= */

        .footer {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px 25px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 750px) {

            .header {
                padding: 18px 20px;
            }

            .header-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .clock {
                text-align: left;
            }

            .container {
                margin-top: 20px;
            }

            .top-row {
                align-items: flex-start;
                gap: 12px;
                flex-direction: column;
            }

            .actions {
                grid-template-columns: 1fr;
            }

            .quick-links {
                grid-template-columns: 1fr;
            }

            .camera-actions {
                grid-template-columns: 1fr;
            }

        }

        /* =========================================================
   FACE RECOGNITION
========================================================= */

.face-recognition-box {
    margin-bottom: 20px;
    padding: 18px;
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 10px;
    text-align: center;
}

.face-recognition-title {
    font-size: 17px;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 6px;
}

.face-recognition-status {
    margin: 8px 0 14px;
    color: #475569;
    font-size: 14px;
}

.face-recognition-video {
    display: none;
    width: 100%;
    max-width: 500px;
    margin: 0 auto 15px;
    border-radius: 10px;
    background: #020617;
    object-fit: cover;
}

.face-recognition-button {
    border: none;
    border-radius: 9px;
    padding: 14px 22px;
    background: #2563eb;
    color: white;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
}

.face-recognition-button:hover {
    background: #1d4ed8;
}

.face-recognition-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

    </style>

</head>


<body>


<header class="header">

    <div class="header-inner">

        <div>

            <div class="header-title">
                Goodman Fielder Attendance System
            </div>

            <div class="header-subtitle">
                Employee Attendance
            </div>

        </div>


        <div class="clock">

            <div
                class="clock-time"
                id="liveClock"
            >
                --:--:--
            </div>

            <div
                class="clock-date"
                id="liveDate"
            >
                Loading...
            </div>

        </div>

    </div>

</header>


<main class="container">


    <div class="top-row">

        <div class="page-title">
            Employee Attendance
        </div>

        <a
            href="{{ route('attendance.index') }}"
            class="back-button"
        >
            ← MAIN MENU
        </a>

    </div>


    {{-- SUCCESS MESSAGE --}}

    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    {{-- ERROR MESSAGE --}}

    @if(session('error'))

        <div class="alert alert-error">
            {{ session('error') }}
        </div>

    @endif


    {{-- VALIDATION ERRORS --}}

    @if($errors->any())

        <div class="alert alert-error">

            <strong>
                Please correct the following:
            </strong>

            <ul style="margin: 8px 0 0 20px;">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         AUTO RETURN COUNTDOWN
    ========================================================== --}}

    <div
        id="autoReturnBox"
        class="auto-return-box"
    >

        Returning to the main menu in

        <strong id="autoReturnCountdown">
            15
        </strong>

        seconds...

    </div>


    <div class="card">


        <div class="card-title">
            Select Employee
        </div>
<!-- =====================================================
     FACE RECOGNITION
====================================================== -->

<div class="face-recognition-box">

    <div class="face-recognition-title">
        📷 Face Recognition
    </div>

    <div
        id="recognitionStatus"
        class="face-recognition-status"
    >
        Click below to identify yourself automatically.
    </div>

    <video
        id="recognitionVideo"
        class="face-recognition-video"
        autoplay
        muted
        playsinline
    ></video>

    <button
        type="button"
        id="startRecognitionButton"
        class="face-recognition-button"
    >
        📷 Face Recognition
    </button>

</div>

        {{-- =====================================================
             EMPLOYEE SEARCH
        ====================================================== --}}

        <div class="employee-search-wrapper">

            <label
                for="employeeSearch"
                class="employee-search-label"
            >
                Search Employee
            </label>


            <input
                type="text"
                id="employeeSearch"
                class="employee-search"
                placeholder="🔍 Search employee name or ID..."
                autocomplete="off"
                autocapitalize="none"
                spellcheck="false"
            >


            <select
                id="employeeSelect"
                class="employee-select"
            >

                <option value="">
                    -- Select Employee --
                </option>

                @foreach($employees as $employee)

                    <option
    value="{{ $employee->id }}"
    data-name="{{ $employee->name }}"
    data-code="{{ $employee->employee_code }}"
    data-face-registered="{{ $employee->face_descriptor ? '1' : '0' }}"
>
                        {{ $employee->name }}

                        @if($employee->employee_code)

                            ({{ $employee->employee_code }})

                        @endif

                    </option>

                @endforeach

            </select>


            <div
                id="employeeSearchMessage"
                class="employee-search-message"
            >
                No employees found.
            </div>


            <div
                id="employeeSearchCount"
                class="employee-search-count"
            >
                Showing all employees
            </div>

        </div>


        {{-- =====================================================
             EMPLOYEE STATUS
        ====================================================== --}}

        <div
            id="employeeStatus"
            class="employee-status"
        >

            <div
                id="employeeName"
                class="employee-name"
            >
                -
            </div>

            <div
                id="employeeCode"
                class="employee-code"
            >
                -
            </div>

            <div
                id="statusMessage"
                class="status-message"
            >
                Select an employee.
            </div>
<div
    id="faceRegistrationArea"
    style="display:none; margin-top:15px;"
>

    <button
        type="button"
        id="registerMyFaceButton"
        class="action-button"
        style="
            width:100%;
            background:#7c3aed;
            color:white;
        "
    >
        📸 REGISTER MY FACE
    </button>

    <div
        id="faceRegistrationMessage"
        style="
            margin-top:8px;
            color:#64748b;
            font-size:13px;
            text-align:center;
        "
    >
        Your face has not been registered yet.
    </div>

</div>
        </div>


        {{-- =====================================================
             ACTIONS
        ====================================================== --}}

        <div class="actions">


            <button
                type="button"
                id="signInButton"
                class="action-button sign-in"
                disabled
            >
                📷 SIGN IN
            </button>


            <button
                type="button"
                id="goingOutButton"
                class="action-button going-out"
                disabled
            >
                GOING OUT
            </button>


            <button
                type="button"
                id="returningButton"
                class="action-button returning"
                disabled
            >
                RETURNING
            </button>


            <form
                method="POST"
                action="{{ route('attendance.sign-out') }}"
                id="signOutForm"
            >

                @csrf

                <input
                    type="hidden"
                    name="employee_id"
                    id="signOutEmployeeId"
                >

                <button
                    type="submit"
                    id="signOutButton"
                    class="action-button sign-out"
                    style="width:100%;"
                    disabled
                >
                    SIGN OUT
                </button>

            </form>


        </div>


        {{-- =====================================================
             GOING OUT REASON
        ====================================================== --}}

        <div
            id="reasonBox"
            class="reason-box"
        >

            <label for="outingReason">
                Reason for going out
                <span class="reason-required">*</span>
            </label>

            <input
                type="text"
                id="outingReason"
                placeholder="Please enter a reason"
                maxlength="255"
                required
                autocomplete="off"
            >

            <div class="reason-help">
                A reason is required before you can confirm going out.
            </div>

            <div
                id="reasonError"
                class="reason-error"
            >
                Please enter a reason before continuing.
            </div>

            <button
                type="button"
                id="confirmGoingOut"
                class="action-button going-out"
                style="width:100%; margin-top:10px;"
                disabled
            >
                CONFIRM GOING OUT
            </button>

        </div>


        {{-- =====================================================
             OUTING HISTORY
        ====================================================== --}}

        <div
            id="history"
            class="history"
            style="display:none;"
        >

            <div class="history-title">
                Today's Outing History
            </div>

            <div id="historyRows"></div>

        </div>


    </div>


    {{-- =========================================================
         QUICK LINKS
    ========================================================= --}}

    <div class="quick-links">


        <a
            href="{{ route('attendance.emergency') }}"
            class="quick-link"
        >
            🚨 Emergency Roll Call
        </a>


        <a
            href="{{ route('visitors.index') }}"
            class="quick-link"
        >
            👤 Visitor Register
        </a>


        @auth

            <a
                href="{{ route('employees.index') }}"
                class="quick-link"
            >
                👥 Employee Management
            </a>

        @endauth


    </div>


</main>


<div class="footer">

    Please ensure you sign out when leaving the premises.

</div>



{{-- =========================================================
     CAMERA MODAL
========================================================= --}}

<div
    id="cameraOverlay"
    class="camera-overlay"
>


    <div class="camera-modal">


        <div class="camera-header">


            <div>

                <div class="camera-title">
                    Employee Sign-In Photo
                </div>

                <div
                    class="camera-subtitle"
                    id="cameraEmployeeName"
                >
                    -
                </div>

            </div>


            <button
                type="button"
                class="camera-close"
                id="cameraClose"
            >
                ×
            </button>


        </div>


        <div class="camera-body">


            <div
                id="cameraError"
                class="camera-error"
            ></div>


            {{-- LIVE CAMERA --}}

            <video
                id="cameraPreview"
                class="camera-preview"
                autoplay
                playsinline
                muted
            ></video>


            {{-- CAPTURED PHOTO --}}

            <img
                id="photoPreview"
                class="photo-preview"
                alt="Sign-in photo preview"
            >


            {{-- FACE STATUS --}}

            <div
                id="faceStatus"
                class="face-status"
            >
                🔴 Checking camera...
            </div>


            <div
                id="cameraInstruction"
                class="camera-instruction"
            >
                Please look directly at the camera.
            </div>


            <div
                id="savingMessage"
                class="saving-message"
            >
                Saving attendance...
            </div>


            {{-- CAMERA BUTTONS --}}

            <div
                class="camera-actions"
                id="cameraActions"
            >


                


                <button
                    type="button"
                    id="cancelCameraButton"
                    class="camera-button cancel-photo"
                >
                    CANCEL
                </button>


            </div>


            {{-- PHOTO CONFIRM BUTTONS --}}

            <div
                class="camera-actions"
                id="photoActions"
                style="display:none;"
            >


                


            </div>


        </div>

    </div>

</div>

{{-- =========================================================
     FIRST-TIME FACE REGISTRATION MODAL
========================================================= --}}

<div
    id="faceRegistrationOverlay"
    class="camera-overlay"
    style="display:none;"
>

    <div class="camera-modal">

        <div class="camera-header">

            <div>

                <div class="camera-title">
                    Register My Face
                </div>

                <div
                    class="camera-subtitle"
                    id="faceRegistrationEmployeeName"
                >
                    -
                </div>

            </div>

            <button
                type="button"
                class="camera-close"
                id="faceRegistrationClose"
            >
                ×
            </button>

        </div>

        <div class="camera-body">

            <div
                id="faceRegistrationError"
                class="camera-error"
            ></div>

            <div
                style="
                    background:#eff6ff;
                    border:1px solid #bfdbfe;
                    color:#1e3a8a;
                    padding:12px;
                    border-radius:8px;
                    margin-bottom:15px;
                    font-size:14px;
                "
            >
                Please look directly at the camera.
                Make sure only your face is visible.
            </div>

            <video
                id="faceRegistrationVideo"
                class="camera-preview"
                autoplay
                playsinline
                muted
            ></video>

            <div
                id="faceRegistrationStatus"
                class="face-status"
            >
                🟡 Starting camera...
            </div>

            <div
                id="faceRegistrationInstruction"
                class="camera-instruction"
            >
                Position your face in the centre of the camera.
            </div>

            <div
                id="faceRegistrationSaving"
                class="saving-message"
            >
                Registering your face...
            </div>

            <div
                class="camera-actions"
                id="faceRegistrationActions"
            >

                <button
                    type="button"
                    id="captureFaceRegistrationButton"
                    class="camera-button take-photo"
                    disabled
                >
                    📸 CAPTURE & REGISTER
                </button>

                <button
                    type="button"
                    id="cancelFaceRegistrationButton"
                    class="camera-button cancel-photo"
                >
                    CANCEL
                </button>

            </div>

        </div>

    </div>

</div>

{{-- =========================================================
     HIDDEN PHOTO CANVAS
========================================================= --}}

<canvas
    id="photoCanvas"
    style="display:none;"
></canvas>



{{-- =========================================================
     MEDIAPIPE
========================================================= --}}

<script type="module">

import {
    FaceLandmarker,
    FilesetResolver
}
from "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision@1.0.1";


window.FaceLandmarker =
    FaceLandmarker;

window.FilesetResolver =
    FilesetResolver;


window.dispatchEvent(
    new Event('mediapipe-ready')
);

</script>



<script>
    

document.addEventListener(
    'DOMContentLoaded',
    function ()
    {


        /* =========================================================
           LIVE CLOCK
        ========================================================= */

        function updateClock()
        {

            const now =
                new Date();


            document.getElementById(
                'liveClock'
            ).textContent =
                now.toLocaleTimeString(
                    'en-FJ',
                    {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    }
                );


            document.getElementById(
                'liveDate'
            ).textContent =
                now.toLocaleDateString(
                    'en-FJ',
                    {
                        weekday: 'long',
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    }
                );

        }


        updateClock();


        setInterval(
            updateClock,
            1000
        );


        /* =========================================================
           ELEMENTS
        ========================================================= */

        const employeeSearch =
            document.getElementById(
                'employeeSearch'
            );


        const employeeSelect =
            document.getElementById(
                'employeeSelect'
            );


        const employeeSearchMessage =
            document.getElementById(
                'employeeSearchMessage'
            );


        const employeeSearchCount =
            document.getElementById(
                'employeeSearchCount'
            );


        const employeeStatus =
            document.getElementById(
                'employeeStatus'
            );


        const employeeName =
            document.getElementById(
                'employeeName'
            );


        const employeeCode =
            document.getElementById(
                'employeeCode'
            );


        const statusMessage =
            document.getElementById(
                'statusMessage'
            );


        const signInButton =
            document.getElementById(
                'signInButton'
            );


        const goingOutButton =
            document.getElementById(
                'goingOutButton'
            );


        const returningButton =
            document.getElementById(
                'returningButton'
            );


        const signOutButton =
            document.getElementById(
                'signOutButton'
            );


        const signOutEmployeeId =
            document.getElementById(
                'signOutEmployeeId'
            );


        const reasonBox =
            document.getElementById(
                'reasonBox'
            );


        const outingReason =
            document.getElementById(
                'outingReason'
            );


        const reasonError =
            document.getElementById(
                'reasonError'
            );


        const confirmGoingOut =
            document.getElementById(
                'confirmGoingOut'
            );


        const history =
            document.getElementById(
                'history'
            );


        const historyRows =
            document.getElementById(
                'historyRows'
            );


        /* =========================================================
           AUTO RETURN ELEMENTS
        ========================================================= */

        const autoReturnBox =
            document.getElementById(
                'autoReturnBox'
            );


        const autoReturnCountdown =
            document.getElementById(
                'autoReturnCountdown'
            );


        const AUTO_RETURN_SECONDS =
            20;


        let autoReturnTimer =
            null;


        let autoReturnSeconds =
            AUTO_RETURN_SECONDS;


        /* =========================================================
           SAVE ORIGINAL EMPLOYEE OPTIONS
        ========================================================= */

        const originalEmployeeOptions =
            Array.from(
                employeeSelect.options
            ).map(
                option =>
                    option.cloneNode(true)
            );


        /* =========================================================
           CAMERA ELEMENTS
        ========================================================= */



        


        /* =========================================================
           AUTO RETURN FUNCTIONS
        ========================================================= */

        function isCameraOpen()
        {

            return (
                cameraOverlay &&
                cameraOverlay.style.display === 'flex'
            );

        }


        function stopAutoReturnTimer()
        {

            if (autoReturnTimer)
            {

                clearInterval(
                    autoReturnTimer
                );


                autoReturnTimer =
                    null;

            }

        }


        function hideAutoReturnBox()
        {

            if (autoReturnBox)
            {

                autoReturnBox.style.display =
                    'none';

            }

        }


        function startAutoReturnTimer()
        {

            stopAutoReturnTimer();


            /*
            |------------------------------------------------------
            | Do not run the timer while the camera is open.
            |------------------------------------------------------
            */

            if (isCameraOpen())
            {

                hideAutoReturnBox();

                return;

            }


            autoReturnSeconds =
                AUTO_RETURN_SECONDS;


            if (autoReturnCountdown)
            {

                autoReturnCountdown.textContent =
                    autoReturnSeconds;

            }


            if (autoReturnBox)
            {

                autoReturnBox.style.display =
                    'block';

            }


            autoReturnTimer =
                setInterval(
                    function ()
                    {

                        /*
                        |--------------------------------------------------
                        | Camera protection.
                        |--------------------------------------------------
                        */

                        if (isCameraOpen())
                        {

                            stopAutoReturnTimer();

                            hideAutoReturnBox();

                            return;

                        }


                        autoReturnSeconds--;


                        if (autoReturnCountdown)
                        {

                            autoReturnCountdown.textContent =
                                autoReturnSeconds;

                        }


                        if (
                            autoReturnSeconds <= 0
                        )
                        {

                            stopAutoReturnTimer();


                            window.location.href =
                                "{{ route('attendance.index') }}";

                        }

                    },
                    1000
                );

        }


        function resetAutoReturnTimer()
        {

            /*
            |------------------------------------------------------
            | Never redirect while camera is being used.
            |------------------------------------------------------
            */

            if (isCameraOpen())
            {

                stopAutoReturnTimer();

                hideAutoReturnBox();

                return;

            }


            startAutoReturnTimer();

        }


        /*
        |----------------------------------------------------------
        | Any user activity resets the 15-second countdown.
        |----------------------------------------------------------
        */

        [
            'click',
            'pointerdown',
            'touchstart',
            'keydown',
            'input',
            'change'
        ].forEach(
            function (eventName)
            {

                document.addEventListener(
                    eventName,
                    function ()
                    {

                        if (!isCameraOpen())
                        {

                            resetAutoReturnTimer();

                        }

                    },
                    {
                        passive: true
                    }
                );

            }
        );


        /* =========================================================
           EMPLOYEE SEARCH
        ========================================================= */

        function filterEmployees()
        {

            const search =
                employeeSearch.value
                    .trim()
                    .toLowerCase();


            employeeSelect.value =
                '';


            resetEmployeeSelection();


            const employeeOptions =
                originalEmployeeOptions.filter(
                    option =>
                        option.value !== ''
                );


            if (!search)
            {

                employeeSelect.innerHTML =
                    '';


                originalEmployeeOptions.forEach(
                    option =>
                        employeeSelect.appendChild(
                            option.cloneNode(true)
                        )
                );


                employeeSearchMessage.style.display =
                    'none';


                employeeSearchCount.textContent =
                    'Showing all employees';


                return;

            }


            const matches =
                employeeOptions.filter(
                    option =>
                    {

                        const name =
                            (
                                option.dataset.name ||
                                ''
                            ).toLowerCase();


                        const code =
                            (
                                option.dataset.code ||
                                ''
                            ).toLowerCase();


                        return (
                            name.includes(search) ||
                            code.includes(search)
                        );

                    }
                );


            employeeSelect.innerHTML =
                '';


            const placeholder =
                originalEmployeeOptions.find(
                    option =>
                        option.value === ''
                );


            if (placeholder)
            {

                employeeSelect.appendChild(
                    placeholder.cloneNode(true)
                );

            }


            matches.forEach(
                option =>
                    employeeSelect.appendChild(
                        option.cloneNode(true)
                    )
            );


            if (matches.length === 0)
            {

                employeeSearchMessage.style.display =
                    'block';


                employeeSearchCount.textContent =
                    '0 employees found';

            }
            else
            {

                employeeSearchMessage.style.display =
                    'none';


                employeeSearchCount.textContent =
                    matches.length +
                    (
                        matches.length === 1
                            ? ' employee found'
                            : ' employees found'
                    );

            }

        }


        employeeSearch.addEventListener(
            'input',
            filterEmployees
        );


        /* =========================================================
           RESET REASON
        ========================================================= */

        function resetReason()
        {

            outingReason.value =
                '';


            outingReason.classList.remove(
                'invalid'
            );


            reasonError.style.display =
                'none';


            confirmGoingOut.disabled =
                true;

        }


        /* =========================================================
           VALIDATE REASON
        ========================================================= */

        function validateReason()
        {

            const reason =
                outingReason.value.trim();


            if (!reason)
            {

                confirmGoingOut.disabled =
                    true;


                outingReason.classList.add(
                    'invalid'
                );


                reasonError.style.display =
                    'block';


                return false;

            }


            outingReason.classList.remove(
                'invalid'
            );


            reasonError.style.display =
                'none';


            confirmGoingOut.disabled =
                false;


            return true;

        }


        /* =========================================================
           REASON INPUT
        ========================================================= */

        outingReason.addEventListener(
            'input',
            function ()
            {

                validateReason();

            }
        );


        /* =========================================================
           RESET EMPLOYEE SELECTION
        ========================================================= */

        function resetEmployeeSelection()
        {

            employeeStatus.style.display =
                'none';


            signInButton.disabled =
                true;


            goingOutButton.disabled =
                true;


            returningButton.disabled =
                true;


            signOutButton.disabled =
                true;


            signOutEmployeeId.value =
                '';


            reasonBox.style.display =
                'none';


            resetReason();


            history.style.display =
                'none';


            historyRows.innerHTML =
                '';

        }




        /* =========================================================
           MEDIAPIPE READY
        ========================================================= */

        window.addEventListener(
            'mediapipe-ready',
            function ()
            {

                console.log(
                    'MediaPipe ready.'
                );

            }
        );


        /* =========================================================
           INITIALIZE FACE DETECTION
        ========================================================= */

        async function initializeFaceDetection()
        {

            if (faceDetectionReady)
            {
                return true;
            }


            if (
                !window.FaceLandmarker ||
                !window.FilesetResolver
            )
            {

                faceStatus.textContent =
                    '🔴 Face detection loading...';


                faceStatus.style.background =
                    '#fef2f2';


                faceStatus.style.color =
                    '#991b1b';


                return false;

            }


            try
            {

                faceStatus.textContent =
                    '🟡 Loading face detection...';


                faceStatus.style.background =
                    '#fffbeb';


                faceStatus.style.color =
                    '#92400e';


                const vision =
                    await FilesetResolver.forVisionTasks(
                        "https://cdn.jsdelivr.net/npm/@mediapipe/tasks-vision/wasm"
                    );


                faceLandmarker =
                    await window.FaceLandmarker.createFromOptions(
                        vision,
                        {

                            baseOptions:
                            {

                                modelAssetPath:
    '/models/face_landmarker.task',

delegate:
    'CPU'

                            },

                            runningMode:
                                'VIDEO',

                            numFaces:
                                2,

                            minFaceDetectionConfidence:
                                0.6,

                            minFacePresenceConfidence:
                                0.6,

                            minTrackingConfidence:
                                0.6

                        }
                    );


                faceDetectionReady =
                    true;


                faceStatus.textContent =
                    '🟡 Please look directly at the camera';


                faceStatus.style.background =
                    '#fffbeb';


                faceStatus.style.color =
                    '#92400e';


                return true;

            }
            catch (error)
            {

                console.error(
                    'Face detection initialization failed:',
                    error
                );


                faceDetectionReady =
                    false;


                faceDetected =
                    false;


                takePhotoButton.disabled =
                    true;


                faceStatus.textContent =
                    '🔴 Face detection unavailable';


                faceStatus.style.background =
                    '#fef2f2';


                faceStatus.style.color =
                    '#991b1b';


                cameraInstruction.textContent =
                    'Please reload the page and try again.';


                return false;

            }

        }


        /* =========================================================
           START FACE DETECTION
        ========================================================= */

        function startFaceDetection()
        {

            if (faceDetectionTimer)
            {

                clearInterval(
                    faceDetectionTimer
                );

            }


            faceDetectionTimer =
                setInterval(
                    function ()
                    {

                        if (
                            !faceDetectionReady
                        )
                        {
                            return;
                        }


                        if (!cameraStream)
                        {
                            return;
                        }


                        if (
                            cameraPreview.readyState <
                            HTMLMediaElement.HAVE_CURRENT_DATA
                        )
                        {
                            return;
                        }


                        if (
                            cameraPreview.currentTime ===
                            lastVideoTime
                        )
                        {
                            return;
                        }


                        lastVideoTime =
                            cameraPreview.currentTime;


                        try
                        {

                            const result =
                                faceLandmarker.detectForVideo(
                                    cameraPreview,
                                    performance.now()
                                );


                            validateFace(
                                result
                            );

                        }
                        catch (error)
                        {

                            console.error(
                                'Face detection error:',
                                error
                            );

                        }

                    },
                    150
                );

        }


        /* =========================================================
           STOP FACE DETECTION
        ========================================================= */

        function stopFaceDetection()
        {

            if (faceDetectionTimer)
            {

                clearInterval(
                    faceDetectionTimer
                );


                faceDetectionTimer =
                    null;

            }

        }


        /* =========================================================
           FACE VALIDATION
        ========================================================= */

        function validateFace(result)
        {

            faceDetected =
                false;


            const faces =
                result &&
                result.faceLandmarks
                    ? result.faceLandmarks
                    : [];


            if (faces.length === 0)
            {

                setFaceStatus(
                    '🔴 No face detected',
                    'Please position your face in front of the camera.',
                    false
                );


                return;

            }


            if (faces.length > 1)
            {

                setFaceStatus(
                    '🔴 Multiple faces detected',
                    'Only one person can be in the photo.',
                    false
                );


                return;

            }


            const landmarks =
                faces[0];


            const xs =
                landmarks.map(
                    point =>
                        point.x
                );


            const ys =
                landmarks.map(
                    point =>
                        point.y
                );


            const minX =
                Math.min(...xs);


            const maxX =
                Math.max(...xs);


            const minY =
                Math.min(...ys);


            const maxY =
                Math.max(...ys);


            const faceWidth =
                maxX - minX;


            const faceHeight =
                maxY - minY;


            if (
                faceWidth < 0.18 ||
                faceHeight < 0.18
            )
            {

                setFaceStatus(
                    '🔴 Move closer',
                    'Your face is too far from the camera.',
                    false
                );


                return;

            }


            const faceCenterX =
                (minX + maxX) / 2;


            const faceCenterY =
                (minY + maxY) / 2;


            if (
                faceCenterX < 0.25 ||
                faceCenterX > 0.75 ||
                faceCenterY < 0.20 ||
                faceCenterY > 0.80
            )
            {

                setFaceStatus(
                    '🟠 Center your face',
                    'Please move your face toward the centre.',
                    false
                );


                return;

            }


            const nose =
                landmarks[1];


            const leftEye =
                landmarks[33];


            const rightEye =
                landmarks[263];


            const leftMouth =
                landmarks[61];


            const rightMouth =
                landmarks[291];


            if (
                !nose ||
                !leftEye ||
                !rightEye ||
                !leftMouth ||
                !rightMouth
            )
            {

                setFaceStatus(
                    '🔴 Face not clear',
                    'Please make sure your facial features are visible.',
                    false
                );


                return;

            }


            const eyeDifference =
                Math.abs(
                    leftEye.y -
                    rightEye.y
                );


            if (
                eyeDifference > 0.12
            )
            {

                setFaceStatus(
                    '🟠 Face angle not suitable',
                    'Please look directly at the camera.',
                    false
                );


                return;

            }


            const eyeDistance =
                Math.abs(
                    rightEye.x -
                    leftEye.x
                );


            const mouthDistance =
                Math.abs(
                    rightMouth.x -
                    leftMouth.x
                );


            if (
                eyeDistance < 0.08 ||
                mouthDistance < 0.04
            )
            {

                setFaceStatus(
                    '🔴 Face not clear',
                    'Please face the camera directly.',
                    false
                );


                return;

            }


            faceDetected =
                true;


            setFaceStatus(
                '🟢 Face detected — ready',
                'You can now sign in.',
                true
            );

        }


        /* =========================================================
           FACE STATUS UI
        ========================================================= */

        function setFaceStatus(
            title,
            message,
            ready
        )
        {

            faceStatus.textContent =
                title;


            if (ready)
            {

                faceStatus.style.background =
                    '#ecfdf5';


                faceStatus.style.color =
                    '#047857';


                if (!capturingPhoto)
                {

                    takePhotoButton.disabled =
                        true;

                }


                cameraInstruction.textContent =
                    message;

            }
            else
            {

                faceStatus.style.background =
                    '#fef2f2';


                faceStatus.style.color =
                    '#991b1b';


                if (!capturingPhoto)
                {

                    takePhotoButton.disabled =
                        true;

                }


                cameraInstruction.textContent =
                    message;

            }

        }


        /* =========================================================
           SELECT EMPLOYEE
        ========================================================= */

        employeeSelect.addEventListener(
            'change',
            function ()
            {

                const employeeId =
                    this.value;


                if (!employeeId)
                {

                    resetEmployeeSelection();


                    return;

                }


                const selectedOption =
                    this.options[
                        this.selectedIndex
                    ];


                employeeName.textContent =
                    selectedOption.dataset.name ||
                    '';


                employeeCode.textContent =
                    selectedOption.dataset.code
                        ? 'Employee ID: ' +
                          selectedOption.dataset.code
                        : '';


                signOutEmployeeId.value =
                    employeeId;


                employeeStatus.style.display =
                    'block';


                loadEmployeeStatus(
                    employeeId
                );

            }
        );


        /* =========================================================
           LOAD EMPLOYEE STATUS
        ========================================================= */

       async function loadEmployeeStatus(employeeId)
{
    try
    {
        const response =
            await fetch(
                `/attendance/status/${employeeId}`
            );


        if (!response.ok)
        {
            throw new Error(
                'Unable to load employee status.'
            );
        }


        const data =
            await response.json();


        updateEmployeeStatus(data);

    }
    catch (error)
    {
        console.error(
            'Employee status error:',
            error
        );
    }
}


        /* =========================================================
           UPDATE STATUS
        ========================================================= */

        function updateEmployeeStatus(
            data
        )
        {

           if (data.status === 'not_signed_in')
{
    signInButton.disabled = false;

    goingOutButton.disabled = true;

    returningButton.disabled = true;

    signOutButton.disabled = true;
}


            if (
                data.status ===
                'signed_in'
            )
            {

                signInButton.disabled =
                    true;


                signOutButton.disabled =
                    false;


                if (
                    data.currently_out
                )
                {

                    statusMessage.textContent =
                        'Currently OUT since ' +
                        data.out_time +
                        '.';


                    goingOutButton.disabled =
                        true;


                    returningButton.disabled =
                        false;

                }
                else
                {

                    statusMessage.textContent =
                        'Signed in at ' +
                        data.check_in +
                        '.';


                    goingOutButton.disabled =
                        false;


                    returningButton.disabled =
                        true;

                }


                loadOutings(
                    data.outings
                );


                return;

            }


            if (
                data.status ===
                'completed'
            )
            {

                statusMessage.textContent =
                    'Completed for today. Signed in at ' +
                    data.check_in +
                    ' and signed out at ' +
                    data.check_out +
                    '.';


                signInButton.disabled =
                    true;


                goingOutButton.disabled =
                    true;


                returningButton.disabled =
                    true;


                signOutButton.disabled =
                    true;


                loadOutings(
                    data.outings
                );

            }

        }


        /* =========================================================
           LOAD OUTINGS
        ========================================================= */

        function loadOutings(
            outings
        )
        {

            historyRows.innerHTML =
                '';


            if (
                !outings ||
                outings.length === 0
            )
            {

                history.style.display =
                    'none';


                return;

            }


            history.style.display =
                'block';


            outings.forEach(
                function (outing)
                {

                    const row =
                        document.createElement(
                            'div'
                        );


                    row.className =
                        'outing-row';


                    const reason =
                        outing.reason
                            ? outing.reason
                            : 'No reason provided';


                    const returned =
                        outing.return_time
                            ? outing.return_time +
                              ' (' +
                              outing.duration_minutes +
                              ' min)'
                            : 'Currently out';


                    row.innerHTML = `
                        <div>

                            <strong>
                                Out at ${outing.out_time}
                            </strong>

                            <div class="outing-reason">
                                ${escapeHtml(reason)}
                            </div>

                        </div>

                        <div class="outing-time">
                            ${returned}
                        </div>
                    `;


                    historyRows.appendChild(
                        row
                    );

                }
            );

        }


        /* =========================================================
           ESCAPE HTML
        ========================================================= */

        function escapeHtml(
            value
        )
        {

            const div =
                document.createElement(
                    'div'
                );


            div.textContent =
                value;


            return div.innerHTML;

        }


 
/* =========================================================
   SIGN IN
========================================================= */

signInButton.addEventListener(
    'click',
    async function ()
    {
        stopAutoReturnTimer();
        hideAutoReturnBox();

        const employeeId =
            employeeSelect.value;

        if (!employeeId)
        {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent double-clicks while saving.
        |--------------------------------------------------------------------------
        */

        signInButton.disabled = true;

        const formData =
            new FormData();

        formData.append(
            '_token',
            document
                .querySelector(
                    'meta[name="csrf-token"]'
                )
                .getAttribute(
                    'content'
                )
        );

        formData.append(
            'employee_id',
            employeeId
        );

        try
        {
            const response =
                await fetch(
                    "{{ route('attendance.sign-in') }}",
                    {
                        method:
                            'POST',

                        body:
                            formData,

                        headers:
                        {
                            'Accept':
                                'text/html'
                        }
                    }
                );

            /*
            |--------------------------------------------------------------------------
            | Laravel normally redirects back to the attendance page.
            |--------------------------------------------------------------------------
            */

            if (
                response.redirected
            )
            {
                window.location.href =
                    response.url;

                return;
            }

            if (!response.ok)
            {
                throw new Error(
                    'Unable to save attendance.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Fallback if Laravel returned a successful response
            | without redirecting.
            |--------------------------------------------------------------------------
            */

            window.location.reload();

        }
        catch (error)
        {
            console.error(
                'Sign in error:',
                error
            );

            signInButton.disabled =
                false;

            statusMessage.textContent =
                'Unable to sign in. Please try again.';

            startAutoReturnTimer();
        }
    }
);


        /* =========================================================
           CLOSE CAMERA
        ========================================================= */

        cameraClose.addEventListener(
            'click',
            closeCamera
        );


        cancelCameraButton.addEventListener(
            'click',
            closeCamera
        );


        function closeCamera()
        {

            stopFaceDetection();


            stopCamera();


            capturedPhotoBlob =
                null;


            capturingPhoto =
                false;


            cameraOverlay.style.display =
                'none';


            cameraError.style.display =
                'none';


            cameraActions.style.display =
                'grid';


            photoActions.style.display =
                'none';


            photoPreview.style.display =
                'none';


            photoPreview.src =
                '';


            cameraPreview.style.display =
                'block';


           


            faceDetected =
                false;


            /*
            |------------------------------------------------------
            | Camera closed.
            | Start the 15-second inactivity countdown again.
            |------------------------------------------------------
            */

            startAutoReturnTimer();

        }


        /* =========================================================
           STOP CAMERA
        ========================================================= */

        function stopCamera()
        {

            if (cameraStream)
            {

                cameraStream
                    .getTracks()
                    .forEach(
                        track =>
                            track.stop()
                    );


                cameraStream =
                    null;

            }


            cameraPreview.srcObject =
                null;

        }


        /* =========================================================
           GOING OUT
        ========================================================= */

        goingOutButton.addEventListener(
            'click',
            function ()
            {

                resetAutoReturnTimer();


                reasonBox.style.display =
                    'block';


                resetReason();


                outingReason.focus();

            }
        );


        /* =========================================================
           CONFIRM GOING OUT
        ========================================================= */

        confirmGoingOut.addEventListener(
            'click',
            async function ()
            {

                stopAutoReturnTimer();

                hideAutoReturnBox();


                const employeeId =
                    employeeSelect.value;


                if (!employeeId)
                {
                    return;
                }


                /*
                |------------------------------------------------------
                | REASON IS COMPULSORY
                |------------------------------------------------------
                */

                const reason =
                    outingReason.value.trim();


                if (!reason)
                {

                    validateReason();


                    outingReason.focus();


                    startAutoReturnTimer();


                    return;

                }


                confirmGoingOut.disabled =
                    true;


                const formData =
                    new FormData();


                formData.append(
                    '_token',
                    document
                        .querySelector(
                            'meta[name="csrf-token"]'
                        )
                        .getAttribute(
                            'content'
                        )
                );


                formData.append(
                    'employee_id',
                    employeeId
                );


                formData.append(
                    'reason',
                    reason
                );


                try
                {

                    const response =
                        await fetch(
                            "{{ route('attendance.going-out') }}",
                            {
                                method:
                                    'POST',

                                body:
                                    formData,

                                headers:
                                {
                                    'Accept':
                                        'text/html'
                                }
                            }
                        );


                    if (
                        response.redirected
                    )
                    {

                        window.location.href =
                            response.url;


                        return;

                    }


                    if (!response.ok)
                    {

                        throw new Error(
                            'Unable to record going out.'
                        );

                    }


                    window.location.reload();

                }
                catch (error)
                {

                    console.error(
                        error
                    );


                    confirmGoingOut.disabled =
                        false;


                    alert(
                        'Unable to record going out. Please try again.'
                    );


                    startAutoReturnTimer();

                }

            }
        );


        /* =========================================================
           RETURNING
        ========================================================= */

        returningButton.addEventListener(
            'click',
            async function ()
            {

                stopAutoReturnTimer();

                hideAutoReturnBox();


                const employeeId =
                    employeeSelect.value;


                if (!employeeId)
                {
                    return;
                }


                const formData =
                    new FormData();


                formData.append(
                    '_token',
                    document
                        .querySelector(
                            'meta[name="csrf-token"]'
                        )
                        .getAttribute(
                            'content'
                        )
                );


                formData.append(
                    'employee_id',
                    employeeId
                );


                try
                {

                    const response =
                        await fetch(
                            "{{ route('attendance.returning') }}",
                            {
                                method:
                                    'POST',

                                body:
                                    formData,

                                headers:
                                {
                                    'Accept':
                                        'text/html'
                                }
                            }
                        );


                    if (
                        response.redirected
                    )
                    {

                        window.location.href =
                            response.url;


                        return;

                    }


                    if (!response.ok)
                    {

                        throw new Error(
                            'Unable to record returning.'
                        );

                    }


                    window.location.reload();

                }
                catch (error)
                {

                    console.error(
                        error
                    );


                    alert(
                        'Unable to record returning.'
                    );


                    startAutoReturnTimer();

                }

            }
        );


        /* =========================================================
           CLEANUP CAMERA
        ========================================================= */

        window.addEventListener(
            'beforeunload',
            function ()
            {

                stopAutoReturnTimer();

                stopFaceDetection();


                stopCamera();

            }
        );


        /* =========================================================
           START AUTO RETURN TIMER
        ========================================================= */

        startAutoReturnTimer();


    }

);
/*
|--------------------------------------------------------------------------
| FIRST-TIME EMPLOYEE FACE REGISTRATION
|--------------------------------------------------------------------------
|
| Employee:
|   - Can register only if no face is registered.
|   - Cannot replace an existing face.
|
| Admin:
|   - Uses the existing Employee Management face registration.
|
| This script does NOT perform attendance sign-in.
|--------------------------------------------------------------------------
*/

document.addEventListener('DOMContentLoaded', function () {

    const employeeSelect =
        document.getElementById('employeeSelect');

    const faceRegistrationArea =
        document.getElementById('faceRegistrationArea');

    const registerMyFaceButton =
        document.getElementById('registerMyFaceButton');

    const faceRegistrationMessage =
        document.getElementById('faceRegistrationMessage');

    const faceRegistrationOverlay =
        document.getElementById('faceRegistrationOverlay');

    const faceRegistrationVideo =
        document.getElementById('faceRegistrationVideo');

    const faceRegistrationEmployeeName =
        document.getElementById('faceRegistrationEmployeeName');

    const faceRegistrationError =
        document.getElementById('faceRegistrationError');

    const faceRegistrationStatus =
        document.getElementById('faceRegistrationStatus');

    const faceRegistrationInstruction =
        document.getElementById('faceRegistrationInstruction');

    const faceRegistrationSaving =
        document.getElementById('faceRegistrationSaving');

    const faceRegistrationActions =
        document.getElementById('faceRegistrationActions');

    const captureFaceRegistrationButton =
        document.getElementById(
            'captureFaceRegistrationButton'
        );

    const cancelFaceRegistrationButton =
        document.getElementById(
            'cancelFaceRegistrationButton'
        );

    const faceRegistrationClose =
        document.getElementById(
            'faceRegistrationClose'
        );


    /*
    |--------------------------------------------------------------------------
    | VARIABLES
    |--------------------------------------------------------------------------
    */

    let registrationStream = null;

    let registrationRunning = false;

    let registrationModelsLoaded = false;

    let currentEmployeeId = null;

    let currentEmployeeName = null;

    let latestFaceDetection = null;


    /*
    |--------------------------------------------------------------------------
    | FACE API MODEL LOCATION
    |--------------------------------------------------------------------------
    */

    const MODEL_URL = '/models';


    /*
    |--------------------------------------------------------------------------
    | CHECK REQUIRED ELEMENTS
    |--------------------------------------------------------------------------
    */

    if (
        !employeeSelect ||
        !faceRegistrationArea ||
        !registerMyFaceButton ||
        !faceRegistrationOverlay ||
        !faceRegistrationVideo ||
        !captureFaceRegistrationButton
    ) {

        console.log(
            'Face registration elements not found.'
        );

        return;
    }


    /*
    |--------------------------------------------------------------------------
    | HIDE REGISTRATION AREA
    |--------------------------------------------------------------------------
    */

    function hideFaceRegistration() {

        faceRegistrationArea.style.display =
            'none';

        currentEmployeeId = null;

        currentEmployeeName = null;
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW REGISTRATION AREA
    |--------------------------------------------------------------------------
    */

    function updateFaceRegistrationVisibility() {

        const selectedOption =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];

        if (
            !selectedOption ||
            !selectedOption.value
        ) {

            hideFaceRegistration();

            return;
        }

        const employeeId =
            selectedOption.value;

        const employeeName =
            selectedOption.dataset.name ||
            selectedOption.textContent.trim();

        const faceRegistered =
            selectedOption.dataset.faceRegistered === '1';


        currentEmployeeId =
            employeeId;

        currentEmployeeName =
            employeeName;


        /*
        |--------------------------------------------------------------------------
        | FACE ALREADY REGISTERED
        |--------------------------------------------------------------------------
        */

        if (faceRegistered) {

            faceRegistrationArea.style.display =
                'none';

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | FACE NOT REGISTERED
        |--------------------------------------------------------------------------
        */

        faceRegistrationArea.style.display =
            'block';

        faceRegistrationMessage.textContent =
            'Your face has not been registered yet.';


        registerMyFaceButton.disabled =
            false;

        registerMyFaceButton.textContent =
            '📸 REGISTER MY FACE';
    }


    /*
    |--------------------------------------------------------------------------
    | EMPLOYEE SELECTION
    |--------------------------------------------------------------------------
    */

    employeeSelect.addEventListener(
        'change',
        function () {

            /*
            | Do not interfere with the existing
            | attendance selection logic.
            */

            updateFaceRegistrationVisibility();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | LOAD FACE API MODELS
    |--------------------------------------------------------------------------
    */

    async function loadRegistrationModels() {

        if (registrationModelsLoaded) {

            return;
        }


        if (
            typeof faceapi === 'undefined'
        ) {

            throw new Error(
                'Face recognition library is not available.'
            );
        }


        faceRegistrationStatus.textContent =
            '🟡 Loading face registration models...';


        faceRegistrationInstruction.textContent =
            'Please wait while the camera system starts.';


        await faceapi.nets.tinyFaceDetector.loadFromUri(
            MODEL_URL
        );


        await faceapi.nets.faceLandmark68Net.loadFromUri(
            MODEL_URL
        );


        await faceapi.nets.faceRecognitionNet.loadFromUri(
            MODEL_URL
        );


        registrationModelsLoaded = true;


        faceRegistrationStatus.textContent =
            '🟢 Face registration models ready.';

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN REGISTRATION CAMERA
    |--------------------------------------------------------------------------
    */

    async function openFaceRegistrationCamera() {

        if (!currentEmployeeId) {

            return;
        }


        /*
        | Make sure the employee still selected
        | in the dropdown is the same employee.
        */

        const selectedOption =
            employeeSelect.options[
                employeeSelect.selectedIndex
            ];


        if (
            !selectedOption ||
            selectedOption.value !== currentEmployeeId
        ) {

            showRegistrationError(
                'Please select the employee again.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | RESET UI
        |--------------------------------------------------------------------------
        */

        faceRegistrationError.textContent =
            '';

        faceRegistrationError.style.display =
            'none';

        faceRegistrationSaving.style.display =
            'none';

        faceRegistrationActions.style.display =
            'flex';

        captureFaceRegistrationButton.disabled =
            true;

        captureFaceRegistrationButton.textContent =
            '📸 CAPTURE & REGISTER';


        faceRegistrationEmployeeName.textContent =
            currentEmployeeName;


        faceRegistrationStatus.textContent =
            '🟡 Starting camera...';


        faceRegistrationInstruction.textContent =
            'Position your face in the centre of the camera.';


        /*
        |--------------------------------------------------------------------------
        | OPEN MODAL
        |--------------------------------------------------------------------------
        */

        faceRegistrationOverlay.style.display =
            'flex';


        try {

            /*
            | Load face-api models first.
            */

            await loadRegistrationModels();


            /*
            | Start camera.
            */

            registrationStream =
                await navigator.mediaDevices.getUserMedia({

                    video: {

                        facingMode: 'user',

                        width: {
                            ideal: 1280
                        },

                        height: {
                            ideal: 960
                        }

                    },

                    audio: false

                });


            faceRegistrationVideo.srcObject =
                registrationStream;


            await faceRegistrationVideo.play();


            registrationRunning =
                true;


            faceRegistrationStatus.textContent =
                '🟡 Looking for your face...';


            faceRegistrationInstruction.textContent =
                'Look directly at the camera and keep your face centred.';


            /*
            | Start face detection.
            */

            registrationDetectionLoop();


        } catch (error) {

            console.error(
                'Face registration camera error:',
                error
            );


            registrationRunning =
                false;


            showRegistrationError(
                'Unable to start the camera. Please allow camera access and try again.'
            );


            faceRegistrationStatus.textContent =
                '🔴 Camera unavailable.';


            stopRegistrationCamera();

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FACE DETECTION LOOP
    |--------------------------------------------------------------------------
    */

    async function registrationDetectionLoop() {

        if (!registrationRunning) {

            return;
        }


        try {

            if (
                faceRegistrationVideo.readyState >=
                HTMLMediaElement.HAVE_CURRENT_DATA
            ) {


                const detection =
                    await faceapi
                        .detectSingleFace(
                            faceRegistrationVideo,
                            new faceapi.TinyFaceDetectorOptions({

                                inputSize: 416,

                                scoreThreshold: 0.70

                            })
                        )
                        .withFaceLandmarks()
                        .withFaceDescriptor();


                /*
                |--------------------------------------------------------------------------
                | NO FACE
                |--------------------------------------------------------------------------
                */

                if (!detection) {

                    latestFaceDetection =
                        null;

                    captureFaceRegistrationButton.disabled =
                        true;

                    faceRegistrationStatus.textContent =
                        '🟡 No face detected.';

                    faceRegistrationInstruction.textContent =
                        'Look directly at the camera and position your face in the centre.';


                } else {

                    const box =
                        detection.detection.box;


                    const videoWidth =
                        faceRegistrationVideo.videoWidth;


                    const videoHeight =
                        faceRegistrationVideo.videoHeight;


                    /*
                    |--------------------------------------------------------------------------
                    | FACE SIZE
                    |--------------------------------------------------------------------------
                    */

                    const faceWidthRatio =
                        box.width /
                        videoWidth;


                    const faceHeightRatio =
                        box.height /
                        videoHeight;


                    /*
                    |--------------------------------------------------------------------------
                    | FACE CENTRE
                    |--------------------------------------------------------------------------
                    */

                    const faceCenterX =
                        box.x +
                        (box.width / 2);


                    const faceCenterY =
                        box.y +
                        (box.height / 2);


                    const videoCenterX =
                        videoWidth / 2;


                    const videoCenterY =
                        videoHeight / 2;


                    const centerDifferenceX =
                        Math.abs(
                            faceCenterX -
                            videoCenterX
                        ) /
                        videoWidth;


                    const centerDifferenceY =
                        Math.abs(
                            faceCenterY -
                            videoCenterY
                        ) /
                        videoHeight;


                    /*
                    |--------------------------------------------------------------------------
                    | VALID FACE POSITION
                    |--------------------------------------------------------------------------
                    |
                    | These values are intentionally similar
                    | to your existing recognition system.
                    |
                    */

                    const faceIsLargeEnough =
                        faceWidthRatio >= 0.18 &&
                        faceHeightRatio >= 0.18;


                    const faceIsCentred =
                        centerDifferenceX <= 0.20 &&
                        centerDifferenceY <= 0.20;


                    /*
                    |--------------------------------------------------------------------------
                    | FACE READY
                    |--------------------------------------------------------------------------
                    */

                    if (
                        faceIsLargeEnough &&
                        faceIsCentred
                    ) {

                        latestFaceDetection =
                            detection;


                        captureFaceRegistrationButton.disabled =
                            false;


                        faceRegistrationStatus.textContent =
                            '🟢 Face detected and ready.';


                        faceRegistrationInstruction.textContent =
                            'Keep still, look directly at the camera, then click Capture & Register.';


                    } else {

                        latestFaceDetection =
                            null;


                        captureFaceRegistrationButton.disabled =
                            true;


                        if (!faceIsLargeEnough) {

                            faceRegistrationStatus.textContent =
                                '🟡 Move closer to the camera.';

                            faceRegistrationInstruction.textContent =
                                'Make sure your face is clearly visible and large enough.';

                        } else {

                            faceRegistrationStatus.textContent =
                                '🟡 Centre your face.';

                            faceRegistrationInstruction.textContent =
                                'Move your face to the centre of the camera.';

                        }

                    }

                }

            }

        } catch (error) {

            console.error(
                'Face registration detection error:',
                error
            );

        }


        /*
        | Continue checking.
        */

        if (registrationRunning) {

            setTimeout(
                registrationDetectionLoop,
                300
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CAPTURE & REGISTER
    |--------------------------------------------------------------------------
    */

    async function captureAndRegisterFace() {

        if (!currentEmployeeId) {

            showRegistrationError(
                'Please select an employee first.'
            );

            return;
        }


        /*
        | Require a fresh face detection.
        */

        if (!latestFaceDetection) {

            showRegistrationError(
                'No valid face detected. Please position your face correctly and try again.'
            );

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | STOP DETECTION
        |--------------------------------------------------------------------------
        */

        registrationRunning =
            false;


        captureFaceRegistrationButton.disabled =
            true;


        faceRegistrationActions.style.display =
            'none';


        faceRegistrationSaving.style.display =
            'block';


        faceRegistrationStatus.textContent =
            '🟡 Registering your face...';


        faceRegistrationInstruction.textContent =
            'Please wait. Do not close this window.';


        try {

            /*
            |--------------------------------------------------------------------------
            | GET 128-VALUE DESCRIPTOR
            |--------------------------------------------------------------------------
            */

            const descriptor =
                Array.from(
                    latestFaceDetection.descriptor
                );


            if (
                descriptor.length !== 128
            ) {

                throw new Error(
                    'Invalid face descriptor. Please capture your face again.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | SEND TO LARAVEL
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    `/attendance/register-face/${currentEmployeeId}`,
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
                                    .getAttribute(
                                        'content'
                                    )

                        },

                        body: JSON.stringify({

                            face_descriptor:
                                descriptor

                        })

                    }
                );


            let data = {};

            try {

                data =
                    await response.json();

            } catch (jsonError) {

                data = {};

            }


            /*
            |--------------------------------------------------------------------------
            | SERVER ERROR
            |--------------------------------------------------------------------------
            */

            if (!response.ok) {

                throw new Error(
                    data.message ||
                    'Face registration failed.'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            if (data.success) {

                faceRegistrationStatus.textContent =
                    '🟢 Face registered successfully.';


                faceRegistrationInstruction.textContent =
                    'Your face has been registered.';


                faceRegistrationSaving.textContent =
                    'Face registration complete.';


                /*
                | Update the selected option.
                |
                | This means the registration button
                | immediately disappears without
                | refreshing the page.
                */

                const selectedOption =
                    employeeSelect.options[
                        employeeSelect.selectedIndex
                    ];


                if (
                    selectedOption &&
                    selectedOption.value === currentEmployeeId
                ) {

                    selectedOption.dataset.faceRegistered =
                        '1';

                }


                /*
                | Hide registration area.
                */

                faceRegistrationArea.style.display =
                    'none';


                /*
                | Close camera after short delay.
                */

                setTimeout(
                    function () {

                        closeFaceRegistrationCamera();

                    },
                    1200
                );


            } else {

                throw new Error(
                    data.message ||
                    'Face registration failed.'
                );

            }


        } catch (error) {

            console.error(
                'Face registration error:',
                error
            );


            registrationRunning =
                false;


            faceRegistrationSaving.style.display =
                'none';


            faceRegistrationActions.style.display =
                'flex';


            captureFaceRegistrationButton.disabled =
                false;


            faceRegistrationStatus.textContent =
                '🔴 Registration failed.';


            showRegistrationError(
                error.message ||
                'Unable to register the face. Please try again.'
            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | SHOW ERROR
    |--------------------------------------------------------------------------
    */

    function showRegistrationError(message) {

        if (!faceRegistrationError) {

            return;
        }


        faceRegistrationError.textContent =
            message;


        faceRegistrationError.style.display =
            'block';

    }


    /*
    |--------------------------------------------------------------------------
    | STOP REGISTRATION CAMERA
    |--------------------------------------------------------------------------
    */

    function stopRegistrationCamera() {

        registrationRunning =
            false;


        latestFaceDetection =
            null;


        if (registrationStream) {

            registrationStream
                .getTracks()
                .forEach(
                    track => track.stop()
                );

        }


        registrationStream =
            null;


        if (faceRegistrationVideo) {

            faceRegistrationVideo.pause();

            faceRegistrationVideo.srcObject =
                null;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE REGISTRATION CAMERA
    |--------------------------------------------------------------------------
    */

    function closeFaceRegistrationCamera() {

        stopRegistrationCamera();


        faceRegistrationOverlay.style.display =
            'none';


        faceRegistrationSaving.style.display =
            'none';


        faceRegistrationActions.style.display =
            'flex';


        captureFaceRegistrationButton.disabled =
            true;


        captureFaceRegistrationButton.textContent =
            '📸 CAPTURE & REGISTER';


        faceRegistrationError.textContent =
            '';

        faceRegistrationError.style.display =
            'none';


        faceRegistrationStatus.textContent =
            '🟡 Starting camera...';


        faceRegistrationInstruction.textContent =
            'Position your face in the centre of the camera.';

    }


    /*
    |--------------------------------------------------------------------------
    | REGISTER MY FACE BUTTON
    |--------------------------------------------------------------------------
    */

    registerMyFaceButton.addEventListener(
        'click',
        function () {

            /*
            | Re-check the selected employee.
            */

            updateFaceRegistrationVisibility();


            if (!currentEmployeeId) {

                return;

            }


            /*
            | Do not allow registration if the
            | employee already has a face.
            */

            const selectedOption =
                employeeSelect.options[
                    employeeSelect.selectedIndex
                ];


            if (
                selectedOption &&
                selectedOption.dataset.faceRegistered === '1'
            ) {

                faceRegistrationArea.style.display =
                    'none';

                return;

            }


            registerMyFaceButton.disabled =
                true;


            openFaceRegistrationCamera()
                .finally(
                    function () {

                        /*
                        | If camera failed, allow
                        | trying again.
                        */

                        if (
                            !registrationStream
                        ) {

                            registerMyFaceButton.disabled =
                                false;

                        }

                    }
                );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CAPTURE BUTTON
    |--------------------------------------------------------------------------
    */

    captureFaceRegistrationButton.addEventListener(
        'click',
        captureAndRegisterFace
    );


    /*
    |--------------------------------------------------------------------------
    | CANCEL BUTTON
    |--------------------------------------------------------------------------
    */

    if (cancelFaceRegistrationButton) {

        cancelFaceRegistrationButton.addEventListener(
            'click',
            function () {

                closeFaceRegistrationCamera();

                registerMyFaceButton.disabled =
                    false;

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE X BUTTON
    |--------------------------------------------------------------------------
    */

    if (faceRegistrationClose) {

        faceRegistrationClose.addEventListener(
            'click',
            function () {

                closeFaceRegistrationCamera();

                registerMyFaceButton.disabled =
                    false;

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | CLICK OUTSIDE MODAL
    |--------------------------------------------------------------------------
    |
    | Optional safety:
    | clicking the dark background does not
    | accidentally close the registration.
    |
    */


    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateFaceRegistrationVisibility();


    /*
    |--------------------------------------------------------------------------
    | BEFORE PAGE CLOSE
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        'beforeunload',
        function () {

            stopRegistrationCamera();

        }
    );

});
</script>

@vite('resources/js/attendance-face-recognition.js')
</body>

</html>
