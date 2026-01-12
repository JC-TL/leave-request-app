<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as LeaveRequest;
use App\Models\User;

class ManagerController extends Controller
{
    /**
     * Display the manager dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get all employees in the manager's department
        $teamMembers = User::where('department_id', $user->department_id)
            ->where('role', 'employee')
            ->get();

        // Get pending requests from team members
        $employeeIds = $teamMembers->pluck('id');
        $pendingRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get stats
        $pendingCount = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->count();
        
        $approvedThisMonth = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->approved()
            ->whereMonth('approved_by_hr_at', now()->month)
            ->whereYear('approved_by_hr_at', now()->year)
            ->count();

        $teamCount = $teamMembers->count();

        return view('manager.dashboard', compact(
            'pendingRequests',
            'teamMembers',
            'pendingCount',
            'approvedThisMonth',
            'teamCount',
            'user'
        ));
    }

    /**
     * Show a specific request details.
     */
    public function showRequest($id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with(['employee', 'employee.department'])
            ->findOrFail($id);

        // Verify the request is from the manager's team
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        // Get employee's balance for this leave type
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);

        return view('manager.show-request', compact('leaveRequest', 'balance'));
    }

    /**
     * Approve a leave request.
     */
    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Verify the request is from the manager's team
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        // Only approve if pending
        if (!$leaveRequest->isPending()) {
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

        // Update request
        $leaveRequest->update([
            'status' => 'dept_manager_approved',
            'approved_by_dept_manager_id' => $user->id,
            'dept_manager_comment' => $request->input('comment'),
            'approved_by_dept_at' => now(),
        ]);

        return redirect()->route('manager.dashboard')
            ->with('success', 'Leave request approved successfully.');
    }

    /**
     * Reject a leave request.
     */
    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Verify the request is from the manager's team
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        // Only reject if pending
        if (!$leaveRequest->isPending()) {
            return back()->withErrors(['error' => 'This request cannot be rejected.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        // Update request
        $leaveRequest->update([
            'status' => 'dept_manager_rejected',
            'dept_manager_comment' => $validated['reason'],
            'rejected_at' => now(),
        ]);

        return redirect()->route('manager.dashboard')
            ->with('success', 'Leave request rejected.');
    }
}
