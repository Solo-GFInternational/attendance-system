<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        @include('partials.theme-init')
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-ui">
        <div class="container-fluid px-4 pt-4">
            @include('partials.admin-nav')

            @isset($header)
                <header class="page-header mb-4">
                    {{ $header }}
                </header>
            @endisset
        </div>

        <main>
            {{ $slot ?? '' }}
            @yield('content')
        </main>
    </body>
</html>
