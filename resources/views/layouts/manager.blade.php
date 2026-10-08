<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')

    <style>
        :root {
            --nav-bg: #111827;
            --page-bg: #f5f6f8;
        }

        @include('layouts.partials.nav-styles')
    </style>

    @stack('styles')
</head>
<body>
    <x-main-nav
        :links="config('navigation.manager')"
        role-label="Branch Manager"
    />

    {{ $slot }}

    @stack('scripts')
</body>
</html>
