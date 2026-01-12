<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Request as LeaveRequest;
use App\Models\Balance;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    /**
     * Display the employee dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $currentYear = now()->year;

        // Get all leave balances for current year
        $balances = $user->leaveBalances()
            ->where('year', $currentYear)
            ->orderBy('leave_type')
            ->get();

        // Get all leave requests, newest first
        $requests = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.dashboard', compact('balances', 'requests', 'user'));
    }

    /**
     * Store a new leave request.
     */
    public function storeRequest(Request $request)
    {
        $user = Auth::user();
        $currentYear = now()->year;

        $validated = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:1000',
        ]);

        // Calculate number of days (excluding weekends)
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $numberOfDays = $this->calculateWorkingDays($startDate, $endDate);

        // Check if user has sufficient balance
        $balance = $user->getLeaveBalance($validated['leave_type'], $currentYear);
        
        if (!$balance) {
            return back()->withErrors(['leave_type' => 'Leave balance not found for this leave type.'])->withInput();
        }

        if (!$balance->hasSufficientBalance($numberOfDays)) {
            return back()->withErrors([
                'leave_type' => 'Insufficient balance. Available: ' . $balance->getAvailableBalance() . ' days.'
            ])->withInput();
        }

        // Create the request
        $leaveRequest = LeaveRequest::create([
            'employee_id' => $user->id,
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'reason' => $validated['reason'],
            'number_of_days' => $numberOfDays,
            'status' => 'pending',
        ]);

        return redirect()->route('employee.dashboard')
            ->with('success', 'Leave request submitted successfully.');
    }

    /**
     * Cancel a pending leave request.
     */
    public function cancelRequest($id)
    {
        $user = Auth::user();
        
        $leaveRequest = LeaveRequest::findOrFail($id);

        // Verify ownership
        if ($leaveRequest->employee_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation if pending
        if (!$leaveRequest->isPending()) {
            return back()->withErrors(['error' => 'Only pending requests can be cancelled.']);
        }

        $leaveRequest->delete();

        return redirect()->route('employee.dashboard')
            ->with('success', 'Leave request cancelled successfully.');
    }

    /**
     * Calculate working days between two dates (excluding weekends).
     */
    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            // Count only weekdays (Monday = 1, Sunday = 7)
            if ($current->dayOfWeek !== Carbon::SATURDAY && $current->dayOfWeek !== Carbon::SUNDAY) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }
}
