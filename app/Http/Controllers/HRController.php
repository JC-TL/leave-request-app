<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;



class HRController extends Controller
{
    public function dashboard()
    {
        // Get requests that need HR attention (pending or manager approved)
        $pendingRequests = LeaveRequest::whereIn('status', ['pending', 'dept_manager_approved'])
            ->with(['employee', 'employee.department', 'departmentManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Count totals for dashboard stats
        $totalPending = LeaveRequest::whereIn('status', ['pending', 'dept_manager_approved'])->count();
        
        $totalApprovedThisMonth = LeaveRequest::approved()
            ->whereMonth('approved_by_hr_at', now()->month)
            ->whereYear('approved_by_hr_at', now()->year)
            ->count();
            
        $totalRejectedThisMonth = LeaveRequest::rejected()
            ->whereMonth('rejected_at', now()->month)
            ->whereYear('rejected_at', now()->year)
            ->count();
            
        // Count active employees
        $totalEmployees = User::whereIn('role', ['employee', 'dept_manager'])->count();

        // Build calendar events from approved requests
        $calendarEvents = LeaveRequest::where('status', 'hr_approved')
            ->with(['employee', 'employee.department'])
            ->get()
            ->map(function ($request) {
                // Use department color if available, otherwise default blue
                $color = optional($request->employee->department)->color ?? '#3b82f6';

                return [
                    'title' => $request->employee->name,
                    'start' => $request->start_date->format('Y-m-d'),
                    'end' => $request->end_date->copy()->addDay()->format('Y-m-d'),
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'allDay' => true,
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('HR/Dashboard', [
            'pendingRequests' => $pendingRequests,
            'totalPending' => $totalPending,
            'totalApprovedThisMonth' => $totalApprovedThisMonth,
            'totalRejectedThisMonth' => $totalRejectedThisMonth,
            'totalEmployees' => $totalEmployees,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    public function showRequest($id)
    {
        $leaveRequest = LeaveRequest::with([
            'employee',
            'employee.department',
            'departmentManager',
            'hrApprover'
        ])->findOrFail($id);

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);

        return Inertia::render('HR/ShowRequest', [
            'leaveRequest' => $leaveRequest,
            'balance' => $balance,
        ]);
    }

    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Can only approve if manager already approved it
        if ($leaveRequest->status !== 'dept_manager_approved') {
            return back()
                ->withErrors(['error' => 'This request cannot be approved yet. It is still pending manager approval.']);
        }

        // Deduct balance only when HR approves (after manager approval)
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        if ($balance) {
            $balance->deductDays($leaveRequest->number_of_days);
        }

        $leaveRequest->update([
            'status' => 'hr_approved',
            'approved_by_hr_id' => $user->id,
            'hr_comment' => $request->input('comment'),
            'approved_by_hr_at' => now(),
        ]);

        return redirect()
            ->route('hr.dashboard')
            ->with('success', 'Leave request approved successfully.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Must be manager approved before HR can reject
        if ($leaveRequest->status !== 'dept_manager_approved') {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected yet. It is still pending manager approval.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Balance was never deducted (only deducted when HR approves), so nothing to restore
        $leaveRequest->update([
            'status' => 'hr_rejected',
            'hr_comment' => $validated['reason'],
            'rejected_at' => now(),
        ]);

        return redirect()
            ->route('hr.dashboard')
            ->with('success', 'Leave request rejected.');
    }

    public function policies()
    {
        $policies = Policy::orderBy('leave_type')->get();

        return Inertia::render('HR/Policies', [
            'policies' => $policies,
        ]);
    }

    public function updatePolicy(Request $request, $id)
    {
        $validated = $request->validate([
            'annual_entitlement' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],
        ], [
            'annual_entitlement.required' => 'Annual entitlement is required.',
            'annual_entitlement.integer' => 'Annual entitlement must be a whole number.',
            'annual_entitlement.min' => 'Annual entitlement cannot be less than 0.',
            'annual_entitlement.max' => 'Annual entitlement cannot exceed 365 days.',
        ]);

        $policy = Policy::findOrFail($id);

        DB::transaction(function () use ($policy, $validated) {
            $policy->update(['annual_entitlement' => $validated['annual_entitlement']]);

            // Propagate to current-year leave balances so the policy change reflects for all employees
            $currentYear = now()->year;
            LeaveBalance::where('leave_type', $policy->leave_type)
                ->where('year', $currentYear)
                ->get()
                ->each(function (LeaveBalance $balance) use ($validated) {
                    // Never set balance below already-used days (avoid negative available)
                    $newBalance = max($validated['annual_entitlement'], $balance->used);
                    $balance->update(['balance' => $newBalance]);
                });
        });

        return redirect()
            ->route('hr.policies')
            ->with('success', 'Policy updated successfully. Current year leave balances have been updated.');
    }

    public function employees()
    {
        $employees = User::whereIn('role', ['employee', 'dept_manager'])
            ->with(['department', 'manager'])
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('HR/Employees', [
            'employees' => $employees,
        ]);
    }

    public function createEmployee()
    {
        $departments = Department::with('manager')->get();
        $roles = ['employee', 'dept_manager'];

        return Inertia::render('HR/CreateEmployee', [
            'departments' => $departments,
            'roles' => $roles,
        ]);
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:employee,dept_manager',
            'department_id' => 'required|exists:departments,id',
        ]);

        // Automatically assign manager: employees get their department's manager; dept managers have none
        if ($validated['role'] === 'employee') {
            $department = Department::find($validated['department_id']);
            $validated['manager_id'] = $department->dept_manager_id;
        } else {
            $validated['manager_id'] = null;
        }

        // Hash password before saving
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Set up leave balances for new employee
        $currentYear = now()->year;
        $policies = Policy::all();
        
        foreach ($policies as $policy) {
            LeaveBalance::create([
                'user_id' => $user->id,
                'leave_type' => $policy->leave_type,
                'balance' => $policy->annual_entitlement,
                'used' => 0,
                'year' => $currentYear,
            ]);
        }

        return redirect()
            ->route('hr.employees')
            ->with('success', sprintf('Employee %s created successfully with leave balances initialized.', $user->name));
    }
}
