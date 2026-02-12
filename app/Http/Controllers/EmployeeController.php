<?php

namespace App\Http\Controllers;

use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $currentYear = now()->year;

        $balances = $user->leaveBalances()
            ->with('leaveType')
            ->where('year', $currentYear)
            ->orderBy('leave_type_id')
            ->get()
            ->map(function ($balance) use ($user, $currentYear) {
                $pendingDays = $user->getPendingLeaveDays($balance->leave_type_id, $currentYear);
                $balance->available_days = max(0, $balance->getAvailableBalance() - $pendingDays);
                $balance->is_credited_on_approval = $balance->leaveType?->isCreditedOnApproval() ?? false;
                $balance->is_start_date_only = $balance->leaveType?->isStartDateOnly() ?? false;
                $balance->default_duration_days = $balance->leaveType?->default_duration_days;
                return $balance;
            });

        $requests = $user->leaveRequests()
            ->with('leaveType')
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
        $user = Auth::user();
        $leaveType = LeaveType::find($request->input('leave_type_id'));

        if (!$leaveType) {
            return back()->withErrors(['leave_type_id' => 'Invalid leave type.'])->withInput();
        }

        $rules = [
            'leave_type_id' => 'required|integer|exists:leave_types,leave_type_id',
            'start_date' => 'required|date|after_or_equal:today',
            'reason' => 'required|string|max:1000',
        ];

        if ($leaveType->isPaternity()) {
            if ($user->gender !== 'M') {
                return back()->withErrors(['leave_type_id' => 'Paternity Leave is only available for male employees.'])->withInput();
            }
            // Paternity: start date only, end_date calculated from default_duration_days
        } elseif ($leaveType->isMaternity()) {
            if ($user->gender !== 'F') {
                return back()->withErrors(['leave_type_id' => 'Maternity Leave is only available for female employees.'])->withInput();
            }
            if (!$leaveType->isStartDateOnly()) {
                $rules['end_date'] = 'required|date|after_or_equal:start_date';
            }
        } else {
            $rules['end_date'] = 'required|date|after_or_equal:start_date';
        }

        $validated = $request->validate($rules);

        $startDate = Carbon::parse($validated['start_date']);
        $endDate = null;
        $numberOfDays = 0;

        if ($leaveType->isStartDateOnly()) {
            $endDate = $startDate->copy()->addWeekdays($leaveType->default_duration_days - 1);
            $numberOfDays = (int) $leaveType->default_duration_days;
        } else {
            $endDate = Carbon::parse($validated['end_date']);
            $numberOfDays = $this->calculateWorkingDays($startDate, $endDate);
        }

        if (!$leaveType->isCreditedOnApproval()) {
            $balance = $user->getLeaveBalance((int) $validated['leave_type_id'], now()->year);
            if (!$balance) {
                return back()->withErrors(['leave_type_id' => 'No leave balance found for this leave type.'])->withInput();
            }
            $pendingDays = $user->getPendingLeaveDays((int) $validated['leave_type_id'], now()->year);
            $available = max(0, $balance->getAvailableBalance() - $pendingDays);
            if ($available < $numberOfDays) {
                return back()->withErrors(['leave_type_id' => "Insufficient balance. You have {$available} days available."])->withInput();
            }
        }

        LeaveRequest::create([
            'emp_id' => $user->emp_id,
            'leave_type_id' => $validated['leave_type_id'],
            'start_date' => $validated['start_date'],
            'end_date' => $endDate->format('Y-m-d'),
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

        if ($leaveRequest->emp_id !== $user->emp_id) {
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
