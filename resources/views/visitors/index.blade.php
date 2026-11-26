@php
    $kioskSite = session('attendance_site_id')
        ? \App\Models\Site::find(session('attendance_site_id'))
        : null;

    // Visitors still on site (includes forgotten visitors from previous days).
    $currentVisitors = $visitors->whereNull('sign_out');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Visitor Register | Goodman Fielder</title>

    @include('partials.kiosk-theme')

    <style>

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 5fr) minmax(0, 6fr);
            gap: 24px;
            align-items: start;
        }

        .count-pill {
            margin-left: auto;
            padding: 4px 12px;
            border-radius: 999px;
            background: var(--green-soft);
            color: var(--green);
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .form-note {
            margin: 14px 0 0;
            color: var(--muted);
            font-size: 13px;
            text-align: center;
        }


        /* Auto return */
        .auto-return-box {
            display: none;
        }

        .auto-return-box strong {
            font-variant-numeric: tabular-nums;
        }


        /* Visitor list */
        .visitor-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .visitor {
            display: flex;
            gap: 14px;
            padding: 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: var(--subtle);
        }

        .visitor-old {
            border-color: var(--amber-border);
            background: var(--amber-soft);
        }

        .visitor-avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: var(--green-soft);
            color: var(--green);
            font-weight: 700;
            font-size: 15px;
        }

        .visitor-old .visitor-avatar {
            background: var(--amber-soft);
            color: var(--amber);
        }

        .visitor-body {
            flex: 1;
            min-width: 0;
        }

        .visitor-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .visitor-name {
            font-size: 17px;
            font-weight: 700;
        }

        .visitor-company {
            margin-top: 2px;
            color: var(--muted);
            font-size: 14px;
        }

        .visitor-purpose {
            margin-top: 8px;
            font-size: 14px;
            color: var(--text-2);
            line-height: 1.45;
        }

        .visitor-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px 14px;
            margin-top: 10px;
            color: var(--muted);
            font-size: 13px;
        }

        .visitor-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .visitor-meta svg {
            width: 14px;
            height: 14px;
        }

        .old-visitor-warning {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            background: var(--amber-soft);
            border: 1px solid var(--amber-border);
            color: var(--amber-text);
            font-size: 13px;
            line-height: 1.5;
        }

        .visitor form {
            margin: 0;
            flex-shrink: 0;
        }

        .no-visitors {
            text-align: center;
            padding: 40px 16px;
            color: var(--muted);
        }

        .no-visitors-icon {
            display: grid;
            place-items: center;
            width: 56px;
            height: 56px;
            margin: 0 auto 12px;
            border-radius: 16px;
            background: var(--hover);
            color: var(--faint);
        }

        .no-visitors-icon svg {
            width: 28px;
            height: 28px;
        }

        .no-visitors strong {
            display: block;
            color: var(--text);
            font-size: 16px;
            margin-bottom: 4px;
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 520px) {
            .visitor-top {
                flex-direction: column;
            }

            .visitor form,
            .visitor form .k-btn {
                width: 100%;
            }
        }

    </style>

</head>

