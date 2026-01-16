<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('welcome');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Trim email to handle whitespace
        $credentials = [
            'email' => trim($request->input('email')),
            'password' => $request->input('password'),
        ];
        
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Regenerate session for security
        $request->session()->regenerate();
        $user = Auth::user();

        // Redirect based on user role
        return match($user->role) {
            'employee' => redirect()->route('employee.dashboard'),
            'dept_manager' => redirect()->route('manager.dashboard'),
            'hr_admin', 'ceo' => redirect()->route('hr.dashboard'),
            default => redirect('/'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function profile(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user(),
            'message' => 'Success',
        ]);
    }

    public function showContactAdmin()
    {
        return view('contact-admin');
    }

    public function submitContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|in:login_issue,password_reset,account_access,other',
            'message' => 'required|string|max:2000',
            'urgent' => 'nullable|boolean',
        ]);

        // TODO: Save to database or send email notification
        // For now just redirect with success message

        return redirect()
            ->route('login')
            ->with('success', 'Your ticket has been submitted successfully. Our admin team will contact you soon.');
    }
}
