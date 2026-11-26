@extends('layouts.app')

@section('title', 'Login')

@section('content')
<main class="card">
    <h1>Log in</h1>

    @if (session('status'))
        <p class="notice">{{ session('status') }}</p>
    @endif

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    {{-- {{ }} escapes output (XSS protection). @csrf adds the CSRF token. --}}
    <form method="POST" action="{{ route('login.attempt') }}" autocomplete="off" novalidate>
        @csrf

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}"
               required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required maxlength="255">

        <button type="submit">Log in</button>
    </form>

    <p class="muted">No account? <a href="{{ route('register') }}">Register here</a>.</p>
</main>
@endsection
