
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Visitor Register</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f1f5f9;
            color: #0f172a;
        }


        /* ==========================================
           HEADER
        ========================================== */

        .header {
            background: #0f172a;
            color: white;
            padding: 25px 20px;
            text-align: center;
        }


        .header h1 {
            margin: 0;
            font-size: 36px;
        }


        .header p {
            margin: 8px 0 0;
            font-size: 17px;
            color: #cbd5e1;
        }


        /* ==========================================
           CONTAINER
        ========================================== */

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 25px;
        }


        /* ==========================================
           DATE / TIME
        ========================================== */

        .date-time {
            text-align: center;
            margin-bottom: 25px;
            font-size: 18px;
            font-weight: 600;
        }


        /* ==========================================
           AUTO RETURN
        ========================================== */

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


        /* ==========================================
           ALERTS
        ========================================== */

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }


        .success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }


        .error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }


        /* ==========================================
           GRID
        ========================================== */

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }


        /* ==========================================
           CARD
        ========================================== */

        .card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }


        .card h2 {
            margin-top: 0;
            margin-bottom: 25px;
            font-size: 25px;
        }


        /* ==========================================
           FORM
        ========================================== */

        .form-group {
            margin-bottom: 20px;
        }


        label {
            display: block;
            font-weight: 700;
            margin-bottom: 8px;
            font-size: 16px;
        }


        input,
        textarea {
            width: 100%;
            padding: 15px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 17px;
            font-family: inherit;
        }


        textarea {
            min-height: 100px;
            resize: vertical;
        }


        input:focus,
        textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }


        /* ==========================================
           SIGN IN BUTTON
        ========================================== */

        .sign-in-btn {
            width: 100%;
            border: none;
            background: #16a34a;
            color: white;
            padding: 17px;
            border-radius: 9px;
            font-size: 19px;
            font-weight: 800;
            cursor: pointer;
        }


        .sign-in-btn:hover {
            background: #15803d;
        }


        /* ==========================================
           VISITOR CARD
        ========================================== */

        .visitor {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 15px;
            background: #f8fafc;
        }


        .visitor-old {
            border: 2px solid #f59e0b;
            background: #fffbeb;
        }


        .visitor-name {
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 5px;
        }


        .visitor-company {
            font-size: 15px;
            color: #475569;
            margin-bottom: 10px;
        }


        .visitor-purpose {
            font-size: 15px;
            margin-bottom: 10px;
        }


        .visitor-time {
            font-size: 14px;
            color: #475569;
            margin-bottom: 12px;
            line-height: 1.7;
        }


        /* ==========================================
           OLD VISITOR WARNING
        ========================================== */

        .old-visitor-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
            padding: 11px 12px;
            border-radius: 7px;
            margin-bottom: 12px;
            font-size: 14px;
            font-weight: 700;
            line-height: 1.5;
        }


        /* ==========================================
           SIGN OUT BUTTON
        ========================================== */

        .sign-out-btn {
            width: 100%;
            border: none;
            background: #dc2626;
            color: white;
            padding: 12px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
        }


        .sign-out-btn:hover {
            background: #b91c1c;
        }


        /* ==========================================
           NO VISITORS
        ========================================== */

        .no-visitors {
            text-align: center;
            padding: 30px 10px;
            color: #64748b;
        }


        /* ==========================================
           FOOTER
        ========================================== */

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #64748b;
            font-size: 13px;
        }


        .back-button {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #475569;
            font-weight: 600;
        }


        /* ==========================================
           MOBILE
        ========================================== */

        @media (max-width: 800px) {

            .grid {
                grid-template-columns: 1fr;
            }


            .header h1 {
                font-size: 29px;
            }


            .container {
                padding: 15px;
            }


            .card {
                padding: 22px;
            }

        }

    </style>

</head>


