<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Question 20(b): the protected dashboard.
 * The "auth" middleware in routes/web.php blocks visitors who are not logged in.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        // Role-based access control: only admins get the list of all users.
        $allUsers = $user->role === 'admin'
            ? User::orderBy('id')->get(['username', 'email', 'role', 'last_login'])
            : collect();

        return view('dashboard', compact('user', 'allUsers'));
    }
}
