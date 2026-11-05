<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<meta name="csrf-token"
      content="{{ csrf_token() }}">

<title>
    @yield('title', 'Goodman Fielder Attendance System')
</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])

@stack('styles')


</head>

<body>

<div class="sidebar sidebar-dark border-end"
     id="sidebar">


<div class="sidebar-header border-bottom">

    <div class="sidebar-brand px-3 py-3">

        <strong>
            GOODMAN FIELDER
        </strong>

        <div class="small text-white-50">
            Attendance System
        </div>

    </div>

</div>


<ul class="sidebar-nav">


    <!-- DASHBOARD -->

    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('dashboard') ? 'active' : '' }}"
           href="{{ route('dashboard') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-speedometer') }}"></use>
            </svg>

            Dashboard

        </a>

    </li>


    <!-- ATTENDANCE -->

    <li class="nav-title">
        Attendance
    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('attendance.admin') ? 'active' : '' }}"
           href="{{ route('attendance.admin') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-calendar') }}"></use>
            </svg>

            Today's Attendance

        </a>

    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('attendance.history') ? 'active' : '' }}"
           href="{{ route('attendance.history') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-list') }}"></use>
            </svg>

            Attendance History

        </a>

    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('attendance.outings') ? 'active' : '' }}"
           href="{{ route('attendance.outings') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-walk') }}"></use>
            </svg>

            Outings

        </a>

    </li>


    <!-- EMPLOYEES -->

    <li class="nav-title">
        Employees
    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('employees.*') ? 'active' : '' }}"
           href="{{ route('employees.index') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-people') }}"></use>
            </svg>

            Employees

        </a>

    </li>


    <!-- VISITORS -->

    <li class="nav-title">
        Visitors
    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('visitors.*') ? 'active' : '' }}"
           href="{{ route('visitors.index') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-door') }}"></use>
            </svg>

            Visitor Register

        </a>

    </li>


    <!-- EMERGENCY -->

    <li class="nav-title">
        Safety
    </li>


    <li class="nav-item">

        <a class="nav-link
            {{ request()->routeIs('attendance.emergency') ? 'active' : '' }}"
           href="{{ route('attendance.emergency') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-warning') }}"></use>
            </svg>

            Emergency Roll Call

        </a>

    </li>


    <!-- ADMIN -->

    <li class="nav-title">
        Administration
    </li>


    <li class="nav-item">

        <a class="nav-link"
           href="{{ route('profile.edit') }}">

            <svg class="nav-icon">
                <use href="{{ asset('icons/coreui.svg#cil-settings') }}"></use>
            </svg>

            Profile

        </a>

    </li>


</ul>
```

</div>

<div class="wrapper d-flex flex-column min-vh-100">

```
<!-- TOP HEADER -->

<header class="header header-sticky p-0 mb-4">

    <div class="container-fluid border-bottom px-4">

        <button
            class="header-toggler"
            type="button"
            onclick="coreui.Sidebar.getOrCreateInstance(
                document.querySelector('#sidebar')
            ).toggle()"
        >

            <svg class="icon icon-lg">
                <use href="{{ asset('icons/coreui.svg#cil-menu') }}"></use>
            </svg>

        </button>


        <div class="ms-auto d-flex align-items-center">

            <span class="me-3 text-body-secondary">

                {{ Auth::user()->name ?? 'Administrator' }}

            </span>


            <a
                href="{{ route('profile.edit') }}"
                class="btn btn-light btn-sm"
            >

                Profile

            </a>

        </div>

    </div>

</header>


<!-- MAIN CONTENT -->

<main class="body flex-grow-1">

    <div class="container-fluid px-4">

        @yield('content')

    </div>

</main>


<!-- FOOTER -->

<footer class="footer px-4">

    <div>

        <span class="text-body-secondary">
            Goodman Fielder Attendance System
        </span>

    </div>

    <div>

        <span class="text-body-secondary">
            Employee Attendance
        </span>

    </div>

</footer>
```

</div>

@stack('scripts')

</body>

</html>
