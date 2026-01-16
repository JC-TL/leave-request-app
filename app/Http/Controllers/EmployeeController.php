<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Request as LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        // Get current year for filtering balances
        $currentYear = now()->year;

        // Fetch user's leave balances for this year
        $balances = $user->leaveBalances()
            ->where('year', $currentYear)
            ->orderBy('leave_type')
            ->get();

        // Get paginated requests, newest first
        $requests = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.dashboard', compact('balances', 'requests', 'user'));
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

        // Make sure they have enough days
        if (!$balance->hasSufficientBalance($numberOfDays)) {
            $available = $balance->getAvailableBalance();
            return back()
                ->withErrors([
                    'leave_type' => "Insufficient balance. You only have {$available} days available."
                ])
                ->withInput();
        }

        // Create the leave request
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
