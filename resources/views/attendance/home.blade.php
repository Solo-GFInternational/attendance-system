@php
    // Site chosen via a kiosk link such as /attendance/DG (stored in the session).
    $kioskSite = session('attendance_site_id')
        ? \App\Models\Site::find(session('attendance_site_id'))
        : null;
@endphp
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Goodman Fielder Attendance System</title>

    @include('partials.kiosk-theme')

    <style>

        /* =====================================================
           HIDDEN ADMIN LOGIN AREA
           Invisible clickable area at the top-right.
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
        }


        /* =====================================================
           OPTION CARDS
        ===================================================== */

        .options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .option {
            --accent: var(--blue);
            --accent-soft: var(--blue-soft);

            position: relative;
            display: flex;
            flex-direction: column;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 36px 36px 30px;
            color: var(--text);
            text-decoration: none;
            overflow: hidden;
            transition: box-shadow .2s ease, border-color .2s ease, transform .2s ease;
        }

        .option::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: var(--accent);
        }

        .option.visitor {
            --accent: var(--green);
            --accent-soft: var(--green-soft);
        }

        .option:hover {
            box-shadow: var(--shadow-hover);
            border-color: var(--border-strong);
            transform: translateY(-2px);
        }

        .option:active {
            transform: translateY(0);
        }

        .option:focus-visible,
        .emergency:focus-visible {
            outline: 3px solid var(--accent, var(--red));
            outline-offset: 3px;
        }

        .option-icon {
            display: grid;
            place-items: center;
            width: 64px;
            height: 64px;
            border-radius: 16px;
            background: var(--accent-soft);
            color: var(--accent);
            margin-bottom: 24px;
        }

        .option-icon svg {
            width: 32px;
            height: 32px;
        }

        .option h2 {
            margin: 0 0 8px;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -.01em;
        }

        .option p {
            margin: 0;
            font-size: 16px;
            line-height: 1.55;
            color: var(--muted);
        }

        .option-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 20px 0 28px;
            padding: 0;
            list-style: none;
        }

        .option-tags li {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-2);
            background: var(--hover);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 5px 12px;
        }

        .option-cta {
            margin-top: auto;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            height: 54px;
            border-radius: 12px;
            background: var(--accent);
            color: #ffffff;
            font-size: 17px;
            font-weight: 700;
            transition: filter .2s ease;
        }

        .option:hover .option-cta {
            filter: brightness(.92);
        }

        .option-cta svg {
            width: 20px;
            height: 20px;
            transition: transform .2s ease;
        }

        .option:hover .option-cta svg {
            transform: translateX(3px);
        }


        /* =====================================================
           EMERGENCY
        ===================================================== */

        .emergency {
            --accent: var(--red);

            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 24px;
            padding: 22px 26px;
            background: var(--surface);
            border: 1px solid var(--red-border);
            border-left: 5px solid var(--red);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            color: var(--text);
            text-decoration: none;
            transition: box-shadow .2s ease, background .2s ease;
        }

        .emergency:hover {
            background: var(--red-soft);
            box-shadow: var(--shadow-hover);
        }

        .emergency-icon {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--red-soft);
            color: var(--red);
        }

        .emergency-icon svg {
            width: 28px;
            height: 28px;
        }

        .emergency-text {
            flex: 1;
            min-width: 0;
        }

        .emergency-text strong {
            display: block;
            font-size: 19px;
            font-weight: 700;
        }

        .emergency-text span {
            display: block;
            margin-top: 3px;
            font-size: 15px;
            color: var(--muted);
        }

        .emergency-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
            height: 48px;
            padding: 0 22px;
            border-radius: 12px;
            background: var(--red);
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            transition: background .2s ease;
        }

        .emergency:hover .emergency-cta {
            background: var(--red-dark);
        }

        .emergency-cta svg {
            width: 18px;
            height: 18px;
        }


        @media (max-width: 860px) {

            .options {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .option {
                padding: 28px 24px 24px;
            }

            .option-icon {
                width: 56px;
                height: 56px;
                margin-bottom: 18px;
            }

            .option h2 {
                font-size: 23px;
            }

            .emergency {
                flex-wrap: wrap;
                padding: 20px;
            }

            .emergency-cta {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 520px) {
            .hidden-admin-area {
                width: 110px;
                height: 50px;
            }
        }

    </style>

</head>

<body>

    {{-- Hidden admin login: invisible tap area at the top-right --}}
    @guest
        <a href="{{ route('login') }}" class="hidden-admin-area" aria-label="Administrator Login" title=""></a>
    @else
        <a href="{{ route('attendance.admin') }}" class="hidden-admin-area" aria-label="Administrator Dashboard" title=""></a>
    @endguest


    @include('partials.kiosk-header', [
        'variant'  => 'welcome',
        'title'    => 'Welcome',
        'subtitle' => 'Please choose an option below to continue.',
        'site'     => $kioskSite,
        'back'     => null,
    ])


    <main class="k-main">

        @if (session('error') || (! $kioskSite && auth()->guest()))
            <div class="k-alert warning" role="alert">
                @include('partials.kiosk-icon', ['name' => 'info'])
                <div>{{ session('error') ?: 'This tablet is not set to a site. Please open your site link.' }}</div>
            </div>
        @endif

        <div class="options">

            {{-- Employee attendance --}}
            <a href="{{ route('attendance.employee') }}" class="option employee">

                <div class="option-icon">@include('partials.kiosk-icon', ['name' => 'users', 'stroke' => 1.8])</div>

                <h2>Employee Attendance</h2>
                <p>For Goodman Fielder staff. Record your attendance using face recognition.</p>

                <ul class="option-tags" aria-label="Available actions">
                    <li>Sign in</li>
                    <li>Sign out</li>
                    <li>Going out</li>
                    <li>Returning</li>
                </ul>

                <span class="option-cta">
                    I'm an employee
                    @include('partials.kiosk-icon', ['name' => 'arrow', 'stroke' => 2.2])
                </span>

            </a>

            {{-- Visitor register --}}
            <a href="{{ route('visitors.index') }}" class="option visitor">

                <div class="option-icon">@include('partials.kiosk-icon', ['name' => 'badge', 'stroke' => 1.8])</div>

                <h2>Visitor Register</h2>
                <p>For guests, contractors and delivery drivers visiting the site.</p>

                <ul class="option-tags" aria-label="Available actions">
                    <li>Register arrival</li>
                    <li>Sign out when leaving</li>
                </ul>

                <span class="option-cta">
                    I'm a visitor
                    @include('partials.kiosk-icon', ['name' => 'arrow', 'stroke' => 2.2])
                </span>

            </a>

        </div>


        {{-- Emergency roll call --}}
        <a href="{{ route('attendance.emergency') }}" class="emergency">

            <div class="emergency-icon">@include('partials.kiosk-icon', ['name' => 'alert'])</div>

            <div class="emergency-text">
                <strong>Emergency Roll Call</strong>
                <span>During an evacuation, see every employee and visitor currently on site.</span>
            </div>

            <span class="emergency-cta">
                Open roll call
                @include('partials.kiosk-icon', ['name' => 'arrow', 'stroke' => 2.2])
            </span>

        </a>

    </main>


    <footer class="k-footer">
        @include('partials.kiosk-icon', ['name' => 'info'])
        Please remember to sign out when you leave the premises.
    </footer>

</body>

</html>
