* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: var(--staff-page, #f5f8fc);
    color: var(--staff-ink, #172033);
}

.staff-page-content {
    min-height: calc(100vh - 76px);
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    min-height: 76px;
    display: flex;
    align-items: center;
    gap: 18px;
    padding: 12px 30px;
}

.nav-brand {
    display: flex;
    align-items: center;
    gap: 11px;
    flex-shrink: 0;
}

.nav-logo-box {
    width: 43px;
    height: 43px;
    overflow: hidden;
    border-radius: 9px;
    background: #fff;
}

.nav-logo-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.nav-brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.05;
}

.nav-brand-text strong {
    font-size: 17px;
}

.nav-brand-text span {
    margin-top: 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
}

.nav-role {
    display: flex;
    align-items: center;
    gap: 7px;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}

.role-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #ed2b24;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 3px;
    flex: 1;
}

.nav-links a {
    min-height: 40px;
    display: flex;
    align-items: center;
    padding: 0 13px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: .18s ease;
}

.navbar-staff {
    color: #172033;
    background: #fff;
    border-bottom: 1px solid #e4e9f1;
    box-shadow: 0 3px 15px rgba(20, 45, 80, .06);
}

.navbar-staff .nav-brand-text strong {
    color: #172033;
}

.navbar-staff .nav-brand-text span {
    color: #7b8799;
}

.navbar-staff .nav-role {
    color: #536078;
    background: #f2f6fc;
    border: 1px solid #e3eaf4;
}

.navbar-staff .nav-links a {
    color: #68748a;
}

.navbar-staff .nav-links a:hover {
    color: #1769e8;
    background: #f0f6ff;
}

.navbar-staff .nav-links a.active {
    color: #1769e8;
    background: #eaf2ff;
}

.navbar-manager {
    color: #fff;
    background: #0b1f44;
    box-shadow: 0 5px 20px rgba(11, 31, 68, .16);
}

.navbar-manager .nav-brand-text strong {
    color: #fff;
}

.navbar-manager .nav-brand-text span {
    color: #9fb9df;
}

.navbar-manager .nav-role {
    color: #dceaff;
    background: rgba(255,255,255,.08);
}

.navbar-manager .nav-links a {
    color: #b9c8df;
}

.navbar-manager .nav-links a:hover {
    color: #fff;
    background: rgba(255,255,255,.07);
}

.navbar-manager .nav-links a.active {
    color: #fff;
    background: #1769e8;
}

.user-area {
    display: flex;
    align-items: center;
    gap: 13px;
    padding-left: 18px;
    border-left: 1px solid #e4e8ef;
    flex-shrink: 0;
}

.navbar-manager .user-area {
    border-left-color: rgba(255,255,255,.12);
}

.user-info {
    display: flex;
    flex-direction: column;
    line-height: 1.15;
}

.user-name {
    font-size: 12px;
    font-weight: 700;
}

.navbar-staff .user-name {
    color: #172033;
}

.navbar-manager .user-name {
    color: #fff;
}

.user-role {
    margin-top: 4px;
    font-size: 10px;
    color: #7a8599;
}

.navbar-manager .user-role {
    color: #9fb0c9;
}

.logout-button {
    border: 0;
    padding: 9px 13px;
    border-radius: 8px;
    background: #ed2b24;
    color: #fff;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
}

.logout-button:hover {
    background: #c9211b;
}

@media (max-width: 1050px) {
    .navbar {
        flex-wrap: wrap;
        padding: 11px 20px;
    }

    .nav-links {
        order: 4;
        width: 100%;
        overflow-x: auto;
    }
}

@media (max-width: 650px) {
    .navbar {
        padding: 10px 14px;
        gap: 10px;
    }

    .nav-logo-box {
        width: 37px;
        height: 37px;
    }

    .nav-brand-text span,
    .user-info {
        display: none;
    }

    .user-area {
        margin-left: auto;
        padding-left: 0;
        border-left: 0;
    }

    .nav-links a {
        white-space: nowrap;
        min-height: 36px;
        padding: 0 10px;
        font-size: 12px;
    }
}
