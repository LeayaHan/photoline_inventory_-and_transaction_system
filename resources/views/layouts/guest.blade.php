<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Photoline Inventory and Transaction System</title>

    <link rel="preconnect" href="https://fonts.bunny.net">

    <link
        href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
        rel="stylesheet"
    />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --photoline-blue: #1769e8;
            --photoline-blue-dark: #0b47b7;
            --photoline-red: #ed2b24;
            --photoline-ink: #172033;
            --photoline-muted: #68738a;
            --photoline-page: #f5f8fc;
        }

        .photoline-login-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(
                    circle at 8% 10%,
                    rgba(23, 105, 232, .09),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 92% 90%,
                    rgba(237, 43, 36, .07),
                    transparent 26%
                ),
                var(--photoline-page);
        }

        .photoline-login-page::before {
            content: "";
            position: absolute;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            top: -250px;
            right: -180px;
            background: rgba(23, 105, 232, .07);
        }

        .photoline-login-shell {
            position: relative;
            z-index: 1;
            width: min(100%, 1040px);
            min-height: 590px;
            display: grid;
            grid-template-columns: .92fr 1.08fr;
            overflow: hidden;
            border: 1px solid rgba(23, 32, 51, .07);
            border-radius: 24px;
            background: #fff;
            box-shadow: 0 24px 70px rgba(18, 37, 66, .13);
        }

        .photoline-login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px;
            background: linear-gradient(
                150deg,
                #0b47b7 0%,
                #1769e8 58%,
                #2582f2 100%
            );
        }

        .photoline-login-brand-inner {
            width: min(330px, 100%);
            color: #fff;
        }

        .photoline-login-logo {
            display: block;
            width: 100%;
            max-width: 310px;
            margin: 0 auto 30px;
            border-radius: 12px;
            background: #fff;
            box-shadow: 0 14px 32px rgba(4, 36, 96, .20);
        }

        .photoline-login-brand h1 {
            margin: 0;
            font-size: 30px;
            line-height: 1.15;
            letter-spacing: -.7px;
        }

        .photoline-login-brand p {
            margin: 14px 0 0;
            color: #dceaff;
            font-size: 14px;
            line-height: 1.65;
        }

        .photoline-login-brand-line {
            width: 42px;
            height: 4px;
            margin-top: 25px;
            border-radius: 99px;
            background: var(--photoline-red);
        }

        .photoline-login-form-area {
            display: flex;
            align-items: center;
            padding: 56px 64px;
        }

        .photoline-login-form {
            width: min(100%, 410px);
            margin: 0 auto;
        }

        .photoline-login-heading {
            margin-bottom: 28px;
        }

        .photoline-login-kicker {
            margin: 0 0 7px;
            color: var(--photoline-red);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.6px;
            text-transform: uppercase;
        }

        .photoline-login-heading h2 {
            margin: 0;
            color: var(--photoline-ink);
            font-size: 30px;
            line-height: 1.2;
            letter-spacing: -.7px;
        }

        .photoline-login-heading p {
            margin: 9px 0 0;
            color: var(--photoline-muted);
            font-size: 14px;
            line-height: 1.55;
        }

        .photoline-field-label {
            color: #33405a !important;
            font-size: 13px !important;
            font-weight: 600 !important;
        }

        .photoline-field {
            width: 100% !important;
            margin-top: 7px !important;
            min-height: 46px !important;
            border-color: #dbe2ed !important;
            border-radius: 9px !important;
            box-shadow: none !important;
        }

        .photoline-field:focus {
            border-color: var(--photoline-blue) !important;
            box-shadow: 0 0 0 3px rgba(23, 105, 232, .11) !important;
        }

        .photoline-check {
            color: #657087;
        }

        .photoline-check input:checked {
            border-color: var(--photoline-blue);
            background-color: var(--photoline-blue);
        }

        .photoline-login-button {
            min-height: 46px;
            padding: 11px 22px !important;
            border-radius: 9px !important;
            background: var(--photoline-blue) !important;
            box-shadow: 0 8px 20px rgba(23, 105, 232, .18);
            transition:
                transform .18s ease,
                background .18s ease,
                box-shadow .18s ease;
        }

        .photoline-login-button:hover {
            background: var(--photoline-blue-dark) !important;
            box-shadow: 0 10px 24px rgba(23, 105, 232, .23);
            transform: translateY(-1px);
        }

        .photoline-forgot {
            color: #667188 !important;
        }

        .photoline-forgot:hover {
            color: var(--photoline-blue) !important;
        }

        .photoline-status {
            border-radius: 9px;
        }

        @media (max-width: 800px) {
            .photoline-login-shell {
                grid-template-columns: 1fr;
                min-height: auto;
                max-width: 520px;
            }

            .photoline-login-brand {
                padding: 30px;
                text-align: center;
            }

            .photoline-login-logo {
                max-width: 260px;
                margin-bottom: 20px;
            }

            .photoline-login-brand h1 {
                font-size: 24px;
            }

            .photoline-login-brand-line {
                margin: 20px auto 0;
            }

            .photoline-login-form-area {
                padding: 38px 30px 44px;
            }
        }

        @media (max-width: 480px) {
            .photoline-login-page {
                padding: 14px;
            }

            .photoline-login-shell {
                border-radius: 18px;
            }

            .photoline-login-brand {
                padding: 24px 22px;
            }

            .photoline-login-form-area {
                padding: 32px 22px 36px;
            }
        }
    </style>
</head>

<body class="antialiased">
    <div class="photoline-login-page">
        <div class="photoline-login-shell">

            <aside class="photoline-login-brand">
                <div class="photoline-login-brand-inner">

                    <img
                        src="{{ asset('images/photoline-logo.jpg') }}"
                        alt="Photoline"
                        class="photoline-login-logo"
                    >

                    <h1>
                        Manage Photoline with confidence.
                    </h1>

                    <p>
                        Keep inventory, transactions, reports, and audit records
                        organized in one secure workspace.
                    </p>

                    <div class="photoline-login-brand-line"></div>

                </div>
            </aside>

            <main class="photoline-login-form-area">
                <div class="photoline-login-form">
                    {{ $slot }}
                </div>
            </main>

        </div>
    </div>
</body>
</html>

