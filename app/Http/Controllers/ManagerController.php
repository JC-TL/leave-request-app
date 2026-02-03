<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveRequestLog;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        // Leave type for team balance card (default: Vacation Leave)
        $leaveTypes = Policy::orderBy('leave_type')->pluck('leave_type');
        $selectedLeaveType = $request->input('leave_type', 'Vacation Leave');
        if (!$leaveTypes->contains($selectedLeaveType)) {
            $selectedLeaveType = $leaveTypes->first() ?? 'Vacation Leave';
        }

        $currentYear = now()->year;

        // get all employees in manager's department with their leave balances
        $teamMembers = User::where('department_id', $user->department_id)
            ->where('role', 'employee')
            ->with(['leaveBalances' => function ($query) use ($currentYear) {
                $query->where('year', $currentYear);
            }])
            ->get();

        $employeeIds = $teamMembers->pluck('id');
        
        // get pending requests from team members (preserve leave_type when paginating)
        $pendingRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

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

        return Inertia::render('Manager/Dashboard', [
            'pendingRequests' => $pendingRequests,
            'teamMembers' => $teamMembers,
            'pendingCount' => $pendingCount,
            'approvedThisMonth' => $approvedThisMonth,
            'teamCount' => $teamCount,
            'leaveTypes' => $leaveTypes,
            'selectedLeaveType' => $selectedLeaveType,
            'user' => $user,
        ]);
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

        return Inertia::render('Manager/ShowRequest', [
            'leaveRequest' => $leaveRequest,
            'balance' => $balance,
        ]);
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

        // Check that total reserved (pending + manager-approved) does not exceed balance - used
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        $pendingDays = $leaveRequest->employee->getPendingLeaveDays($leaveRequest->leave_type, $currentYear);
        if (!$balance || $balance->getAvailableBalance() < $pendingDays) {
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

        // Get old status before update
        $oldStatus = $leaveRequest->status;

        // Balance is only deducted when HR approves, so nothing to restore
        $leaveRequest->update([
            'status' => 'dept_manager_rejected',
            'dept_manager_comment' => $validated['reason'],
            'rejected_at' => now(),
        ]);

        // Log the rejection
        LeaveRequestLog::createLog(
            $leaveRequest->id,
            $user->id,
            'manager_rejected',
            $oldStatus,
            'dept_manager_rejected',
            $validated['reason']
        );

        return redirect()
            ->route('manager.dashboard')
            ->with('success', 'Leave request rejected.');
    }
}
