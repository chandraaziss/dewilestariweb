@extends('layouts.app')

@section('content')

<div class="admin-container">

    <div class="login-box">

        <h1>
            🔒 Login Admin
        </h1>

        @if(session('error'))

            <div class="error-box">
                {{ session('error') }}
            </div>

        @endif

        <form method="POST"
              action="/admin/login">

            @csrf

            <input
                type="text"
                name="username"
                placeholder="Username"
                required>

            <input
                type="password"
                name="password"
                placeholder="Password"
                required>

            <button type="submit">
                Login
            </button>

        </form>

    </div>

</div>

@endsection