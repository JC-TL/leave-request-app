<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Department;
use App\Models\Policy;
use App\Models\Request as LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// TODO: Add email notifications for approvals/rejections

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

        return view('hr.dashboard', compact(
            'pendingRequests',
            'totalPending',
            'totalApprovedThisMonth',
            'totalRejectedThisMonth',
            'totalEmployees',
            'calendarEvents'
        ));
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

        return view('hr.show-request', compact('leaveRequest', 'balance'));
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

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        
        // Check balance before approving
        if (!$balance || !$balance->hasSufficientBalance($leaveRequest->number_of_days)) {
            return back()
                ->withErrors(['error' => 'Employee has insufficient balance for this request.']);
        }

        // Deduct the days from balance
        $balance->deductDays($leaveRequest->number_of_days);

        // Update request status
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
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Must be manager approved before HR can reject
        if ($leaveRequest->status !== 'dept_manager_approved') {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected yet. It is still pending manager approval.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

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

        return view('hr.policies', compact('policies'));
    }

    public function updatePolicy(Request $request, $id)
    {
        $validated = $request->validate([
            'annual_entitlement' => 'required|integer|min:0|max:365',
        ]);

        $policy = Policy::findOrFail($id);
        $policy->update($validated);

        return redirect()
            ->route('hr.policies')
            ->with('success', 'Policy updated successfully.');
    }

    public function employees()
    {
        $employees = User::whereIn('role', ['employee', 'dept_manager'])
            ->with(['department', 'manager'])
            ->orderBy('name')
            ->paginate(15);

        return view('hr.employees', compact('employees'));
    }

    public function createEmployee()
    {
        $departments = Department::with('manager')->get();
        $managers = User::where('role', 'dept_manager')->get();
        $roles = ['employee', 'dept_manager'];

        return view('hr.create-employee', compact('departments', 'managers', 'roles'));
    }

    public function storeEmployee(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:employee,dept_manager',
            'department_id' => 'required|exists:departments,id',
            'manager_id' => 'nullable|exists:users,id',
        ]);

        // Managers don't have managers
        if ($validated['role'] === 'dept_manager') {
            $validated['manager_id'] = null;
        }

        // Hash password before saving
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        // Set up leave balances for new employee
        $currentYear = now()->year;
        $policies = Policy::all();
        
        foreach ($policies as $policy) {
            Balance::create([
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
