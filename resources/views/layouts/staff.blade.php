<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')

    <style>
        :root {
            --staff-blue: #1769e8;
            --staff-blue-dark: #0b47b7;
            --staff-red: #ed2b24;
            --staff-page: #f5f8fc;
            --staff-ink: #172033;
            --staff-muted: #68738a;
        }

        @include('layouts.partials.nav-styles')
    </style>

    @stack('styles')
    @include('layouts.partials.action-styles')
</head>

<body class="staff-theme">
    <x-main-nav
        :links="config('navigation.staff')"
        role-label="Staff"
        theme="staff"
    />

    <main class="staff-page-content">
        {{ $slot }}
    </main>

    @stack('scripts')
</body>
</html>
