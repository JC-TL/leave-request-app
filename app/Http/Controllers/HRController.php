<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as LeaveRequest;
use App\Models\User;
use App\Models\Policy;

class HRController extends Controller
{
    /**
     * Display the HR dashboard.
     */
    public function dashboard()
    {
        // Get all requests awaiting HR approval (dept_manager_approved or pending)
        $pendingRequests = LeaveRequest::whereIn('status', ['dept_manager_approved', 'pending'])
            ->with(['employee', 'employee.department', 'departmentManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Get stats
        $totalPending = LeaveRequest::whereIn('status', ['dept_manager_approved', 'pending'])->count();
        $totalApprovedThisMonth = LeaveRequest::approved()
            ->whereMonth('approved_by_hr_at', now()->month)
            ->whereYear('approved_by_hr_at', now()->year)
            ->count();
        $totalRejectedThisMonth = LeaveRequest::rejected()
            ->whereMonth('rejected_at', now()->month)
            ->whereYear('rejected_at', now()->year)
            ->count();
        $totalEmployees = User::whereIn('role', ['employee', 'dept_manager'])->count();

        // Get all approved leave requests for calendar with department colors
        $calendarEvents = LeaveRequest::where('status', 'hr_approved')
            ->with(['employee', 'employee.department'])
            ->get()
            ->map(function ($request) {
                $department = $request->employee->department;
                $color = $department && $department->color ? $department->color : '#6b7280'; // Default gray if no department or color
                
                return [
                    'title' => '', // Empty title - will be styled as circle
                    'start' => $request->start_date->format('Y-m-d'),
                    'end' => $request->end_date->copy()->addDay()->format('Y-m-d'), // FullCalendar uses exclusive end dates
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'employee' => $request->employee->name,
                        'leave_type' => $request->leave_type,
                        'days' => $request->number_of_days,
                        'department' => $department ? $department->name : 'N/A',
                        'department_id' => $department ? $department->id : null,
                    ]
                ];
            })
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

    /**
     * Show a specific request details.
     */
    public function showRequest($id)
    {
        $leaveRequest = LeaveRequest::with([
            'employee',
            'employee.department',
            'departmentManager',
            'hrApprover'
        ])->findOrFail($id);

        // Get employee's balance for this leave type
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);

        return view('hr.show-request', compact('leaveRequest', 'balance'));
    }

    /**
     * Approve a leave request.
     */
    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Only approve if pending or manager approved
        if (!in_array($leaveRequest->status, ['pending', 'dept_manager_approved'])) {
            return back()->withErrors(['error' => 'This request cannot be approved.']);
        }

        // Check if employee has sufficient balance
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        
        if (!$balance || !$balance->hasSufficientBalance($leaveRequest->number_of_days)) {
            return back()->withErrors([
                'error' => 'Employee has insufficient balance for this request.'
            ]);
        }

        // Deduct balance
        $balance->deductDays($leaveRequest->number_of_days);

        // Update request
        $leaveRequest->update([
            'status' => 'hr_approved',
            'approved_by_hr_id' => $user->id,
            'hr_comment' => $request->input('comment'),
            'approved_by_hr_at' => now(),
        ]);

        return redirect()->route('hr.dashboard')
            ->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request.
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Only reject if pending or manager approved
        if (!in_array($leaveRequest->status, ['pending', 'dept_manager_approved'])) {
            return back()->withErrors(['error' => 'This request cannot be rejected.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update request
        $leaveRequest->update([
            'status' => 'hr_rejected',
            'hr_comment' => $validated['reason'],
            'rejected_at' => now(),
        ]);

        return redirect()->route('hr.dashboard')
            ->with('success', 'Leave request rejected.');
    }

    /**
     * Display leave policies.
     */
    public function policies()
    {
        $policies = Policy::orderBy('leave_type')->get();

        return view('hr.policies', compact('policies'));
    }

    /**
     * Update a leave policy.
     */
    public function updatePolicy(Request $request, $id)
    {
        $validated = $request->validate([
            'annual_entitlement' => 'required|integer|min:0|max:365',
        ]);

        $policy = Policy::findOrFail($id);
        $policy->update($validated);

        return redirect()->route('hr.policies')
            ->with('success', 'Policy updated successfully.');
    }
}
