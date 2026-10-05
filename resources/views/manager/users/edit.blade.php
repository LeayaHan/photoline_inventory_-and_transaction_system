@extends('layouts.panel')

@section('title', 'Edit Staff Account')

@push('styles')
<style>
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }
        h1 {
            margin-top: 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 15px;
        }
        input:focus {
            outline: none;
            border-color: #2563eb;
        }
        .error {
            color: #dc2626;
            margin-top: 5px;
            font-size: 13px;
        }
        .buttons {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }
        .btn {
            padding: 11px 18px;
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
        .btn-secondary {
            background: #e5e7eb;
            color: #111827;
        }
    
</style>
@endpush

@section('content')
    


    <div class="container">

        <div class="card">

            <h1>
                Edit Staff Account
            </h1>


            <form
                action="{{ route('users.update', $user) }}"
                method="POST"
            >

                @csrf

                @method('PUT')


                <div class="form-group">

                    <label for="name">
                        Staff Name
                    </label>

                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        required
                    >

                    @error('name')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        required
                    >

                    @error('email')

                        <div class="error">
                            {{ $message }}
                        </div>

                    @enderror

                </div>


                <div class="buttons">

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Save Changes
                    </button>


                    <a
                        href="{{ route('users.index') }}"
                        class="btn btn-secondary"
                    >
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>
@endsection
