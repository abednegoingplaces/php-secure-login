@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<main class="card wide">
    <header class="bar">
        <h1>Welcome, {{ $user->name }}</h1>

        {{-- Logout is a POST request with a CSRF token, so another site cannot log you out. --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="button">Log out</button>
        </form>
    </header>

    <p class="muted">You are logged in. This page is protected by the "auth" middleware.</p>

    <table>
        <tr><th>Username</th><td>{{ $user->username }}</td></tr>
        <tr><th>Email</th><td>{{ $user->email }}</td></tr>
        <tr><th>Role</th><td>{{ $user->role }}</td></tr>
        <tr><th>Member since</th><td>{{ $user->created_at }}</td></tr>
        <tr><th>Last login</th><td>{{ $user->last_login ?? 'First login' }}</td></tr>
    </table>

    @if ($allUsers->isNotEmpty())
        <h2>All users (admin only)</h2>
        <table>
            <tr><th>Username</th><th>Email</th><th>Role</th><th>Last login</th></tr>
            @foreach ($allUsers as $row)
                <tr>
                    <td>{{ $row->username }}</td>
                    <td>{{ $row->email }}</td>
                    <td>{{ $row->role }}</td>
                    <td>{{ $row->last_login ?? '-' }}</td>
                </tr>
            @endforeach
        </table>
    @endif
</main>
@endsection
