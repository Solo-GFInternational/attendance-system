<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Goodman Fielder Attendance System</title>


    <style>

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            min-height: 100vh;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f1f5f9;
            color: #0f172a;

            display: flex;
            flex-direction: column;
        }


        /* =====================================================
           HEADER
        ===================================================== */

        .header {
            background: #0f172a;
            color: white;

            padding: 35px 20px 30px;

            position: relative;
        }


        .header-inner {
            width: 100%;
            max-width: 1200px;

            margin: 0 auto;

            text-align: center;
        }


        /* =====================================================
           HIDDEN ADMIN LOGIN AREA
           
           Invisible clickable area at the top-right.
           No button or text is displayed.
        ===================================================== */

        .hidden-admin-area {
            position: fixed;

            top: 0;
            right: 0;

            width: 150px;
            height: 55px;

            z-index: 9999;

            background: transparent;

            cursor: pointer;

            /*
             * Invisible to the user.
             * It still receives clicks/taps.
             */
        }


        /* =====================================================
           HEADER TITLE
        ===================================================== */

        .header h1 {
            margin: 0;

            font-size: 40px;

            font-weight: 800;

            letter-spacing: -0.5px;
        }


        .header p {
            margin: 10px 0 0;

            color: #cbd5e1;

            font-size: 18px;
        }


        /* =====================================================
           CLOCK
        ===================================================== */

        .clock {
            margin-top: 14px;

            font-size: 18px;

            font-weight: 600;

            color: #e2e8f0;
        }


        /* =====================================================
           MAIN CONTAINER
        ===================================================== */

        .container {
            width: 100%;

            max-width: 1200px;

            margin: auto;

            padding: 45px 25px 35px;
        }


        /* =====================================================
           WELCOME
        ===================================================== */

        .instruction {
            text-align: center;

            margin-bottom: 32px;
        }


        .instruction h2 {
            margin: 0 0 8px;

            font-size: 28px;

            font-weight: 800;
        }


        .instruction p {
            margin: 0;

            color: #64748b;

            font-size: 16px;
        }


        /* =====================================================
           OPTIONS
        ===================================================== */

        .options {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 30px;
        }


        /* =====================================================
           OPTION CARD
        ===================================================== */

        .option {
            background: white;

            border-radius: 18px;

            padding: 45px 35px;

            text-align: center;

            text-decoration: none;

            color: #0f172a;

            box-shadow:
                0 5px 20px rgba(0, 0, 0, 0.08);

            border: 3px solid transparent;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }


        .option:hover {
            transform: translateY(-4px);

            box-shadow:
                0 10px 28px rgba(0, 0, 0, 0.12);
        }


        /* =====================================================
           EMPLOYEE
        ===================================================== */

        .employee {
            border-color: #2563eb;
        }


        /* =====================================================
           VISITOR
        ===================================================== */

        .visitor {
            border-color: #16a34a;
        }


        /* =====================================================
           ICON
        ===================================================== */

        .icon {
            font-size: 70px;

            margin-bottom: 20px;
        }


        /* =====================================================
           CARD TITLE
        ===================================================== */

        .option h3 {
            margin: 0 0 10px;

            font-size: 30px;

            font-weight: 800;
        }


        /* =====================================================
           CARD DESCRIPTION
        ===================================================== */

        .option p {
            margin: 0 0 25px;

            color: #64748b;

            font-size: 17px;

            line-height: 1.5;
        }


        /* =====================================================
           CARD BUTTON
        ===================================================== */

        .button {
            display: inline-block;

            padding: 15px 35px;

            border-radius: 9px;

            color: white;

            font-size: 18px;

            font-weight: 800;
        }


        .employee .button {
            background: #2563eb;
        }


        .visitor .button {
            background: #16a34a;
        }


        /* =====================================================
           EMERGENCY
        ===================================================== */

        .emergency {
            text-align: center;

            margin-top: 30px;
        }


        .emergency a {
            display: inline-block;

            background: #b91c1c;

            color: white;

            text-decoration: none;

            padding: 15px 30px;

            border-radius: 9px;

            font-size: 17px;

            font-weight: 800;

            box-shadow:
                0 4px 12px rgba(185, 28, 28, 0.18);

            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }


        .emergency a:hover {
            background: #991b1b;

            transform: translateY(-2px);
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .footer {
            text-align: center;

            padding: 20px;

            color: #64748b;

            font-size: 13px;

            margin-top: auto;
        }


        /* =====================================================
           MOBILE / TABLET
        ===================================================== */

        @media (max-width: 750px) {

            .hidden-admin-area {
                width: 120px;

                height: 50px;
            }


            .header {
                padding:
                    35px
                    15px
                    25px;
            }


            .header h1 {
                font-size: 30px;

                line-height: 1.2;
            }


            .header p {
                font-size: 16px;
            }


            .clock {
                font-size: 15px;
            }


            .container {
                padding:
                    30px
                    15px
                    25px;
            }


            .instruction {
                margin-bottom: 25px;
            }


            .instruction h2 {
                font-size: 25px;
            }


            .options {
                grid-template-columns: 1fr;

                gap: 20px;
            }


            .option {
                padding:
                    35px
                    25px;
            }


            .icon {
                font-size: 55px;
            }


            .option h3 {
                font-size: 26px;
            }


            .option p {
                font-size: 16px;
            }


            .emergency {
                margin-top: 20px;
            }


            .emergency a {
                width: 100%;

                padding:
                    15px
                    20px;

                font-size: 16px;
            }

        }


        /* =====================================================
           SMALL MOBILE
        ===================================================== */

        @media (max-width: 450px) {

            .hidden-admin-area {
                width: 100px;

                height: 45px;
            }


            .header {
                padding-top: 30px;
            }


            .header h1 {
                font-size: 26px;
            }


            .header p {
                font-size: 14px;
            }


            .clock {
                font-size: 13px;
            }

        }

    </style>

</head>


<body>


    <!-- =====================================================
         HIDDEN ADMIN LOGIN AREA
         
         Invisible area at the TOP-RIGHT.
         
         Clicking/tapping here opens the Laravel login page.
    ====================================================== -->

    @guest

        <a
            href="{{ route('login') }}"
            class="hidden-admin-area"
            aria-label="Administrator Login"
            title=""
        ></a>

    @else

        <a
            href="{{ route('attendance.admin') }}"
            class="hidden-admin-area"
            aria-label="Administrator Dashboard"
            title=""
        ></a>

    @endguest



    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="header">

        <div class="header-inner">


            <h1>
                Goodman Fielder Attendance System
            </h1>


            <p>
                Employee &amp; Visitor Management
            </p>


            <div class="clock">

                📅
                {{ now()->format('l, d F Y') }}

                &nbsp; | &nbsp;

                🕐

                <span id="currentTime">
                    {{ now()->format('h:i:s A') }}
                </span>

            </div>


        </div>

    </div>



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <div class="container">


        <!-- =================================================
             WELCOME
        ================================================== -->

        <div class="instruction">

            <h2>
                Welcome
            </h2>


            <p>
                Please select an option below to continue.
            </p>

        </div>



        <!-- =================================================
             OPTIONS
        ================================================== -->

        <div class="options">


            <!-- =================================================
                 EMPLOYEE ATTENDANCE
            ================================================== -->

            <a
                href="{{ route('attendance.employee') }}"
                class="option employee"
            >

                <div class="icon">
                    👥
                </div>


                <h3>
                    Employee Attendance
                </h3>


                <p>
                    Sign in, sign out, going out or returning.
                </p>


                <span class="button">
                    EMPLOYEE
                </span>

            </a>



            <!-- =================================================
                 VISITOR REGISTER
            ================================================== -->

            <a
                href="{{ route('visitors.index') }}"
                class="option visitor"
            >

                <div class="icon">
                    👋
                </div>


                <h3>
                    Visitor Register
                </h3>


                <p>
                    Register your visit and sign out when leaving.
                </p>


                <span class="button">
                    VISITOR
                </span>

            </a>


        </div>



        <!-- =================================================
             EMERGENCY ROLL CALL
        ================================================== -->

        <div class="emergency">

            <a
                href="{{ route('attendance.emergency') }}"
            >

                🚨 EMERGENCY ROLL CALL

            </a>

        </div>


    </div>



    <!-- =====================================================
         FOOTER
    ====================================================== -->

    <div class="footer">

        Please ensure you sign out when leaving the premises.

    </div>



    <!-- =====================================================
         LIVE CLOCK
    ====================================================== -->

    <script>

        function updateClock() {

            const now = new Date();


            let hours = now.getHours();

            let minutes = now.getMinutes();

            let seconds = now.getSeconds();


            const ampm =
                hours >= 12
                    ? 'PM'
                    : 'AM';


            hours = hours % 12;


            if (hours === 0) {

                hours = 12;

            }


            hours =
                String(hours)
                    .padStart(2, '0');


            minutes =
                String(minutes)
                    .padStart(2, '0');


            seconds =
                String(seconds)
                    .padStart(2, '0');


            document
                .getElementById('currentTime')
                .textContent =
                `${hours}:${minutes}:${seconds} ${ampm}`;

        }


        updateClock();


        setInterval(
            updateClock,
            1000
        );

    </script>


</body>

</html>