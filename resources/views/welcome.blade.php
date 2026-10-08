<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1769e8">
    <title>Photoline | Inventory & Transaction System</title>

    <style>
        * { box-sizing: border-box; }

        :root {
            --blue: #1769e8;
            --blue-dark: #0b47b7;
            --red: #ed2b24;
            --ink: #172033;
            --muted: #68738a;
            --surface: #ffffff;
            --page: #f5f8fc;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: var(--ink);
            background: var(--page);
        }

        .page {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(420px, .92fr);
            overflow: hidden;
        }

        .brand-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 60px;
            overflow: hidden;
            background: linear-gradient(145deg, #0b47b7 0%, #1769e8 55%, #2582f2 100%);
        }

        .brand-panel::before,
        .brand-panel::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .brand-panel::before {
            width: 460px;
            height: 460px;
            right: -190px;
            top: -150px;
            background: rgba(255, 255, 255, .10);
        }

        .brand-panel::after {
            width: 360px;
            height: 360px;
            left: -190px;
            bottom: -160px;
            background: rgba(237, 43, 36, .18);
        }

        .brand-content {
            position: relative;
            z-index: 1;
            width: min(560px, 100%);
            color: #fff;
        }

        .logo-wrap {
            display: inline-flex;
            padding: 14px 18px;
            margin-bottom: 30px;
            border-radius: 18px;
            background: #fff;
            box-shadow: 0 18px 45px rgba(4, 36, 96, .22);
        }

        .logo {
            display: block;
            width: min(360px, 70vw);
            height: auto;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            margin: 0 0 14px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.7px;
            text-transform: uppercase;
            color: #eaf2ff;
        }

        .eyebrow::before {
            content: "";
            width: 28px;
            height: 3px;
            border-radius: 99px;
            background: var(--red);
        }

        .brand-content h1 {
            margin: 0;
            max-width: 540px;
            font-size: clamp(34px, 4.2vw, 58px);
            line-height: 1.03;
            letter-spacing: -1.7px;
        }

        .brand-content h1 span {
            color: #ffd9d7;
        }

        .brand-content p {
            max-width: 500px;
            margin: 20px 0 0;
            font-size: 17px;
            line-height: 1.7;
            color: #dceaff;
        }

        .feature-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 28px;
        }

        .feature {
            padding: 9px 13px;
            border: 1px solid rgba(255,255,255,.20);
            border-radius: 999px;
            background: rgba(255,255,255,.09);
            font-size: 13px;
            font-weight: 600;
            color: #fff;
        }

        .action-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
            background: var(--surface);
        }

        .action-content {
            width: min(440px, 100%);
        }

        .mini-logo {
            display: none;
            width: 190px;
            margin: 0 auto 28px;
        }

        .welcome-label {
            margin: 0 0 8px;
            color: var(--red);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .action-content h2 {
            margin: 0;
            font-size: 32px;
            line-height: 1.15;
            letter-spacing: -.7px;
        }

        .action-content .description {
            margin: 12px 0 30px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.6;
        }

        .login-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 52px;
            padding: 14px 24px;
            border-radius: 10px;
            background: var(--blue);
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: .2px;
            box-shadow: 0 10px 24px rgba(23, 105, 232, .20);
            transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .login-button:hover {
            background: var(--blue-dark);
            transform: translateY(-1px);
            box-shadow: 0 13px 28px rgba(23, 105, 232, .26);
        }

        .login-button:focus-visible {
            outline: 3px solid rgba(237, 43, 36, .28);
            outline-offset: 3px;
        }

        .note {
            margin: 18px 0 0;
            text-align: center;
            color: #8a93a3;
            font-size: 12px;
        }

        .red-line {
            width: 44px;
            height: 4px;
            margin-top: 28px;
            border-radius: 99px;
            background: var(--red);
        }

        @media (max-width: 900px) {
            .page {
                grid-template-columns: 1fr;
            }

            .brand-panel {
                min-height: auto;
                padding: 48px 28px 54px;
            }

            .brand-content h1 {
                font-size: 42px;
            }

            .action-panel {
                padding: 48px 28px 60px;
            }

            .mini-logo {
                display: block;
            }
        }

        @media (max-width: 520px) {
            .brand-panel {
                padding: 34px 22px 42px;
            }

            .logo-wrap {
                margin-bottom: 24px;
                padding: 10px 12px;
                border-radius: 14px;
            }

            .logo {
                width: 280px;
                max-width: 100%;
            }

            .brand-content h1 {
                font-size: 36px;
                letter-spacing: -1px;
            }

            .brand-content p {
                font-size: 15px;
            }

            .action-panel {
                padding: 42px 22px 50px;
            }

            .action-content h2 {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>
    <main class="page">
        <section class="brand-panel">
            <div class="brand-content">
                <div class="logo-wrap">
                    <img
                        src="{{ asset('images/photoline-logo.jpg') }}"
                        alt="Photoline"
                        class="logo"
                    >
                </div>

                <p class="eyebrow">Inventory & Transaction System</p>

                <h1>
                    Keep your business <span>organized.</span>
                </h1>

                <p>
                    A simple workspace for managing Photoline inventory, transactions,
                    records, and day-to-day operations in one place.
                </p>

                <div class="feature-row">
                    <span class="feature">Inventory</span>
                    <span class="feature">Transactions</span>
                    <span class="feature">Reports</span>
                    <span class="feature">Audit Records</span>
                </div>
            </div>
        </section>

        <section class="action-panel">
            <div class="action-content">
                <img
                    src="{{ asset('images/photoline-logo.jpg') }}"
                    alt="Photoline"
                    class="mini-logo"
                >

                <p class="welcome-label">Welcome</p>

                <h2>Sign in to Photoline</h2>

                <p class="description">
                    Access your inventory and transaction workspace securely.
                </p>

                <a href="{{ route('login') }}" class="login-button">
                    Login
                </a>

                <div class="red-line"></div>

                <p class="note">
                    Photoline Inventory & Transaction System
                </p>
            </div>
        </section>
    </main>
</body>
</html>
