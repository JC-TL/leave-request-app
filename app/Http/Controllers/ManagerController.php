<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ManagerController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::user();

        $leaveTypes = LeaveType::orderBy('leave_type')->pluck('leave_type', 'leave_type_id');
        $selectedLeaveTypeId = $request->input('leave_type_id', $leaveTypes->keys()->first());
        if (!$leaveTypes->has($selectedLeaveTypeId)) {
            $selectedLeaveTypeId = $leaveTypes->keys()->first();
        }

        $currentYear = now()->year;

        $teamMembers = Employee::where('dept_id', $user->dept_id)
            ->where('role', 'employee')
            ->with(['leaveBalances' => function ($query) use ($currentYear) {
                $query->where('year', $currentYear)->with('leaveType');
            }])
            ->get();

        $employeeIds = $teamMembers->pluck('emp_id');

        $pendingRequests = LeaveRequest::whereIn('emp_id', $employeeIds)
            ->pending()
            ->with(['employee', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $pendingCount = LeaveRequest::whereIn('emp_id', $employeeIds)
            ->pending()
            ->count();

        $approvedThisMonth = LeaveRequest::whereIn('emp_id', $employeeIds)
            ->whereNotNull('approved_by_dept_at')
            ->where('approved_by_dept_manager_id', $user->emp_id)
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
            'selectedLeaveTypeId' => $selectedLeaveTypeId,
            'user' => $user,
        ]);
    }

    public function showRequest($id)
    {
        $user = Auth::user();

        $leaveRequest = LeaveRequest::with(['employee', 'employee.department', 'leaveType'])
            ->findOrFail($id);

        if ($leaveRequest->employee->dept_id !== $user->dept_id) {
            abort(403, 'Unauthorized access.');
        }

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type_id, $currentYear);

        return Inertia::render('Manager/ShowRequest', [
            'leaveRequest' => $leaveRequest,
            'balance' => $balance,
        ]);
    }

    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with(['employee', 'leaveType'])->findOrFail($id);

        if ($leaveRequest->employee->dept_id !== $user->dept_id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'This request cannot be approved.']);
        }

        $leaveType = $leaveRequest->leaveType;
        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type_id, $currentYear);
        $pendingDays = $leaveRequest->employee->getPendingLeaveDays($leaveRequest->leave_type_id, $currentYear);

        if (!$leaveType?->isCreditedOnApproval() && (!$balance || $balance->getAvailableBalance() < $pendingDays)) {
            return back()
                ->withErrors(['error' => 'Employee has insufficient balance for this request.']);
        }

        $leaveRequest->update([
            'status' => 'dept_manager_approved',
            'approved_by_dept_manager_id' => $user->emp_id,
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

        if ($leaveRequest->employee->dept_id !== $user->dept_id) {
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
