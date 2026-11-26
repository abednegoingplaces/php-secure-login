@extends('layouts.app')

@section('title', 'Register')

@section('content')
<main class="card">
    <h1>Create account</h1>

    @if ($errors->any())
        <ul class="errors">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form method="POST" action="{{ route('register.store') }}" novalidate>
        @csrf

        <label for="name">Full name</label>
        <input type="text" id="name" name="name" value="{{ old('name') }}" required maxlength="100">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ old('username') }}"
               required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ old('email') }}" required maxlength="120">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8" maxlength="255">

        <label for="password_confirmation">Confirm password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" maxlength="255">

        <button type="submit">Register</button>
    </form>

    <p class="muted">Already registered? <a href="{{ route('login') }}">Log in</a>.</p>
</main>
@endsection
