<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')

    <style>
        :root {
            --nav-bg: #222;
            --page-bg: #f4f4f4;
        }

        @include('layouts.partials.nav-styles')
    </style>

    @stack('styles')
</head>
<body>
    <x-main-nav
        :links="config('navigation.staff')"
        role-label="Staff"
    />

    {{ $slot }}

    @stack('scripts')
</body>
</html>
