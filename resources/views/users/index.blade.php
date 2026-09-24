<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>User Management</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f5f6f8;
            color: #222;
        }

        .navbar {
            background: #111827;
            color: white;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 40px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0;
        }

        .card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f3f4f6;
            text-align: left;
            padding: 14px;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
        }

        .btn {
            display: inline-block;
            padding: 9px 14px;
            border-radius: 6px;
            border: none;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .success {
            background: #dcfce7;
            color: #166534;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-manager {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-staff {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .actions {
            display: flex;
            gap: 8px;
        }

        .actions form {
            margin: 0;
        }
    </style>
</head>

<body>

    <div class="navbar">

        <h2>Photoline Abreeza</h2>

        <div>
            <span>{{ auth()->user()->name }}</span>

            <span>
                ({{ ucfirst(auth()->user()->role) }})
            </span>

            <a href="{{ route('dashboard') }}">
                Dashboard
            </a>
        </div>

    </div>


    <div class="container">

        <div class="page-header">

            <h1>User Management</h1>

            <a
                href="{{ route('users.create') }}"
                class="btn btn-primary"
            >
                + Add Staff
            </a>

        </div>


        @if(session('success'))

            <div class="success">
                {{ session('success') }}
            </div>

        @endif


        @if(session('error'))

            <div class="error">
                {{ session('error') }}
            </div>

        @endif


        <div class="card">

            @if($users->count() > 0)

                <table>

                    <thead>

                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Date Created</th>
                            <th>Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                        @foreach($users as $user)

                            <tr>

                                <td>
                                    {{ $user->name }}
                                </td>

                                <td>
                                    {{ $user->email }}
                                </td>

                                <td>

                                    @if($user->role === 'manager')

                                        <span class="badge badge-manager">
                                            Manager
                                        </span>

                                    @else

                                        <span class="badge badge-staff">
                                            Staff
                                        </span>

                                    @endif

                                </td>

                                <td>
                                    {{ $user->created_at->format('M d, Y') }}
                                </td>

                                <td>

                                    <div class="actions">

                                        <a
                                            href="{{ route('users.edit', $user) }}"
                                            class="btn btn-warning"
                                        >
                                            Edit
                                        </a>


                                        @if($user->id !== auth()->id())

                                            <form
                                                action="{{ route('users.destroy', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm('Are you sure you want to delete this account?');"
                                            >

                                                @csrf

                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-danger"
                                                >
                                                    Delete
                                                </button>

                                            </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            @else

                <p>
                    No users found.
                </p>

            @endif

        </div>

    </div>

</body>

</html>