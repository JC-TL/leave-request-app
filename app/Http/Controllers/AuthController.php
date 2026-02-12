<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\PendingRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return Inertia::render('Auth/Login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
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

        return match ($user->role) {
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
        return Inertia::render('Auth/ContactAdmin');
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

        return redirect()
            ->route('login')
            ->with('success', 'Your ticket has been submitted successfully. Our admin team will contact you soon.');
    }

    public function showRegisterForm()
    {
        $departments = Department::with('manager')->get();
        $roles = ['employee', 'dept_manager'];

        return Inertia::render('Auth/Register', [
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|string|max:1',
            'email' => [
                'required',
                'email',
                'max:255',
                function ($attr, $value, $fail) {
                    if (Employee::where('email', $value)->exists() || PendingRegistration::where('email', $value)->exists()) {
                        $fail('This email is already registered or pending approval.');
                    }
                },
            ],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:employee,dept_manager',
            'dept_id' => 'required|exists:departments,dept_id',
        ]);

        if ($validated['role'] === 'employee') {
            $department = Department::find($validated['dept_id']);
            $validated['manager_id'] = $department?->dept_manager_id;
        } else {
            $validated['manager_id'] = null;
        }

        $validated['password'] = Hash::make($validated['password']);
        PendingRegistration::create($validated);

        return redirect()
            ->route('login')
            ->with('success', 'Your registration has been submitted. HR will review and approve your account before you can sign in.');
    }
}
