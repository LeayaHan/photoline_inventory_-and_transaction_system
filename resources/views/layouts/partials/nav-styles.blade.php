* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: var(--page-bg);
    color: #1f2937;
}

.navbar {
    position: sticky;
    top: 0;
    z-index: 1000;
    background: var(--nav-bg);
    color: white;
    padding: 18px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
}

.navbar h2 {
    margin: 0;
}

.nav-links {
    display: flex;
    align-items: center;
    gap: 18px;
}

.nav-links a {
    color: #d1d5db;
    text-decoration: none;
    font-size: 14px;
    padding: 4px 0;
    border-bottom: 2px solid transparent;
}

.nav-links a:hover {
    color: white;
}

.nav-links a.active {
    color: white;
    font-weight: bold;
    border-bottom-color: white;
}

.user-area {
    display: flex;
    align-items: center;
    gap: 18px;
}

.user-area span {
    font-size: 14px;
}

.logout-button {
    background: #dc2626;
    color: white;
    border: none;
    padding: 9px 14px;
    border-radius: 6px;
    cursor: pointer;
}

@media (max-width: 1000px) {
    .navbar {
        flex-direction: column;
        align-items: flex-start;
    }

    .nav-links {
        flex-wrap: wrap;
    }
}

@media print {
    .navbar {
        display: none !important;
    }
}
