<?php

namespace App\Http\Controllers;

use App\Models\Request as LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerController extends Controller
{
    // Could add caching here for team members list if needed
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get all employees in manager's department
        $teamMembers = User::where('department_id', $user->department_id)
            ->where('role', 'employee')
            ->get();

        $employeeIds = $teamMembers->pluck('id');
        
        // Get pending requests from team members
        $pendingRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $pendingCount = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->count();
        
        // Count how many requests manager approved this month
        $approvedThisMonth = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->whereNotNull('approved_by_dept_at')
            ->where('approved_by_dept_manager_id', $user->id)
            ->whereMonth('approved_by_dept_at', now()->month)
            ->whereYear('approved_by_dept_at', now()->year)
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

    public function showRequest($id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with(['employee', 'employee.department'])
            ->findOrFail($id);

        // Security check - only view requests from own department
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);

        return view('manager.show-request', compact('leaveRequest', 'balance'));
    }

    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Make sure it's from their department
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        // Can only approve pending requests
        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'This request cannot be approved.']);
        }

        // Check balance
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        
        if (!$balance || !$balance->hasSufficientBalance($leaveRequest->number_of_days)) {
            return back()
                ->withErrors(['error' => 'Employee has insufficient balance for this request.']);
        }

        // Approve the request
        $leaveRequest->update([
            'status' => 'dept_manager_approved',
            'approved_by_dept_manager_id' => $user->id,
            'dept_manager_comment' => $request->input('comment'),
            'approved_by_dept_at' => now(),
        ]);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', 'Leave request approved successfully.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        // Department check
        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $leaveRequest->update([
            'status' => 'dept_manager_rejected',
            'dept_manager_comment' => $validated['reason'],
            'rejected_at' => now(),
        ]);

        return redirect()
            ->route('manager.dashboard')
            ->with('success', 'Leave request rejected.');
    }
}
