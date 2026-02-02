<?php

namespace App\Http\Controllers;


use App\Models\LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        // Get current year for filtering balances
        $currentYear = now()->year;

        // Fetch user's leave balances for this year with available days (after reserved pending requests)
        $balances = $user->leaveBalances()
            ->where('year', $currentYear)
            ->orderBy('leave_type')
            ->get()
            ->map(function ($balance) use ($user, $currentYear) {
                $pendingDays = $user->getPendingLeaveDays($balance->leave_type, $currentYear);
                $balance->available_days = max(0, $balance->getAvailableBalance() - $pendingDays);
                return $balance;
            });

        $requests = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Employee/Dashboard', [
            'balances' => $balances,
            'requests' => $requests,
            'user' => $user,
        ]);
    }

    public function storeRequest(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        $user = Auth::user();
        $currentYear = now()->year;
        
        // Parse dates and calculate working days (excludes weekends)
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $numberOfDays = $this->calculateWorkingDays($startDate, $endDate);

        // Check if user has balance for this leave type
        $balance = $user->getLeaveBalance($validated['leave_type'], $currentYear);
        
        if (!$balance) {
            return back()
                ->withErrors(['leave_type' => 'No leave balance found for this leave type.'])
                ->withInput();
        }

        // Available = balance minus used minus days already reserved by pending/manager-approved requests
        $pendingDays = $user->getPendingLeaveDays($validated['leave_type'], $currentYear);
        $available = max(0, $balance->getAvailableBalance() - $pendingDays);

        if ($available < $numberOfDays) {
            return back()
                ->withErrors([
                    'leave_type' => "Insufficient balance. You have {$available} days available (after pending requests)."
                ])
                ->withInput();
        }

        // Create the leave request (balance is only deducted when HR approves)
        LeaveRequest::create([
            'employee_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'number_of_days' => $numberOfDays,
            'status' => 'pending',
        ]);

        return redirect()
            ->route('employee.dashboard')
            ->with('success', 'Leave request submitted successfully.');
    }

    public function cancelRequest($id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Security check - make sure they can only cancel their own requests
        if ($leaveRequest->employee_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Can only cancel if still pending
        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'Only pending requests can be cancelled.']);
        }

        // Balance was never deducted (only deducted when HR approves), so nothing to restore
        $leaveRequest->delete();

        return redirect()
            ->route('employee.dashboard')
            ->with('success', 'Leave request cancelled successfully.');
    }

    // Calculate working days excluding weekends
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate)
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Skip weekends
            if (!in_array($current->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }
}
