<?php

namespace App\Http\Controllers;

use App\Models\Balance;
use App\Models\Request as LeaveRequest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function dashboard(): View
    {
        $user = Auth::user();
        $currentYear = now()->year;

        $balances = $user->leaveBalances()
            ->where('year', $currentYear)
            ->orderBy('leave_type')
            ->get();

        $requests = $user->leaveRequests()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('employee.dashboard', compact('balances', 'requests', 'user'));
    }

    public function storeRequest(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'leave_type' => ['required', 'string'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $user = Auth::user();
        $currentYear = now()->year;
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $numberOfDays = $this->calculateWorkingDays($startDate, $endDate);

        $balance = $user->getLeaveBalance($validated['leave_type'], $currentYear);
        
        if (!$balance) {
            return back()
                ->withErrors(['leave_type' => 'Leave balance not found for this leave type.'])
                ->withInput();
        }

        if (!$balance->hasSufficientBalance($numberOfDays)) {
            return back()
                ->withErrors([
                    'leave_type' => sprintf(
                        'Insufficient balance. Available: %.1f days.',
                        $balance->getAvailableBalance()
                    )
                ])
                ->withInput();
        }

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

    public function cancelRequest(int $id): RedirectResponse
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::findOrFail($id);

        if ($leaveRequest->employee_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$leaveRequest->isPending()) {
            return back()
                ->withErrors(['error' => 'Only pending requests can be cancelled.']);
        }

        $leaveRequest->delete();

        return redirect()
            ->route('employee.dashboard')
            ->with('success', 'Leave request cancelled successfully.');
    }

    private function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
    {
        $days = 0;
        $current = $startDate->copy();

        while ($current->lte($endDate)) {
            if (!in_array($current->dayOfWeek, [Carbon::SATURDAY, Carbon::SUNDAY])) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }
}
