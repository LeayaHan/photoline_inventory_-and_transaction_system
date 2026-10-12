{{-- Shared Photoline panel styles (used by staff, manager and inventory pages) --}}
<style>
    *, *::before, *::after { box-sizing: border-box; }

    :root {
        --pl-bg: #f5f6f8;
        --pl-ink: #1f2937;
        --pl-muted: #6b7280;
        --pl-line: #e5e7eb;
        --pl-nav: #111827;
        --pl-primary: #2563eb;
        --pl-green: #059669;
        --pl-orange: #d97706;
        --pl-red: #dc2626;
        --pl-gray: #374151;
    }

    body {
        margin: 0;
        font-family: system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        -webkit-font-smoothing: antialiased;
        line-height: 1.5;
        background: var(--pl-bg);
        color: var(--pl-ink);
    }

    /* ---------- Top bar ---------- */
    /* Side padding grows on wide screens so the bar lines up with the page content. */
    .pl-topbar {
        background: var(--pl-nav);
        color: #fff;
        padding: 0 max(32px, calc((100% - 1200px) / 2));
        display: flex;
        align-items: center;
        gap: 28px;
        min-height: 68px;
    }
    .pl-brand {
        color: #fff;
        font-weight: 700;
        font-size: 18px;
        line-height: 1.2;
        text-decoration: none;
        padding: 14px 0;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .pl-brand small {
        display: block;
        margin-top: 2px;
        font-weight: 400;
        font-size: 12px;
        color: #9ca3af;
    }
    .pl-links {
        display: flex;
        gap: 2px;
        flex: 1;
        min-width: 0;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .pl-links::-webkit-scrollbar { display: none; }
    .pl-links a {
        color: #d1d5db;
        text-decoration: none;
        padding: 8px 11px;
        border-radius: 6px;
        font-size: 14px;
        white-space: nowrap;
    }
    .pl-links a:hover { background: #1f2937; color: #fff; }
    .pl-links a.active { background: var(--pl-primary); color: #fff; }
    .pl-links a:focus-visible,
    .pl-logout:focus-visible { outline: 2px solid #93c5fd; outline-offset: 2px; }

    .pl-user {
        display: flex;
        align-items: center;
        gap: 14px;
        flex-shrink: 0;
        padding-left: 24px;
        border-left: 1px solid #1f2937;
    }
    .pl-user .who { display: flex; flex-direction: column; line-height: 1.25; text-align: right; }
    .pl-user .name { font-size: 14px; font-weight: 600; }
    .pl-user .role { font-size: 12px; color: #9ca3af; }
    .pl-user form { margin: 0; }
    .pl-logout {
        background: transparent;
        color: #e5e7eb;
        border: 1px solid #374151;
        padding: 7px 13px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-family: inherit;
    }
    .pl-logout:hover { background: var(--pl-red); border-color: var(--pl-red); color: #fff; }

    /* ---------- Page ---------- */
    .container {
        width: 92%;
        max-width: 1200px;
        margin: 32px auto;
    }
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 22px;
    }
    .page-header h1 { margin: 0 0 6px; font-size: 26px; }
    .page-header p { margin: 0; color: var(--pl-muted); }

    .card {
        background: #fff;
        border-radius: 10px;
        padding: 24px;
        margin-bottom: 22px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .07);
    }
    .card h2 { margin-top: 0; }
    .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
    .grid.three { grid-template-columns: repeat(3, 1fr); }

    /* ---------- Buttons ---------- */
    .btn, .button {
        display: inline-block;
        padding: 9px 15px;
        background: var(--pl-primary);
        color: #fff;
        text-decoration: none;
        border: 0;
        border-radius: 6px;
        font-size: 14px;
        cursor: pointer;
        font-family: inherit;
    }
    .btn:hover, .button:hover { opacity: .92; }
    .btn.green, .button.green { background: var(--pl-green); }
    .btn.orange, .button.orange { background: var(--pl-orange); }
    .btn.gray, .btn-secondary, .button.secondary { background: var(--pl-gray); }
    .btn.red, .btn-danger { background: var(--pl-red); }
    .btn.small { padding: 5px 10px; font-size: 13px; }

    /* ---------- Forms ---------- */
    .form-group { margin-bottom: 16px; }
    label { display: block; font-weight: bold; margin-bottom: 6px; font-size: 14px; }
    input[type=text], input[type=number], input[type=date], input[type=email],
    input[type=password], input[type=search], select, textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-family: inherit;
        background: #fff;
    }
    input:focus, select:focus, textarea:focus {
        outline: 2px solid #bfdbfe;
        border-color: var(--pl-primary);
    }
    .form-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; }
    .form-actions { display: flex; gap: 10px; align-items: center; margin-top: 20px; flex-wrap: wrap; }
    .hint { color: var(--pl-muted); font-size: 12px; margin-top: 4px; }
    .filter-bar { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 18px; }
    .filter-bar input[type=text], .filter-bar input[type=search] { flex: 1; min-width: 220px; width: auto; }

    /* ---------- Alerts ---------- */
    .success, .error, .errors {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 18px;
        font-size: 14px;
    }
    .success { background: #d1fae5; color: #065f46; }
    .error, .errors { background: #fee2e2; color: #991b1b; }
    .errors ul { margin: 6px 0 0 18px; padding: 0; }

    /* ---------- Tables ---------- */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; background: #fff; }
    th, td { padding: 11px 12px; border-bottom: 1px solid var(--pl-line); text-align: left; font-size: 14px; }
    th { background: #f3f4f6; font-size: 13px; text-transform: uppercase; letter-spacing: .3px; color: var(--pl-gray); }
    .actions { white-space: nowrap; }
    .actions form { display: inline; }
    .actions a, .actions button {
        background: none;
        border: 0;
        color: var(--pl-primary);
        cursor: pointer;
        font: inherit;
        font-size: 14px;
        padding: 0;
        margin-right: 10px;
        text-decoration: none;
    }
    .actions button.danger { color: var(--pl-red); }
    .empty { text-align: center; color: var(--pl-muted); padding: 24px; }
    .pagination { margin-top: 18px; }

    /* ---------- Badges ---------- */
    .badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
        background: #e5e7eb;
        color: var(--pl-gray);
    }
    .badge.green { background: #d1fae5; color: #065f46; }
    .badge.orange { background: #fef3c7; color: #92400e; }
    .badge.red { background: #fee2e2; color: #991b1b; }
    .badge.blue { background: #dbeafe; color: #1e40af; }

    /* Not enough room for everything on one line: brand and user on top, menu below. */
    @media (max-width: 1100px) {
        .pl-topbar { flex-wrap: wrap; gap: 0 20px; padding-top: 4px; }
        .pl-brand { order: 1; }
        .pl-user { order: 2; margin-left: auto; border-left: 0; padding-left: 0; }
        .pl-links {
            order: 3;
            flex: 0 0 100%;
            padding: 6px 0 10px;
            border-top: 1px solid #1f2937;
        }
    }

    @media (max-width: 760px) {
        .grid, .grid.three, .form-row { grid-template-columns: 1fr; }
        .pl-topbar { padding-left: 16px; padding-right: 16px; }
        .pl-user .who { display: none; }
    }
</style>