<body>


    {{-- ==========================================
         HEADER
    ========================================== --}}

    <div class="header">

        <h1>
            Goodman Fielder Visitor's Register
        </h1>

        <p>
            Please sign in when you arrive
        </p>

    </div>


    <div class="container">


        {{-- ==========================================
             DATE AND TIME
        ========================================== --}}

        <div class="date-time">

            📅 {{ now()->format('l, d F Y') }}

            &nbsp; | &nbsp;

            🕐

            <span id="currentTime">

                {{ now()->format('h:i:s A') }}

            </span>

        </div>


        {{-- ==========================================
             AUTO RETURN COUNTDOWN
        ========================================== --}}

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


        {{-- ==========================================
             SUCCESS MESSAGE
        ========================================== --}}

        @if(session('success'))

            <div class="alert success">

                ✓ {{ session('success') }}

            </div>

        @endif


        {{-- ==========================================
             ERROR MESSAGE
        ========================================== --}}

        @if(session('error'))

            <div class="alert error">

                ✕ {{ session('error') }}

            </div>

        @endif


        {{-- ==========================================
             VALIDATION ERRORS
        ========================================== --}}

        @if($errors->any())

            <div class="alert error">

                @foreach($errors->all() as $error)

                    <div>
                        {{ $error }}
                    </div>

                @endforeach

            </div>

        @endif


        <div class="grid">


            {{-- ==========================================
                 VISITOR SIGN IN
            ========================================== --}}

            <div class="card">

                <h2>
                    📝 Visitor Sign In
                </h2>


                <form
                    method="POST"
                    action="{{ route('visitors.sign-in') }}"
                >

                    @csrf


                    {{-- NAME --}}

                    <div class="form-group">

                        <label for="name">
                            Name *
                        </label>


                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Enter your full name"
                            required
                            autocomplete="name"
                        >

                    </div>


                    {{-- COMPANY --}}

                    <div class="form-group">

                        <label for="company">
                            Company
                        </label>


                        <input
                            type="text"
                            id="company"
                            name="company"
                            value="{{ old('company') }}"
                            placeholder="Enter company name"
                            autocomplete="organization"
                        >

                    </div>


                    {{-- PURPOSE --}}

                    <div class="form-group">

                        <label for="purpose">
                            Purpose of Visit *
                        </label>


                        <textarea
                            id="purpose"
                            name="purpose"
                            placeholder="Example: Meeting with Accounts Department"
                            required
                        >{{ old('purpose') }}</textarea>

                    </div>


                    {{-- SIGN IN --}}

                    <button
                        type="submit"
                        class="sign-in-btn"
                    >

                        ✓ SIGN IN

                    </button>

                </form>

            </div>


            {{-- ==========================================
                 CURRENT VISITORS
            ========================================== --}}

            <div class="card">

                <h2>
                    👥 Visitors Currently On Site
                </h2>


                {{--

                    IMPORTANT:

                    The controller now returns:

                    • Visitors with sign_out = NULL
                      regardless of the date they signed in.

                    • Visitors who signed out today.

                    Therefore this list can contain
                    forgotten visitors from previous days.

                --}}


                @php

                    $currentVisitors =
                        $visitors->whereNull('sign_out');

                @endphp


                @forelse($currentVisitors as $visitor)


                    @php

                        $isOldVisitor =
                            $visitor->visit_date &&
                            $visitor->visit_date->lt(
                                \Carbon\Carbon::today()
                            );

                    @endphp


                    <div
                        class="visitor {{ $isOldVisitor ? 'visitor-old' : '' }}"
                    >


                        {{-- ==================================
                             VISITOR NAME
                        =================================== --}}

                        <div class="visitor-name">

                            {{ $visitor->name }}

                        </div>


                        {{-- ==================================
                             COMPANY
                        =================================== --}}

                        @if($visitor->company)

                            <div class="visitor-company">

                                🏢

                                {{ $visitor->company }}

                            </div>

                        @endif


                        {{-- ==================================
                             PURPOSE
                        =================================== --}}

                        <div class="visitor-purpose">

                            <strong>
                                Purpose:
                            </strong>

                            {{ $visitor->purpose }}

                        </div>


                        {{-- ==================================
                             SIGN IN INFORMATION
                        =================================== --}}

                        <div class="visitor-time">

                            🕐

                            <strong>
                                Signed in:
                            </strong>

                            {{ $visitor->sign_in->format('h:i A') }}


                            <br>


                            📅

                            <strong>
                                Date:
                            </strong>

                            {{ $visitor->sign_in->format('d F Y') }}

                        </div>


                        {{-- ==================================
                             OLD VISITOR WARNING
                        =================================== --}}

                        @if($isOldVisitor)

                            <div class="old-visitor-warning">

                                ⚠️

                                <strong>
                                    Previous Day Visitor
                                </strong>

                                <br>

                                This visitor signed in on
                                {{ $visitor->sign_in->format('d F Y') }}
                                and has not been signed out.

                                Please confirm whether they have
                                left the site before signing them out.

                            </div>

                        @endif


                        {{-- ==================================
                             SIGN OUT
                        =================================== --}}

                        <form
                            method="POST"
                            action="{{ route('visitors.sign-out', $visitor) }}"
                        >

                            @csrf

                            @method('PATCH')


                            <button
                                type="submit"
                                class="sign-out-btn"
                                onclick="return confirm('Are you sure you want to sign out {{ $visitor->name }}?')"
                            >

                                🚪 SIGN OUT

                            </button>

                        </form>


                    </div>


                @empty


                    <div class="no-visitors">

                        <div style="font-size: 40px;">
                            👤
                        </div>


                        <p>
                            No visitors currently on site.
                        </p>

                    </div>


                @endforelse

            </div>

        </div>


        {{-- ==========================================
             FOOTER
        ========================================== --}}

        <div class="footer">

            Visitor records are automatically recorded
            with date and time.

            <br>


            <a
                href="{{ route('attendance.index') }}"
                class="back-button"
            >

                ← Employee Attendance

            </a>

        </div>

    </div>


    <script>


        /* =====================================================
           LIVE CLOCK
        ===================================================== */

        function updateClock() {

            const now = new Date();


            let hours =
                now.getHours();

            let minutes =
                now.getMinutes();

            let seconds =
                now.getSeconds();


            const ampm =
                hours >= 12
                    ? 'PM'
                    : 'AM';


            hours =
                hours % 12;


            if (hours === 0) {

                hours = 12;

            }


            hours =
                String(hours).padStart(2, '0');


            minutes =
                String(minutes).padStart(2, '0');


            seconds =
                String(seconds).padStart(2, '0');


            const clock =
                document.getElementById(
                    'currentTime'
                );


            if (clock) {

                clock.textContent =
                    `${hours}:${minutes}:${seconds} ${ampm}`;

            }

        }


        setInterval(
            updateClock,
            1000
        );


        updateClock();



        /* =====================================================
           AUTO RETURN TO MAIN MENU
           15 SECONDS OF INACTIVITY
        ===================================================== */

        const autoReturnBox =
            document.getElementById(
                'autoReturnBox'
            );


        const autoReturnCountdown =
            document.getElementById(
                'autoReturnCountdown'
            );


        const AUTO_RETURN_SECONDS = 15;


        let autoReturnTimer = null;


        let autoReturnSeconds =
            AUTO_RETURN_SECONDS;



        /* =====================================================
           STOP TIMER
        ===================================================== */

        function stopAutoReturnTimer() {

            if (autoReturnTimer) {

                clearInterval(
                    autoReturnTimer
                );

                autoReturnTimer = null;

            }

        }



        /* =====================================================
           START TIMER
        ===================================================== */

        function startAutoReturnTimer() {

            stopAutoReturnTimer();


            autoReturnSeconds =
                AUTO_RETURN_SECONDS;


            if (autoReturnCountdown) {

                autoReturnCountdown.textContent =
                    autoReturnSeconds;

            }


            if (autoReturnBox) {

                autoReturnBox.style.display =
                    'block';

            }


            autoReturnTimer =
                setInterval(
                    function () {

                        autoReturnSeconds--;


                        if (autoReturnCountdown) {

                            autoReturnCountdown.textContent =
                                autoReturnSeconds;

                        }


                        if (
                            autoReturnSeconds <= 0
                        ) {

                            stopAutoReturnTimer();


                            window.location.href =
                                "{{ route('attendance.index') }}";

                        }

                    },
                    1000
                );

        }



        /* =====================================================
           RESET TIMER
        ===================================================== */

        function resetAutoReturnTimer() {

            startAutoReturnTimer();

        }



        /* =====================================================
           USER INTERACTION
        ===================================================== */

        [

            'click',

            'pointerdown',

            'touchstart',

            'keydown',

            'input',

            'change'

        ].forEach(
            function (eventName) {

                document.addEventListener(
                    eventName,
                    function () {

                        resetAutoReturnTimer();

                    },
                    {
                        passive: true
                    }
                );

            }
        );



        /* =====================================================
           START WHEN PAGE LOADS
        ===================================================== */

        document.addEventListener(
            'DOMContentLoaded',
            function () {

                startAutoReturnTimer();

            }
        );

    </script>


</body>
</html>
