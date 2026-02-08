<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Try to find user by email or username
        $user = DB::table('users')
            ->where('email', $credentials['email'])
            ->orWhere('username', $credentials['email'])
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->withInput($request->except('password'));
        }

        // Verify password
        if (!Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->withInput($request->except('password'));
        }

        // Log the user in
        Auth::loginUsingId($user->user_id, $request->filled('remember'));

        $request->session()->regenerate();

        return redirect()->intended('/account');
    }

    /**
     * Show the registration form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'terms' => 'required|accepted',
        ]);

        // Create the user
        $userId = DB::table('users')->insertGetId([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'avatar_url' => null,
            'is_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Log the user in
        Auth::loginUsingId($userId);

        return redirect('/account')->with('success', 'Welcome to Camp Crystal Lake!');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Show forgot password form
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        
        // For now, just redirect back with a message

        return back()->with('success', 'If an account exists with that email, you will receive a password reset link.');
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:500',
        ];

        // Only validate password if user is trying to change it
        if ($request->filled('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $validated = $request->validate($rules);

        // If changing password, verify current password
       if (
    $request->filled('password') &&
    !Hash::check($request->current_password, $user->password)
) {
    return back()->withErrors([
        'current_password' => 'Current password is incorrect.'
    ])->withInput();
}


        // Handle avatar upload
        $avatarUrl = $user->getRawOriginal('avatar_url'); // Get raw value without accessor
        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            $filename = 'avatar_' . $user->user_id . '_' . time() . '.' . $avatar->getClientOriginalExtension();
            $avatar->move(public_path('images/avatars'), $filename);
            $avatarUrl = '/images/avatars/' . $filename;
        }

        // Update user - VULNERABLE: bio is stored without sanitization
        DB::table('users')
            ->where('user_id', $user->user_id)
            ->update([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'password' => $request->filled('password') ? Hash::make($validated['password']) : $user->password,
                'avatar_url' => $avatarUrl,
                'bio' => $request->input('bio'),
                'updated_at' => now(),
            ]);

        return redirect()->route('account')->with('success', 'Profile updated successfully!');
    }
}
