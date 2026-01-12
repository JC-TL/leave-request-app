<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('welcome');
    }

    /**
     * Login user and redirect based on role.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => trim($request->input('email')),
            'password' => $request->input('password'),
        ];
        
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        return match($user->role) {
            'employee' => redirect()->route('employee.dashboard'),
            'dept_manager' => redirect()->route('manager.dashboard'),
            'hr_admin' => redirect()->route('hr.dashboard'),
            'ceo' => redirect()->route('hr.dashboard'), // CEO can access HR dashboard
            default => redirect('/'),
        };
    }

    /**
     * Logout user.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Success',
        ]);
    }
}
