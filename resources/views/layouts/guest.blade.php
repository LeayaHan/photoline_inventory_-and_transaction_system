<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Photoline') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            box-sizing: border-box;
        }

        html,
        body {
            width: 100%;
            min-height: 100%;
            margin: 0;
        }

        body {
            font-family: 'Figtree', sans-serif;
            color: #202b3c;
            background: #f4f6fa;
        }

        .photoline-login-page {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .photoline-login-wrapper {
            width: 100%;
            max-width: 460px;
            margin: auto;
        }

        /* Logo */
        .photoline-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            text-align: center;
        }

        .photoline-brand a {
            display: inline-block;
            line-height: 0;
        }

        .photoline-brand img {
            display: block;
            width: 140px;
            height: 140px;
            object-fit: contain;
            margin: 0 auto;
        }

        /* Login card */
        .photoline-login-card {
            width: 100%;
            padding: 30px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            box-shadow: 0 12px 35px rgba(31, 41, 55, 0.08);
        }

        /* Login heading */
        .photoline-login-heading {
            margin-bottom: 22px;
        }

        .photoline-login-kicker {
            margin: 0 0 8px;
            color: #c62828;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1.5px;
        }

        .photoline-login-heading h2 {
            margin: 0 0 8px;
            color: #1f2937;
            font-size: 25px;
            line-height: 1.35;
            font-weight: 700;
        }

        .photoline-login-heading > p:last-child {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        .photoline-status {
            margin-bottom: 16px;
            color: #166534;
            font-size: 14px;
        }

        /* Email and password */
        .photoline-form-group {
            margin-top: 18px;
        }

        .photoline-field-label {
            display: block;
            margin: 0 0 7px;
            color: #374151;
            font-size: 14px;
            line-height: 1.4;
            font-weight: 600;
        }

        .photoline-login-card input.photoline-field {
            display: block;
            width: 100%;
            min-height: 46px;
            margin: 0;
            padding: 11px 13px;
            color: #111827;
            background: #f9fafb;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            font-family: inherit;
            font-size: 14px;
            line-height: 1.5;
            box-shadow: none;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .photoline-login-card input.photoline-field:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .photoline-login-card input.photoline-field::placeholder {
            color: #9ca3af;
        }

        .photoline-error {
            margin-top: 6px;
            color: #dc2626;
            font-size: 12px;
            line-height: 1.5;
        }

        /* Remember me and forgot password */
        .photoline-login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 20px;
        }

        .photoline-login-options .photoline-check {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0;
            color: #4b5563;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 400;
            cursor: pointer;
        }

        .photoline-login-options .photoline-check input {
            width: 16px;
            height: 16px;
            margin: 0;
            padding: 0;
            accent-color: #2563eb;
            cursor: pointer;
        }

        .photoline-login-card .photoline-forgot {
            color: #2563eb;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 600;
            text-decoration: none;
        }

        .photoline-login-card .photoline-forgot:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        /* Login button */
        .photoline-login-card button.photoline-login-button {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 48px;
            margin: 24px 0 0;
            padding: 13px 18px;
            color: #ffffff;
            background: #1f2937;
            border: 0;
            border-radius: 7px;
            font-family: inherit;
            font-size: 13px;
            line-height: 1.4;
            font-weight: 700;
            letter-spacing: 0.7px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .photoline-login-card button.photoline-login-button:hover {
            background: #c62828;
        }

        .photoline-login-card button.photoline-login-button:focus-visible {
            outline: 3px solid rgba(37, 99, 235, 0.35);
            outline-offset: 3px;
        }

        /* Footer inside login card */
        .photoline-login-footer {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            margin-top: 24px;
            padding-top: 18px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
        }

        .photoline-login-footer span:first-child {
            color: #374151;
            font-size: 11px;
            line-height: 1.4;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .photoline-login-footer span:last-child {
            color: #9ca3af;
            font-size: 11px;
            line-height: 1.5;
        }

        /* Responsive layout */
        @media (max-width: 480px) {
            .photoline-login-page {
                padding: 18px 14px;
            }

            .photoline-brand {
                margin-bottom: 14px;
            }

            .photoline-brand img {
                width: 110px;
                height: 110px;
            }

            .photoline-login-card {
                padding: 24px 20px;
            }

            .photoline-login-heading h2 {
                font-size: 23px;
            }

            .photoline-login-options {
                gap: 10px;
            }
        }

        /* Keep normal desktop login screens from scrolling */
        @media (min-height: 650px) {
            body:has(.photoline-login-page) {
                overflow: hidden;
            }
        }
    </style>
</head>

<body>
    <main class="photoline-login-page">
        <div class="photoline-login-wrapper">

            <div class="photoline-brand">
                    <img
                        src="{{ asset('images/photoline-logo.jpg') }}"
                        alt="Photoline Logo"
                    >
            </div>

            <section class="photoline-login-card">
                {{ $slot }}
            </section>

        </div>
    </main>
</body>
</html>
