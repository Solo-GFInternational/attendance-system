{{--
    Shared admin header.
    Include on every admin page with: @include('partials.admin-nav')
    The active link is worked out from the current route, so pages
    do not need to pass anything in. Styles live in resources/css/admin.css.
--}}

@php
    // Line icons (24x24, stroke). Keyed by name, used below.
    $gfIcons = [
        'dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/>',
        'history'   => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>',
        'outings'   => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'reports'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M8 13h8M8 17h5"/>',
        'employees' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
        'sites'     => '<rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01M8 14h.01M12 14h.01M16 14h.01"/>',
        'visitors'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><polyline points="16 11 18 13 22 9"/>',
        'emergency' => '<path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3"/><path d="M12 9v4"/><path d="M12 17h.01"/>',
        'users'     => '<path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/>',
        'add-user'  => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/>',
        'profile'   => '<circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 0 0-16 0"/>',
        'logout'    => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" x2="9" y1="12" y2="12"/>',
    ];

    $gfIcon = fn (string $name) => '<svg class="gf-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $gfIcons[$name] . '</svg>';

    $gfNavLinks = [
        ['route' => 'dashboard',            'active' => ['dashboard', 'attendance.admin'], 'icon' => 'dashboard', 'label' => 'Dashboard'],
        ['route' => 'attendance.history',   'active' => ['attendance.history*'],           'icon' => 'history',   'label' => 'History'],
        ['route' => 'attendance.outings',   'active' => ['attendance.outings'],            'icon' => 'outings',   'label' => 'Outings'],
        ['route' => 'reports.employee',     'active' => ['reports.*'],                     'icon' => 'reports',   'label' => 'Reports'],
        ['route' => 'employees.index',      'active' => ['employees.*'],                   'icon' => 'employees', 'label' => 'Employees'],
        ['route' => 'sites.index',          'active' => ['sites.*'],                       'icon' => 'sites',     'label' => 'Sites'],
        ['route' => 'visitors.index',       'active' => ['visitors.*'],                    'icon' => 'visitors',  'label' => 'Visitors'],
        ['route' => 'attendance.emergency', 'active' => ['attendance.emergency'],          'icon' => 'emergency', 'label' => 'Emergency'],
    ];

    $gfUser = auth()->user();

    if ($gfUser?->is_admin) {
        $gfNavLinks[] = ['route' => 'admin-users.index', 'active' => ['admin-users.index', 'admin-users.edit'], 'icon' => 'users', 'label' => 'Users'];
    }

    $gfInitials = $gfUser
        ? collect(preg_split('/\s+/', trim($gfUser->name)))->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('')
        : '';
@endphp

<nav class="gf-nav" aria-label="Admin navigation">

    <a href="{{ route('dashboard') }}" class="gf-nav-brand">
        <img src="{{ asset('images/goodman-fielder-mark.png') }}" class="gf-nav-mark" alt="" width="36" height="36">
        <span class="gf-nav-brand-text">
            <strong>Goodman Fielder</strong>
            <span>Attendance System</span>
        </span>
    </a>

    <div class="gf-nav-links">
        @foreach ($gfNavLinks as $link)
            @php $isActive = request()->routeIs(...$link['active']); @endphp
            <a href="{{ route($link['route']) }}"
               class="gf-nav-link {{ $isActive ? 'active' : '' }}"
               @if ($isActive) aria-current="page" @endif>
                {!! $gfIcon($link['icon']) !!}
                <span>{{ $link['label'] }}</span>
            </a>
        @endforeach
    </div>

    @if ($gfUser)
        <div class="gf-nav-actions">

            @if ($gfUser->is_admin)
                <a href="{{ route('admin-users.create') }}"
                   class="gf-nav-link gf-nav-link-primary {{ request()->routeIs('admin-users.create') ? 'active' : '' }}"
                   title="Create a new User or Admin account">
                    {!! $gfIcon('add-user') !!}
                    <span>Add User</span>
                </a>
            @endif

            <span class="gf-nav-divider" aria-hidden="true"></span>

            <a href="{{ route('profile.edit') }}"
               class="gf-nav-user {{ request()->routeIs('profile.*') ? 'active' : '' }}"
               title="Your profile">
                <span class="gf-nav-avatar" aria-hidden="true">{{ $gfInitials }}</span>
                <span class="gf-nav-user-text">
                    <strong>{{ $gfUser->name }}</strong>
                    <span>{{ $gfUser->is_admin ? 'Admin' : 'User' }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}" class="gf-nav-logout-form">
                @csrf
                <button type="submit" class="gf-nav-link gf-nav-link-danger" title="Log out">
                    {!! $gfIcon('logout') !!}
                    <span>Logout</span>
                </button>
            </form>

        </div>
    @endif

</nav>
