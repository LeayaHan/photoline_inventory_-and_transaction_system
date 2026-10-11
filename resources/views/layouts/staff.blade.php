<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Photoline') }} | Staff</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/staff-layout.css') }}">
</head>
<body class="staffui-body">
    @php($staffUser = auth('staff')->user())

    <div class="staffui-shell">
        <aside class="staffui-sidebar" id="staffuiSidebar">
            <a href="{{ route('staff.dashboard') }}" class="staffui-brand">
                <span class="staffui-brand-name">PHOTO<span>LINE</span></span>
                <span class="staffui-brand-tag">STAFF PORTAL</span>
            </a>

            <div class="staffui-menu-label">WORKSPACE</div>

            <nav class="staffui-menu">
                <a href="{{ route('staff.dashboard') }}"
                   class="staffui-link {{ request()->routeIs('staff.dashboard') ? 'is-active' : '' }}">
                    <span class="staffui-icon">▦</span>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('staff.transactions') }}"
                   class="staffui-link {{ request()->routeIs('staff.transactions') ? 'is-active' : '' }}">
                    <span class="staffui-icon">⇄</span>
                    <span>Transactions</span>
                </a>

                <a href="{{ route('staff.inventory') }}"
                   class="staffui-link {{ request()->routeIs('staff.inventory*') ? 'is-active' : '' }}">
                    <span class="staffui-icon">▣</span>
                    <span>Inventory</span>
                </a>

                <a href="{{ route('staff.audits') }}"
                   class="staffui-link {{ request()->routeIs('staff.audits*') ? 'is-active' : '' }}">
                    <span class="staffui-icon">☷</span>
                    <span>Audits</span>
                </a>
            </nav>

            <div class="staffui-sidebar-bottom">
                <div class="staffui-account">
                    <div class="staffui-avatar">{{ strtoupper(substr($staffUser->first_name ?? 'S', 0, 1)) }}</div>
                    <div class="staffui-account-info">
                        <span class="staffui-account-name">{{ $staffUser->full_name ?? 'Staff' }}</span>
                        <span class="staffui-account-role">{{ $staffUser->position ?? 'Staff' }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="staffui-logout">
                        <span class="staffui-icon">↪</span>
                        <span>Log out</span>
                    </button>
                </form>
            </div>
        </aside>

        <div class="staffui-main">
            <header class="staffui-topbar">
                <button type="button" class="staffui-toggle" id="staffuiToggle"
                        aria-label="Toggle navigation" aria-controls="staffuiSidebar" aria-expanded="false">☰</button>

                <div class="staffui-crumbs">
                    <span class="staffui-crumb-brand">PHOTO<span>LINE</span></span>
                    <span class="staffui-crumb-sep">/</span>
                    <span>{{ $title ?? 'Staff' }}</span>
                </div>

                <div class="staffui-topbar-user">
                    <span class="staffui-staffid">{{ $staffUser->staff_id ?? '' }}</span>
                </div>
            </header>

            <main class="staffui-content">
                {{ $slot }}
            </main>

            <footer class="staffui-footer">
                <span>Photoline Inventory &amp; Transaction System</span>
                <span>Staff Portal</span>
            </footer>
        </div>
    </div>

    <script>
        (function () {
            var toggle = document.getElementById('staffuiToggle');
            var sidebar = document.getElementById('staffuiSidebar');
            toggle.addEventListener('click', function () {
                var open = sidebar.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', String(open));
            });
        })();
    </script>
</body>
</html>
