<?php

namespace App\Http\Controllers;

use App\Models\Policy;
use App\Models\Request as LeaveRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HRController extends Controller
{
    public function dashboard(): View
    {
        $pendingRequests = LeaveRequest::whereIn('status', ['dept_manager_approved', 'pending'])
            ->with(['employee', 'employee.department', 'departmentManager'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

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

        $calendarEvents = LeaveRequest::where('status', 'hr_approved')
            ->with(['employee', 'employee.department'])
            ->get()
            ->map(function ($request) {
                $department = $request->employee->department;
                $color = $department?->color ?? '#6b7280';
                
                return [
                    'title' => '',
                    'start' => $request->start_date->format('Y-m-d'),
                    'end' => $request->end_date->copy()->addDay()->format('Y-m-d'),
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'extendedProps' => [
                        'employee' => $request->employee->name,
                        'leave_type' => $request->leave_type,
                        'days' => $request->number_of_days,
                        'department' => $department?->name ?? 'N/A',
                        'department_id' => $department?->id,
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

    public function showRequest(int $id): View
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

    public function approveRequest(Request $request, int $id): RedirectResponse
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        if (!in_array($leaveRequest->status, ['pending', 'dept_manager_approved'])) {
            return back()
                ->withErrors(['error' => 'This request cannot be approved.']);
        }

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type, $currentYear);
        
        if (!$balance || !$balance->hasSufficientBalance($leaveRequest->number_of_days)) {
            return back()
                ->withErrors(['error' => 'Employee has insufficient balance for this request.']);
        }

        $balance->deductDays($leaveRequest->number_of_days);

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

    public function rejectRequest(Request $request, int $id): RedirectResponse
    {
        $leaveRequest = LeaveRequest::findOrFail($id);

        if (!in_array($leaveRequest->status, ['pending', 'dept_manager_approved'])) {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected.']);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
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

    public function policies(): View
    {
        $policies = Policy::orderBy('leave_type')->get();

        return view('hr.policies', compact('policies'));
    }

    public function updatePolicy(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'annual_entitlement' => ['required', 'integer', 'min:0', 'max:365'],
        ]);

        $policy = Policy::findOrFail($id);
        $policy->update($validated);

        return redirect()
            ->route('hr.policies')
            ->with('success', 'Policy updated successfully.');
    }
}
