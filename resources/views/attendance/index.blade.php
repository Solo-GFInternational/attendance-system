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

    {{-- One shared copy of face-api for recognition and registration.
         Deferred so the page shows straight away; models are preloaded after. --}}
    <script defer src="{{ asset('js/face-api/face-api.js') }}"></script>

    @include('partials.kiosk-theme')

    <style>

        /* =========================================================
           Employee Attendance page. Shared header, colours and
           buttons come from partials/kiosk-theme. Class names and
           hidden-by-default states below are used by the page
           scripts, so keep them.
        ========================================================= */

        .attendance-layout {
            max-width: 880px;
            margin: 0 auto;
        }


        /* =========================================================
           ALERTS
        ========================================================= */

        .alert {
            padding: 14px 18px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            line-height: 1.45;
        }

        .alert-success {
            background: var(--green-soft);
            border-color: var(--green-border);
            color: var(--green-text);
        }

        .alert-error {
            background: var(--red-soft);
            border-color: var(--red-border);
            color: var(--red-text);
        }


        /* =========================================================
           AUTO RETURN TO MAIN MENU
        ========================================================= */

        .auto-return-box {
            display: none;
            margin-bottom: 20px;
            padding: 14px 18px;
            background: var(--blue-soft);
            border: 1px solid var(--blue-border);
            border-radius: 12px;
            color: var(--blue-text);
            font-weight: 600;
            text-align: center;
        }

        .auto-return-box strong {
            font-size: 18px;
            font-variant-numeric: tabular-nums;
        }


        /* =========================================================
           MAIN CARD
        ========================================================= */

        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 22px;
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .card-title .k-card-icon {
            width: 40px;
            height: 40px;
        }


        /* =========================================================
           FACE RECOGNITION
        ========================================================= */

        .face-recognition-box {
            margin-bottom: 24px;
            padding: 24px;
            background: var(--blue-soft);
            border: 1px solid var(--blue-border);
            border-radius: 14px;
            text-align: center;
        }

        .face-recognition-title {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 18px;
            font-weight: 700;
            color: var(--blue-text);
        }

        .face-recognition-title svg {
            width: 22px;
            height: 22px;
        }

        .face-recognition-status {
            margin: 8px 0 16px;
            color: var(--text-2);
            font-size: 15px;
        }

        /* Camera frame sized for tablets: as wide as fits, but never taller
           than about half the screen so the button stays visible.
           Fixed 4:3 frame; object-fit: cover crops the stream to fit. */
        .face-recognition-frame {
            position: relative;
            display: none;
            width: min(100%, 760px, calc(52vh * 4 / 3));
            margin: 0 auto 18px;
            border-radius: 18px;
            overflow: hidden;
            background: #020617;
            box-shadow: 0 8px 24px rgba(15, 31, 61, .25);
        }

        .face-recognition-frame.is-active {
            display: block;
        }

        .face-recognition-video {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            height: auto;
            object-fit: cover;
            /* Mirror like a selfie camera so people move the right way.
               Display only: recognition reads the real camera image. */
            transform: scaleX(-1);
        }

        /* Oval to fit the face in: big enough and centred is exactly
           what recognition waits for, so people get it right first time. */
        .face-recognition-guide {
            position: absolute;
            left: 50%;
            top: 50%;
            width: 40%;
            aspect-ratio: 3 / 4;
            transform: translate(-50%, -50%);
            border: 3px dashed rgba(255, 255, 255, .85);
            border-radius: 50%;
            box-shadow: 0 0 0 100vmax rgba(2, 6, 23, .28);
            pointer-events: none;
        }

        .face-recognition-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 54px;
            padding: 0 28px;
            border: none;
            border-radius: 12px;
            background: var(--blue);
            color: #ffffff;
            font-family: inherit;
            font-size: 17px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease;
        }

        .face-recognition-button svg {
            width: 22px;
            height: 22px;
            flex-shrink: 0;
        }

        .face-recognition-button:hover:not(:disabled) {
            background: var(--blue-dark);
        }

        .face-recognition-button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 0 0 20px;
            color: var(--faint);
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--border);
        }


        /* =========================================================
           EMPLOYEE SEARCH + SELECT
        ========================================================= */

        .employee-search-wrapper {
            width: 100%;
        }

        .employee-search-label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-2);
        }

        .employee-search,
        .employee-select {
            width: 100%;
            min-height: 54px;
            padding: 14px 16px;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            background: var(--surface);
            color: var(--text);
            font-family: inherit;
            font-size: 16px;
            outline: none;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .employee-search {
            margin-bottom: 10px;
            padding-left: 46px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath d='m21 21-4.3-4.3'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: 16px center;
            background-size: 19px;
        }

        .employee-search::placeholder {
            color: var(--faint);
        }

        .employee-search:focus,
        .employee-select:focus {
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(31, 95, 214, .14);
        }

        .employee-select option {
            padding: 12px;
        }

        .employee-search-message {
            display: none;
            margin-top: 10px;
            padding: 14px;
            background: var(--subtle);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }

        /* =========================================================
           NEW EMPLOYEE FACE SETUP
        ========================================================= */

        .face-setup {
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--surface);
        }

        .face-setup summary {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 16px 18px;
            cursor: pointer;
            list-style: none;
        }

        .face-setup summary::-webkit-details-marker {
            display: none;
        }

        .face-setup summary .k-card-icon {
            width: 40px;
            height: 40px;
            flex-shrink: 0;
        }

        .face-setup-text {
            display: flex;
            flex-direction: column;
            gap: 2px;
            min-width: 0;
        }

        .face-setup-text strong {
            font-size: 16px;
        }

        .face-setup-text small {
            color: var(--muted);
            font-size: 13px;
        }

        .face-setup-count {
            margin-left: auto;
            min-width: 30px;
            padding: 3px 10px;
            border-radius: 999px;
            background: var(--violet-soft);
            color: var(--violet);
            font-size: 13px;
            font-weight: 700;
            text-align: center;
        }

        .face-setup[open] summary {
            border-bottom: 1px solid var(--border);
        }

        .face-setup-body {
            padding: 16px 18px 18px;
        }

        .setup-list {
            display: grid;
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
        }

        .setup-item {
            display: flex;
            align-items: center;
            gap: 12px;
            width: 100%;
            min-height: 60px;
            padding: 10px 14px;
            border: 1px solid var(--border);
            border-radius: 12px;
            background: var(--surface);
            color: var(--text);
            font-family: inherit;
            text-align: left;
            cursor: pointer;
            transition: border-color .15s ease, background .15s ease;
        }

        .setup-item:hover {
            border-color: var(--violet-border);
            background: var(--violet-soft);
        }

        .setup-item.selected {
            border-color: var(--violet);
            background: var(--violet-soft);
        }

        .setup-avatar {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            border-radius: 50%;
            background: var(--violet-soft);
            color: var(--violet);
            font-size: 14px;
            font-weight: 700;
        }

        .setup-name {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .setup-name strong {
            font-size: 15px;
        }

        .setup-name small {
            color: var(--muted);
            font-size: 13px;
        }

        .setup-empty {
            padding: 14px;
            color: var(--muted);
            text-align: center;
            font-size: 14px;
        }


        /* =========================================================
           EMPLOYEE STATUS
        ========================================================= */

        .employee-status {
            display: none;
            margin-top: 20px;
            padding: 18px 20px;
            background: var(--subtle);
            border: 1px solid var(--border);
            border-left: 4px solid var(--blue);
            border-radius: 12px;
        }

        .employee-name {
            font-size: 20px;
            font-weight: 700;
        }

        .employee-code {
            margin-top: 3px;
            color: var(--muted);
            font-size: 14px;
        }

        .status-message {
            margin-top: 10px;
            font-size: 15px;
            color: var(--text-2);
        }


        /* =========================================================
           ACTION BUTTONS
        ========================================================= */

        /* Only the buttons that apply are shown (others are display:none),
           so one button fills the row and two share it. */
        .actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .action-button svg {
            width: 22px;
            height: 22px;
        }


        /* =========================================================
           GREETING POP-UP
        ========================================================= */

        .greeting-modal {
            max-width: 560px;
        }

        .greeting-head {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 22px 24px;
            background: linear-gradient(180deg, var(--navy) 0%, var(--navy-2) 100%);
            color: #ffffff;
        }

        .greeting-avatar {
            display: grid;
            place-items: center;
            width: 58px;
            height: 58px;
            flex-shrink: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, .14);
            border: 2px solid rgba(255, 255, 255, .35);
            font-size: 21px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .greeting-who {
            min-width: 0;
        }

        .greeting-hi {
            font-size: 26px;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: -.01em;
        }

        .greeting-head .employee-code {
            margin-top: 4px;
            color: #b6c2d9;
        }

        .greeting-close {
            display: grid;
            place-items: center;
            width: 42px;
            height: 42px;
            margin-left: auto;
            flex-shrink: 0;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 10px;
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
            font-size: 26px;
            line-height: 1;
            cursor: pointer;
        }

        .greeting-close:hover {
            background: rgba(255, 255, 255, .16);
        }

        .greeting-body {
            padding: 22px 24px 18px;
        }

        .greeting-modal .employee-status {
            margin: 0;
            padding: 0;
            background: none;
            border: 0;
        }

        .greeting-modal .status-message {
            margin: 0;
            font-size: 17px;
            color: var(--text);
        }

        .greeting-modal .history {
            margin-top: 20px;
            padding-top: 16px;
        }

        .greeting-foot {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 8px;
            margin-top: 20px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
            color: var(--muted);
            font-size: 14px;
        }

        .greeting-link {
            padding: 0;
            border: 0;
            background: none;
            color: var(--blue);
            font-family: inherit;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
        }

        .greeting-foot strong {
            font-variant-numeric: tabular-nums;
        }


        /* Register My Face (inside "New employee?" section) */

        .setup-register {
            margin-top: 14px;
            padding: 16px;
            background: var(--violet-soft);
            border: 1px solid var(--violet-border);
            border-radius: 12px;
        }

        .setup-register-message {
            margin-bottom: 12px;
            color: var(--violet-text);
            font-size: 15px;
            font-weight: 600;
            text-align: center;
        }

        .setup-register .action-button {
            width: 100%;
        }

        .actions form {
            margin: 0;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            width: 100%;
            min-height: 64px;
            padding: 0 18px;
            border: none;
            border-radius: 12px;
            font-family: inherit;
            font-size: 17px;
            font-weight: 700;
            letter-spacing: .02em;
            color: #ffffff;
            cursor: pointer;
            transition: background .15s ease, opacity .15s ease;
        }

        .action-button:disabled {
            opacity: .38;
            cursor: not-allowed;
        }

        .action-button:focus-visible {
            outline: 3px solid rgba(31, 95, 214, .45);
            outline-offset: 2px;
        }

        .sign-in   { background: var(--green); }
        .sign-in:hover:not(:disabled)   { background: var(--green-dark); }

        .going-out { background: #c46a0a; }
        .going-out:hover:not(:disabled) { background: #a85a08; }

        .returning { background: var(--blue); }
        .returning:hover:not(:disabled) { background: var(--blue-dark); }

        .sign-out  { background: var(--red); }
        .sign-out:hover:not(:disabled)  { background: var(--red-dark); }

        #registerMyFaceButton {
            background: #6d3fc0 !important; /* solid in both themes: white text sits on it */
        }


        /* =========================================================
           GOING OUT REASON
        ========================================================= */

        .reason-box {
            display: none;
            margin-top: 16px;
            padding: 18px;
            background: var(--amber-soft);
            border: 1px solid var(--amber-border);
            border-radius: 12px;
        }

        .reason-box label {
            display: block;
            margin-bottom: 7px;
            font-size: 14px;
            font-weight: 700;
            color: var(--amber-text);
        }

        .reason-required {
            color: var(--red);
            margin-left: 3px;
        }

        .reason-box input {
            width: 100%;
            min-height: 52px;
            padding: 13px 15px;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-sm);
            background: var(--surface);
            font-family: inherit;
            font-size: 16px;
            outline: none;
        }

        .reason-box input:focus {
            border-color: #c46a0a;
            box-shadow: 0 0 0 4px rgba(196, 106, 10, .14);
        }

        .reason-box input.invalid {
            border-color: var(--red);
            box-shadow: 0 0 0 4px rgba(198, 47, 59, .12);
        }

        .reason-help {
            margin-top: 6px;
            color: var(--amber-text);
            font-size: 12.5px;
        }

        .reason-error {
            display: none;
            margin-top: 8px;
            color: var(--red);
            font-size: 13px;
            font-weight: 700;
        }


        /* =========================================================
           OUTING HISTORY (rows are built by the page script)
        ========================================================= */

        .history {
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
        }

        .history-title {
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: 700;
        }

        .outing-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }

        .outing-row:last-child {
            border-bottom: none;
        }

        .outing-reason {
            margin-top: 3px;
            color: var(--muted);
            font-size: 13px;
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
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 14px;
            margin-top: 20px;
        }

        .quick-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 18px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 14px;
            box-shadow: var(--shadow);
            color: var(--text);
            font-weight: 600;
            text-decoration: none;
            transition: box-shadow .2s ease, border-color .2s ease;
        }

        .quick-link:hover {
            border-color: var(--border-strong);
            box-shadow: var(--shadow-hover);
        }

        .quick-link .k-card-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
        }

        .quick-link .k-card-icon svg {
            width: 19px;
            height: 19px;
        }

        .quick-link .chev {
            margin-left: auto;
            width: 18px;
            height: 18px;
            color: var(--faint);
        }


        /* =========================================================
           CAMERA MODALS
        ========================================================= */

        .camera-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 31, 61, .82);
            backdrop-filter: blur(3px);
            z-index: 9999;
        }

        .camera-modal {
            width: 100%;
            max-width: 660px;
            max-height: calc(100vh - 40px);
            overflow: auto;
            background: var(--surface);
            border-radius: 18px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, .35);
        }

        .camera-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            background: linear-gradient(180deg, var(--navy) 0%, var(--navy-2) 100%);
            color: #ffffff;
        }

        .camera-title {
            font-size: 18px;
            font-weight: 700;
        }

        .camera-subtitle {
            margin-top: 3px;
            color: #b6c2d9;
            font-size: 14px;
        }

        .camera-close {
            display: grid;
            place-items: center;
            width: 40px;
            height: 40px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 10px;
            background: rgba(255, 255, 255, .08);
            color: #ffffff;
            font-size: 24px;
            line-height: 1;
            cursor: pointer;
        }

        .camera-close:hover {
            background: rgba(255, 255, 255, .16);
        }

        .camera-body {
            padding: 22px;
        }

        .camera-preview,
        .photo-preview {
            width: 100%;
            aspect-ratio: 4 / 3;
            height: auto;
            max-height: 42vh;
            background: #020617;
            border-radius: 14px;
            object-fit: cover;
        }

        .camera-preview {
            display: block;
        }

        .photo-preview {
            display: none;
        }

        .camera-instruction {
            margin: 12px 0 16px;
            text-align: center;
            color: var(--text-2);
            font-size: 14px;
        }

        .face-status {
            margin: 16px 0 6px;
            padding: 12px;
            border-radius: 10px;
            background: var(--red-soft);
            color: var(--red-text);
            text-align: center;
            font-size: 15px;
            font-weight: 700;
            transition: background .2s ease, color .2s ease;
        }

        .camera-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
            gap: 12px;
        }

        .camera-button {
            min-height: 54px;
            padding: 0 16px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s ease, opacity .15s ease;
        }

        .camera-button:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        .take-photo    { background: var(--green); color: #ffffff; }
        .confirm-photo { background: var(--blue);  color: #ffffff; }
        .retake-photo  { background: #64748b;      color: #ffffff; }
        .cancel-photo  { background: var(--surface); color: var(--text-2); border-color: var(--border-strong); }
        .cancel-photo:hover { background: var(--hover); }

        .camera-error {
            display: none;
            margin-bottom: 16px;
            padding: 13px 14px;
            background: var(--red-soft);
            border: 1px solid var(--red-border);
            border-radius: 10px;
            color: var(--red-text);
            font-size: 14px;
        }

        .saving-message {
            display: none;
            padding: 14px;
            text-align: center;
            font-weight: 700;
            color: var(--blue);
        }


        /* =========================================================
           MOBILE
        ========================================================= */

        @media (max-width: 750px) {

            .card {
                padding: 20px;
            }

            .face-recognition-box {
                padding: 18px;
            }

            .actions,
            .camera-actions {
                grid-template-columns: 1fr;
            }
        }

        /* Tablets: larger, easier-to-read recognition text and button. */
        @media (min-width: 600px) {

            .face-recognition-status {
                font-size: 18px;
                font-weight: 600;
            }

            .face-recognition-button {
                min-height: 62px;
                padding: 0 36px;
                font-size: 19px;
            }
        }

    </style>

</head>


<body>


@php
    $kioskSite = $selectedSite ? \App\Models\Site::find($selectedSite) : null;
@endphp

{{-- #liveClock / #liveDate in this header are updated by the page script below. --}}
@include('partials.kiosk-header', [
    'variant'     => 'page',
    'title'       => 'Employee Attendance',
    'subtitle'    => 'Sign in, sign out, going out or returning.',
    'icon'        => 'users',
    'site'        => $kioskSite,
    'clockScript' => false,
])


<main class="k-main">
<div class="attendance-layout">


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
            <span class="k-card-icon">@include('partials.kiosk-icon', ['name' => 'user'])</span>
            Identify Yourself
        </div>
<!-- =====================================================
     FACE RECOGNITION
====================================================== -->

<div class="face-recognition-box">

    <div class="face-recognition-title">
        @include('partials.kiosk-icon', ['name' => 'scan'])
        Face Recognition
    </div>

    <div
        id="recognitionStatus"
        class="face-recognition-status"
    >
        Click below to identify yourself automatically.
    </div>

    <div class="face-recognition-frame" id="recognitionFrame">
        <video
            id="recognitionVideo"
            class="face-recognition-video"
            autoplay
            muted
            playsinline
        ></video>
        <div class="face-recognition-guide" aria-hidden="true"></div>
    </div>

    <button
        type="button"
        id="startRecognitionButton"
        class="face-recognition-button"
    >
        @include('partials.kiosk-icon', ['name' => 'camera'])
        Start Face Recognition
    </button>

</div>

        {{-- =====================================================
             EMPLOYEE (selected automatically by face recognition)

             Hidden: face recognition sets its value, and the page
             scripts read the selected employee from it. Employees
             are not picked by name any more.
        ====================================================== --}}

        <select
            id="employeeSelect"
            class="employee-select"
            style="display:none;"
            aria-hidden="true"
            tabindex="-1"
        >
            <option value=""></option>

            @foreach($employees as $employee)
                <option
                    value="{{ $employee->id }}"
                    data-name="{{ $employee->name }}"
                    data-code="{{ $employee->employee_code }}"
                    data-face-registered="{{ $employee->face_descriptor ? '1' : '0' }}"
                >{{ $employee->name }}</option>
            @endforeach
        </select>


        {{-- =====================================================
             NEW EMPLOYEE: FACE SETUP
             Lists only employees who have not set up their face yet.
        ====================================================== --}}

        @php
            $needsFaceSetup = $employees->filter(fn ($e) => empty($e->face_descriptor))->values();
        @endphp

        @if($needsFaceSetup->isNotEmpty())

            <details class="face-setup" id="faceSetup">

                <summary>
                    <span class="k-card-icon violet">@include('partials.kiosk-icon', ['name' => 'user'])</span>
                    <span class="face-setup-text">
                        <strong>New employee? Set up face recognition</strong>
                        <small>One-time setup for employees who have not registered their face yet.</small>
                    </span>
                    <span class="face-setup-count" id="faceSetupCount">{{ $needsFaceSetup->count() }}</span>
                </summary>

                <div class="face-setup-body">

                    <input
                        type="text"
                        id="setupSearch"
                        class="employee-search"
                        placeholder="Search your name or employee ID…"
                        autocomplete="off"
                        autocapitalize="none"
                        spellcheck="false"
                        aria-label="Search your name or employee ID"
                    >

                    <div class="setup-list" id="setupList">
                        @foreach($needsFaceSetup as $employee)
                            @php
                                $initials = collect(preg_split('/\s+/', trim($employee->name)))
                                    ->filter()->take(2)
                                    ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                    ->implode('');
                            @endphp
                            <button
                                type="button"
                                class="setup-item"
                                data-id="{{ $employee->id }}"
                                data-search="{{ mb_strtolower($employee->name . ' ' . $employee->employee_code) }}"
                            >
                                <span class="setup-avatar">{{ $initials }}</span>
                                <span class="setup-name">
                                    <strong>{{ $employee->name }}</strong>
                                    <small>
                                        ID: {{ $employee->employee_code }}@if($employee->department) · {{ $employee->department }}@endif
                                    </small>
                                </span>
                            </button>
                        @endforeach
                    </div>

                    <div class="setup-empty" id="setupEmpty" style="display:none;">
                        No matching employees. If your face is already set up, use face recognition above.
                    </div>

                    {{-- Shown after a name is picked above. --}}
                    <div
                        id="faceRegistrationArea"
                        class="setup-register"
                        style="display:none;"
                    >
                        <div
                            id="faceRegistrationMessage"
                            class="setup-register-message"
                        >
                            Your face has not been registered yet.
                        </div>

                        <button
                            type="button"
                            id="registerMyFaceButton"
                            class="action-button"
                        >
                            📸 REGISTER MY FACE
                        </button>
                    </div>

                </div>

            </details>

        @endif

    </div>


    {{-- =========================================================
         QUICK LINKS
    ========================================================= --}}

    <div class="quick-links">


        <a href="{{ route('visitors.index') }}" class="quick-link">
            <span class="k-card-icon green">@include('partials.kiosk-icon', ['name' => 'badge'])</span>
            Visitor Register
            <span class="chev">@include('partials.kiosk-icon', ['name' => 'arrow'])</span>
        </a>

        <a href="{{ route('attendance.emergency') }}" class="quick-link">
            <span class="k-card-icon red">@include('partials.kiosk-icon', ['name' => 'alert'])</span>
            Emergency Roll Call
            <span class="chev">@include('partials.kiosk-icon', ['name' => 'arrow'])</span>
        </a>

        @auth
            <a href="{{ route('employees.index') }}" class="quick-link">
                <span class="k-card-icon">@include('partials.kiosk-icon', ['name' => 'users'])</span>
                Employee Management
                <span class="chev">@include('partials.kiosk-icon', ['name' => 'arrow'])</span>
            </a>
        @endauth

    </div>

</div>
</main>


<footer class="k-footer">
    @include('partials.kiosk-icon', ['name' => 'info'])
    Please remember to sign out when you leave the premises.
</footer>



{{-- =========================================================
     GREETING POP-UP
     Opens after face recognition: "Hi, Name" with only the
     actions that apply right now (sign in / going out + sign out /
     returning). Element IDs are used by the page scripts.
========================================================= --}}

<div
    id="greetingOverlay"
    class="camera-overlay"
>

    <div
        class="camera-modal greeting-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="greetingTitle"
    >

        <div class="greeting-head">

            <div class="greeting-avatar" id="greetingAvatar" aria-hidden="true"></div>

            <div class="greeting-who">
                <div class="greeting-hi" id="greetingTitle">
                    Hi, <span id="employeeName">-</span>
                </div>
                <div id="employeeCode" class="employee-code">-</div>
            </div>

            <button
                type="button"
                class="greeting-close"
                id="greetingClose"
                aria-label="Close"
            >×</button>

        </div>


        <div class="greeting-body">

            <div
                id="employeeStatus"
                class="employee-status"
            >
                <div
                    id="statusMessage"
                    class="status-message"
                ></div>
            </div>


            {{-- ACTIONS: only the ones that apply are shown --}}

            <div class="actions">

                <button
                    type="button"
                    id="signInButton"
                    class="action-button sign-in"
                    disabled
                >
                    @include('partials.kiosk-icon', ['name' => 'login'])
                    SIGN IN
                </button>

                <button
                    type="button"
                    id="goingOutButton"
                    class="action-button going-out"
                    disabled
                >
                    @include('partials.kiosk-icon', ['name' => 'walk'])
                    GOING OUT
                </button>

                <button
                    type="button"
                    id="returningButton"
                    class="action-button returning"
                    disabled
                >
                    @include('partials.kiosk-icon', ['name' => 'return'])
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
                        @include('partials.kiosk-icon', ['name' => 'logout'])
                        SIGN OUT
                    </button>
                </form>

            </div>


            {{-- GOING OUT REASON --}}

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


            {{-- TODAY'S OUTINGS --}}

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


            <div class="greeting-foot">
                <span>
                    Not you?
                    <button type="button" class="greeting-link" id="greetingRetry">Scan again</button>
                </span>
                <span>Closes in <strong id="greetingCountdown">15</strong>s</span>
            </div>

        </div>

    </div>

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
                    background:var(--blue-soft);
                    border:1px solid var(--blue-border);
                    color:var(--blue-text);
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



{{-- MediaPipe was imported here from the internet on every page load but
     never used (initializeFaceDetection is not called), so it was removed
     to make the tablet page load faster. --}}



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


            // Same format as the shared kiosk header on the other pages.
            document.getElementById(
                'liveClock'
            ).textContent =
                now.toLocaleTimeString(
                    'en-FJ',
                    {
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }
                ).toUpperCase();


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
                ).replace(/,/g, '');

        }


        updateClock();


        setInterval(
            updateClock,
            1000
        );


        /* =========================================================
           ELEMENTS
        ========================================================= */

        const employeeSelect =
            document.getElementById(
                'employeeSelect'
            );


        const greetingOverlay =
            document.getElementById('greetingOverlay');

        const greetingAvatar =
            document.getElementById('greetingAvatar');

        const greetingCountdown =
            document.getElementById('greetingCountdown');


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


            if (greetingCountdown)
            {
                greetingCountdown.textContent =
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


                        if (greetingCountdown)
                        {
                            greetingCountdown.textContent =
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
                    'var(--red-soft)';


                faceStatus.style.color =
                    'var(--red-text)';


                return false;

            }


            try
            {

                faceStatus.textContent =
                    '🟡 Loading face detection...';


                faceStatus.style.background =
                    'var(--amber-soft)';


                faceStatus.style.color =
                    'var(--amber-text)';


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
                    'var(--amber-soft)';


                faceStatus.style.color =
                    'var(--amber-text)';


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
                    'var(--red-soft)';


                faceStatus.style.color =
                    'var(--red-text)';


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
                    'var(--green-soft)';


                faceStatus.style.color =
                    'var(--green-text)';


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
                    'var(--red-soft)';


                faceStatus.style.color =
                    'var(--red-text)';


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

        /* =========================================================
           SHOW STATUS IN THE "HI, NAME" POP-UP
           Only the buttons that apply are shown:
             not signed in      -> Sign In
             signed in          -> Going Out, Sign Out
             currently out      -> Returning
        ========================================================= */

        function updateEmployeeStatus(data)
        {
            applyEmployeeStatus(data);

            const option =
                employeeSelect.options[employeeSelect.selectedIndex];

            // Face not set up yet: registration happens in the
            // "New employee?" section, not in the pop-up.
            if (!option || !option.value || option.dataset.faceRegistered === '0')
            {
                return;
            }

            [signInButton, goingOutButton, returningButton, signOutButton].forEach(button =>
            {
                const holder = button.closest('form') || button;
                holder.style.display = button.disabled ? 'none' : '';
            });

            if (data.status === 'completed')
            {
                statusMessage.textContent =
                    'You are done for today. Signed in at ' + data.check_in +
                    ' and signed out at ' + data.check_out + '.';
            }

            openGreeting(option.dataset.name || '');
        }


        function openGreeting(name)
        {
            if (greetingAvatar)
            {
                greetingAvatar.textContent = name
                    .trim()
                    .split(/\s+/)
                    .filter(Boolean)
                    .slice(0, 2)
                    .map(part => part.charAt(0).toUpperCase())
                    .join('');
            }

            reasonBox.style.display = 'none';
            resetReason();

            greetingOverlay.style.display = 'flex';

            // The person is identified, so release the camera.
            if (window.attendanceFaceRecognition)
            {
                window.attendanceFaceRecognition.stopCamera();
            }

            const video = document.getElementById('recognitionVideo');

            if (video)
            {
                video.style.display = 'none';
            }

            resetAutoReturnTimer();
        }


        function closeGreeting(scanAgain)
        {
            greetingOverlay.style.display = 'none';

            employeeSelect.value = '';

            resetEmployeeSelection();

            const recognitionStatus = document.getElementById('recognitionStatus');

            if (recognitionStatus)
            {
                recognitionStatus.textContent =
                    'Click below to identify yourself automatically.';
            }

            if (scanAgain)
            {
                document.getElementById('startRecognitionButton').click();
            }

            resetAutoReturnTimer();
        }


        document.getElementById('greetingClose')
            .addEventListener('click', () => closeGreeting(false));

        document.getElementById('greetingRetry')
            .addEventListener('click', () => closeGreeting(true));


        function applyEmployeeStatus(
            data
        )
        {

            /*
            | Face not set up yet (chosen from the "New employee" list):
            | no attendance actions until registration is done.
            */

            const selectedOption =
                employeeSelect.options[employeeSelect.selectedIndex];

            if (selectedOption && selectedOption.dataset.faceRegistered === '0')
            {
                signInButton.disabled = true;
                goingOutButton.disabled = true;
                returningButton.disabled = true;
                signOutButton.disabled = true;

                statusMessage.textContent =
                    'Register your face below to continue. After that you will be recognised automatically.';

                return;
            }


            if (data.status === 'not_signed_in')
            {
                statusMessage.textContent =
                    'Not signed in yet today.';

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
            'Hi ' + employeeName + ', tap below to register your face. Look straight at the camera.';


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


        // Shared with face recognition: usually already loaded, so this is instant.
        if (window.attendanceFaceRecognition && window.attendanceFaceRecognition.loadModels) {

            await window.attendanceFaceRecognition.loadModels();

        } else {

            await Promise.all([
                faceapi.nets.tinyFaceDetector.isLoaded || faceapi.nets.tinyFaceDetector.loadFromUri(MODEL_URL),
                faceapi.nets.faceLandmark68Net.isLoaded || faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL),
                faceapi.nets.faceRecognitionNet.isLoaded || faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
            ]);
        }


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
            | CAPTURE SEVERAL SAMPLES
            |--------------------------------------------------------------------------
            | One frame can be blurred or at an angle. Several samples
            | are averaged on the server into a more reliable face.
            */

            faceRegistrationStatus.textContent =
                '🟡 Hold still… capturing your face.';

            faceRegistrationInstruction.textContent =
                'Keep looking straight at the camera.';

            const descriptors =
                await collectRegistrationSamples(5);


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

                            face_descriptors:
                                descriptors

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
                | Remove them from the "New employee" list and
                | load their attendance buttons.
                */

                onFaceRegistered(currentEmployeeId);


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
    | COLLECT REGISTRATION SAMPLES
    |--------------------------------------------------------------------------
    | Takes `count` good frames (face large, centred and facing the
    | camera). Gives up after 8 seconds.
    */

    function isFacingCamera(landmarks) {

        const centre = points => ({
            x: points.reduce((s, p) => s + p.x, 0) / points.length,
            y: points.reduce((s, p) => s + p.y, 0) / points.length,
        });

        const leftEye = centre(landmarks.getLeftEye());
        const rightEye = centre(landmarks.getRightEye());
        const noseTip = landmarks.getNose()[3];

        const eyeDistance = Math.hypot(rightEye.x - leftEye.x, rightEye.y - leftEye.y);

        if (eyeDistance <= 0) {
            return false;
        }

        const turn = Math.abs(noseTip.x - (leftEye.x + rightEye.x) / 2) / eyeDistance;
        const tilt = Math.abs(rightEye.y - leftEye.y) / eyeDistance;

        return turn <= 0.22 && tilt <= 0.25;
    }

    async function collectRegistrationSamples(count) {

        const samples = [];
        const deadline = Date.now() + 8000;

        while (samples.length < count && Date.now() < deadline) {

            const detection =
                await faceapi
                    .detectSingleFace(
                        faceRegistrationVideo,
                        new faceapi.TinyFaceDetectorOptions({ inputSize: 416, scoreThreshold: 0.70 })
                    )
                    .withFaceLandmarks()
                    .withFaceDescriptor();

            if (detection) {

                const box = detection.detection.box;
                const w = faceRegistrationVideo.videoWidth;
                const h = faceRegistrationVideo.videoHeight;

                const largeEnough = box.width / w >= 0.18 && box.height / h >= 0.18;
                const centred =
                    Math.abs(box.x + box.width / 2 - w / 2) / w <= 0.20 &&
                    Math.abs(box.y + box.height / 2 - h / 2) / h <= 0.20;

                if (largeEnough && centred && isFacingCamera(detection.landmarks)) {

                    samples.push(Array.from(detection.descriptor));

                    faceRegistrationStatus.textContent =
                        '🟡 Capturing… ' + samples.length + ' of ' + count;
                }
            }

            await new Promise(resolve => setTimeout(resolve, 150));
        }

        if (samples.length < 3) {

            throw new Error(
                'Could not get a clear view of your face. Look straight at the camera, hold still and try again.'
            );
        }

        return samples;
    }


    /*
    |--------------------------------------------------------------------------
    | NEW EMPLOYEE SETUP LIST
    |--------------------------------------------------------------------------
    */

    const faceSetup = document.getElementById('faceSetup');
    const setupSearch = document.getElementById('setupSearch');
    const setupList = document.getElementById('setupList');
    const setupEmpty = document.getElementById('setupEmpty');
    const faceSetupCount = document.getElementById('faceSetupCount');

    function filterSetupList() {

        if (!setupList) {
            return;
        }

        const term = (setupSearch ? setupSearch.value : '').trim().toLowerCase();
        let visible = 0;

        setupList.querySelectorAll('.setup-item').forEach(item => {
            const show = !term || item.dataset.search.includes(term);
            item.style.display = show ? '' : 'none';
            if (show) visible++;
        });

        if (setupEmpty) {
            setupEmpty.style.display = visible === 0 ? 'block' : 'none';
        }
    }

    if (setupSearch) {
        setupSearch.addEventListener('input', filterSetupList);
    }

    if (setupList) {

        setupList.addEventListener('click', function (event) {

            const item = event.target.closest('.setup-item');

            if (!item) {
                return;
            }

            setupList.querySelectorAll('.setup-item.selected')
                .forEach(el => el.classList.remove('selected'));

            item.classList.add('selected');

            employeeSelect.value = item.dataset.id;

            employeeSelect.dispatchEvent(new Event('change', { bubbles: true }));

            // Register My Face appears just below the list.
            setTimeout(function () {
                faceRegistrationArea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 50);
        });
    }

    function onFaceRegistered(employeeId) {

        if (setupList) {

            const item = setupList.querySelector('.setup-item[data-id="' + CSS.escape(String(employeeId)) + '"]');

            if (item) {
                item.remove();
            }

            const remaining = setupList.querySelectorAll('.setup-item').length;

            if (faceSetupCount) {
                faceSetupCount.textContent = remaining;
            }

            if (remaining === 0 && faceSetup) {
                faceSetup.style.display = 'none';
            }
        }

        // Reload status: the option is now marked as registered,
        // so the attendance buttons are enabled.
        employeeSelect.dispatchEvent(new Event('change', { bubbles: true }));
    }


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
