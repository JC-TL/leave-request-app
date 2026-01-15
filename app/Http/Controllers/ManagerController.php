<?php

namespace App\Http\Controllers;

use App\Models\Request as LeaveRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        
        $teamMembers = User::where('department_id', $user->department_id)
            ->where('role', 'employee')
            ->get();

        $employeeIds = $teamMembers->pluck('id');
        $pendingRequests = LeaveRequest::whereIn('employee_id', $employeeIds)
            ->pending()
            ->with('employee')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

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

    public function showRequest(int $id): View
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::with(['employee', 'employee.department'])
            ->findOrFail($id);

        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);

        return view('manager.show-request', compact('leaveRequest', 'balance'));
    }

    public function approveRequest(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'This request cannot be approved.']);
        }

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        
        if (!$balance || !$balance->hasSufficientBalance($leaveRequest->number_of_days)) {
            return back()
                ->withErrors(['error' => 'Employee has insufficient balance for this request.']);
        }

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

    public function rejectRequest(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        if ($leaveRequest->employee->department_id !== $user->department_id) {
            abort(403, 'Unauthorized access.');
        }

        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
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
