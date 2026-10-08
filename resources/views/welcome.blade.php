<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>Photoline</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f5f5;
            color: #222;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px;
        }

        .card {
            width: 100%;
            max-width: 600px;
            background: white;
            border-radius: 14px;
            padding: 45px 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .logo {
            display: block;
            width: 300px;
            max-width: 80%;
            height: auto;
            margin: 0 auto 25px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #666;
            font-size: 16px;
        }

        .login-button {
            display: inline-block;
            padding: 12px 38px;
            background: #111827;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-size: 15px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .login-button:hover {
            background: #000;
        }

        @media (max-width: 600px) {

            .card {
                padding: 35px 20px;
            }

            .logo {
                width: 260px;
            }

            .subtitle {
                font-size: 14px;
            }

        }

    </style>

</head>

<body>

    <div class="page">

        <div class="card">

            <img
                src="{{ asset('images/photoline-logo.jpg') }}"
                alt="Photoline"
                class="logo"
            >

            <p class="subtitle">
                Inventory & Transaction System
            </p>

            <a
                href="{{ route('login') }}"
                class="login-button"
            >
                Log In
            </a>

        </div>

    </div>

</body>

</html>