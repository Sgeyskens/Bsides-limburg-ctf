<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AdminUserController extends Controller
{
    /**
     * Display a listing of all users.
     * Sets the admin flag cookie when accessed.
     */
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        // Set the flag cookie for admin - this is what players need to steal
        $response = response()->view('admin.users.index', compact('users'));
        $response->cookie('admin_flag', 'CTF{STORED_XSS_COOKIE_THEFT}', 60, '/', null, false, false);

        return $response;
    }

    /**
     * Display the specified user's profile.
     * VULNERABLE: User bio is rendered with raw HTML, allowing XSS execution.
     */
    public function show(User $user)
    {
        // Set the flag cookie for admin - this is what players need to steal
        $response = response()->view('admin.users.show', compact('user'));
        $response->cookie('admin_flag', 'CTF{STORED_XSS_COOKIE_THEFT}', 60, '/', null, false, false);

        return $response;
    }
}
