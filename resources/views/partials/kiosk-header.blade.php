{{--
    Shared kiosk header (dark band with logo, name, live clock and page title).

    @include('partials.kiosk-header', [
        'variant'  => 'page',            // 'welcome' | 'page' | 'emergency'
        'title'    => 'Visitor Register',
        'subtitle' => 'Please sign in when you arrive',
        'icon'     => 'badge',            // name from partials.kiosk-icon
        'back'     => route('attendance.index'),   // null hides the Home button
        'site'     => $site,              // optional Site model, shown as a chip
        'clockScript' => true,            // false if the page updates #liveClock itself
    ])

    Extra buttons/chips on the right of the title can be added with
    @push('kiosk-hero-actions') ... @endpush before the include.
--}}
@php
    $variant = $variant ?? 'page';
    $back = array_key_exists('back', get_defined_vars()) ? $back : route('attendance.index');
    $clockScript = $clockScript ?? true;
@endphp

<header class="k-hero is-{{ $variant }}">

    <div class="k-topbar">

        <a href="{{ route('attendance.index') }}" class="k-brand" aria-label="Goodman Fielder Attendance System - home">
            @include('partials.brand-mark', ['class' => 'k-brand-logo'])
            <span class="k-brand-text">
                <strong>Goodman Fielder</strong>
                <span>Attendance System</span>
            </span>
        </a>

        <div class="k-topbar-end">
            {{-- Left of the clock: the home page has a hidden admin tap area over the top-right corner. --}}
            @include('partials.theme-toggle', ['class' => 'k-theme-toggle'])

            <div class="k-now">
                <div class="k-now-time" id="liveClock">{{ now()->format('h:i A') }}</div>
                <div class="k-now-date" id="liveDate">{{ now()->format('l j F Y') }}</div>
            </div>
        </div>

    </div>

    <div class="k-hero-body">

        @if ($variant === 'welcome')

            @php $festive = \App\Support\FestiveTheme::current(); @endphp
            @if ($festive['isFestive'])
                <div class="k-chip k-festive"><span aria-hidden="true">{{ $festive['emoji'] }}</span> {{ $festive['greeting'] }}</div>
            @endif

            @if (! empty($site))
                <div class="k-chip">@include('partials.kiosk-icon', ['name' => 'pin']) {{ $site->name }}</div>
            @endif

            <h1>{{ $title ?? 'Welcome' }}</h1>

            @if (! empty($subtitle))
                <p class="k-hero-sub">{{ $subtitle }}</p>
            @endif

        @else

            <div class="k-title-group">
                @if (! empty($icon))
                    <div class="k-title-icon">@include('partials.kiosk-icon', ['name' => $icon])</div>
                @endif
                <div>
                    <h1>{{ $title }}</h1>
                    @if (! empty($subtitle))
                        <p class="k-hero-sub">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="k-hero-actions">
                @if (! empty($site))
                    <span class="k-chip">@include('partials.kiosk-icon', ['name' => 'pin']) {{ $site->name }}</span>
                @endif

                @stack('kiosk-hero-actions')

                @if ($back)
                    <a href="{{ $back }}" class="k-back">
                        @include('partials.kiosk-icon', ['name' => 'home'])
                        Home
                    </a>
                @endif
            </div>

        @endif

    </div>

</header>

@if ($clockScript)
    <script>
        (function () {
            const timeEl = document.getElementById('liveClock');
            const dateEl = document.getElementById('liveDate');
            const timeFmt = new Intl.DateTimeFormat('en-FJ', { hour: '2-digit', minute: '2-digit', hour12: true });
            const dateFmt = new Intl.DateTimeFormat('en-FJ', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

            function tick() {
                const now = new Date();
                timeEl.textContent = timeFmt.format(now).toUpperCase();
                dateEl.textContent = dateFmt.format(now).replace(/,/g, '');
            }

            tick();
            setInterval(tick, 1000);
        })();
    </script>
@endif