<body>

    @include('partials.kiosk-header', [
        'variant'  => 'page',
        'title'    => 'Visitor Register',
        'subtitle' => 'Please sign in when you arrive and sign out when you leave.',
        'icon'     => 'badge',
        'site'     => $kioskSite,
    ])


    <main class="k-main">

        {{-- Auto return countdown --}}
        <div id="autoReturnBox" class="k-alert info auto-return-box">
            @include('partials.kiosk-icon', ['name' => 'clock'])
            <div>Returning to the main menu in <strong id="autoReturnCountdown">15</strong> seconds…</div>
        </div>

        @if (session('success'))
            <div class="k-alert success">
                @include('partials.kiosk-icon', ['name' => 'check'])
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if (session('error'))
            <div class="k-alert error">
                @include('partials.kiosk-icon', ['name' => 'x'])
                <div>{{ session('error') }}</div>
            </div>
        @endif

        @if ($errors->any())
            <div class="k-alert error">
                @include('partials.kiosk-icon', ['name' => 'x'])
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif


        <div class="layout">

            {{-- ================= SIGN IN ================= --}}
            <section class="k-card">

                <div class="k-card-head">
                    <div class="k-card-icon green">@include('partials.kiosk-icon', ['name' => 'pen'])</div>
                    <div>
                        <h2>Visitor Sign In</h2>
                        <p>Fill in your details below.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('visitors.sign-in') }}">
                    @csrf

                    <div class="k-field">
                        <label for="name">Full name <span class="req">*</span></label>
                        <input type="text" id="name" name="name" class="k-input"
                               value="{{ old('name') }}" placeholder="Enter your full name"
                               required autocomplete="name">
                    </div>

                    <div class="k-field">
                        <label for="company">Company</label>
                        <input type="text" id="company" name="company" class="k-input"
                               value="{{ old('company') }}" placeholder="Enter your company name"
                               autocomplete="organization">
                    </div>

                    <div class="k-field">
                        <label for="purpose">Purpose of visit <span class="req">*</span></label>
                        <textarea id="purpose" name="purpose" class="k-input"
                                  placeholder="Example: Meeting with the Accounts Department"
                                  required>{{ old('purpose') }}</textarea>
                    </div>

                    <button type="submit" class="k-btn green block">
                        @include('partials.kiosk-icon', ['name' => 'login'])
                        Sign In
                    </button>

                    <p class="form-note">Your arrival time is recorded automatically.</p>
                </form>

            </section>


            {{-- ================= ON SITE ================= --}}
            <section class="k-card">

                <div class="k-card-head">
                    <div class="k-card-icon">@include('partials.kiosk-icon', ['name' => 'users'])</div>
                    <div>
                        <h2>Visitors On Site</h2>
                        <p>Tap Sign Out when you leave.</p>
                    </div>
                    <span class="count-pill">{{ $currentVisitors->count() }} on site</span>
                </div>

                <div class="visitor-list">

                    @forelse ($currentVisitors as $visitor)

                        @php
                            $isOldVisitor = $visitor->visit_date
                                && $visitor->visit_date->lt(\Carbon\Carbon::today());

                            $initials = collect(preg_split('/\s+/', trim($visitor->name)))
                                ->filter()->take(2)
                                ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
                                ->implode('');
                        @endphp

                        <div class="visitor {{ $isOldVisitor ? 'visitor-old' : '' }}">

                            <div class="visitor-avatar" aria-hidden="true">{{ $initials }}</div>

                            <div class="visitor-body">

                                <div class="visitor-top">

                                    <div>
                                        <div class="visitor-name">{{ $visitor->name }}</div>
                                        @if ($visitor->company)
                                            <div class="visitor-company">{{ $visitor->company }}</div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('visitors.sign-out', $visitor) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="k-btn red sm"
                                                onclick="return confirm({{ \Illuminate\Support\Js::from('Are you sure you want to sign out ' . $visitor->name . '?') }})">
                                            @include('partials.kiosk-icon', ['name' => 'logout'])
                                            Sign Out
                                        </button>
                                    </form>

                                </div>

                                <div class="visitor-purpose">{{ $visitor->purpose }}</div>

                                <div class="visitor-meta">
                                    <span>@include('partials.kiosk-icon', ['name' => 'clock']) Signed in {{ $visitor->sign_in->format('h:i A') }}</span>
                                    <span>{{ $visitor->sign_in->format('j F Y') }}</span>
                                </div>

                                @if ($isOldVisitor)
                                    <div class="old-visitor-warning">
                                        <strong>Previous day visitor.</strong>
                                        Signed in on {{ $visitor->sign_in->format('j F Y') }} and not signed out.
                                        Please confirm they have left the site before signing them out.
                                    </div>
                                @endif

                            </div>

                        </div>

                    @empty

                        <div class="no-visitors">
                            <div class="no-visitors-icon">@include('partials.kiosk-icon', ['name' => 'user'])</div>
                            <strong>No visitors on site</strong>
                            Visitors who sign in will appear here.
                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </main>


    <footer class="k-footer">
        @include('partials.kiosk-icon', ['name' => 'info'])
        Visitor arrivals and departures are recorded with the date and time.
    </footer>


    <script>
        /* =====================================================
           AUTO RETURN TO MAIN MENU AFTER 15 SECONDS OF INACTIVITY
        ===================================================== */

        (function () {
            const box = document.getElementById('autoReturnBox');
            const countdown = document.getElementById('autoReturnCountdown');
            const AUTO_RETURN_SECONDS = 15;

            let timer = null;
            let seconds = AUTO_RETURN_SECONDS;

            function stop() {
                if (timer) {
                    clearInterval(timer);
                    timer = null;
                }
            }

            function start() {
                stop();
                seconds = AUTO_RETURN_SECONDS;
                countdown.textContent = seconds;
                box.style.display = 'flex';

                timer = setInterval(function () {
                    seconds--;
                    countdown.textContent = seconds;

                    if (seconds <= 0) {
                        stop();
                        window.location.href = "{{ route('attendance.index') }}";
                    }
                }, 1000);
            }

            ['click', 'pointerdown', 'touchstart', 'keydown', 'input', 'change'].forEach(function (eventName) {
                document.addEventListener(eventName, start, { passive: true });
            });

            document.addEventListener('DOMContentLoaded', start);
        })();
    </script>

</body>
</html>
