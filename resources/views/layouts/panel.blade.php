<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - Photoline Abreeza</title>

    @include('layouts.partials.styles')

    {{-- Page-specific styles --}}
    @stack('styles')
</head>
<body>

    @include('layouts.partials.topbar')

    <div class="pl-main">
        @yield('content')
    </div>

    @stack('scripts')
</body>
</html>
