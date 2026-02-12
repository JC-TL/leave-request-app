<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Employee;
use App\Models\PendingRegistration;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class HRController extends Controller
{
    public function dashboard()
    {
        $pendingRequests = LeaveRequest::whereIn('status', ['pending', 'dept_manager_approved'])
            ->with(['employee', 'employee.department', 'departmentManager', 'leaveType'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $totalPending = LeaveRequest::whereIn('status', ['pending', 'dept_manager_approved'])->count();

        $totalApprovedThisMonth = LeaveRequest::approved()
            ->whereMonth('approved_by_hr_at', now()->month)
            ->whereYear('approved_by_hr_at', now()->year)
            ->count();

        $totalRejectedThisMonth = LeaveRequest::rejected()
            ->whereMonth('rejected_at', now()->month)
            ->whereYear('rejected_at', now()->year)
            ->count();

        $totalEmployees = Employee::whereIn('role', ['employee', 'dept_manager'])->count();

        $calendarEvents = LeaveRequest::where('status', 'hr_approved')
            ->with(['employee', 'employee.department'])
            ->get()
            ->map(function ($request) {
                $color = optional($request->employee->department)->color ?? '#3b82f6';

                return [
                    'title' => $request->employee->name,
                    'start' => $request->start_date->format('Y-m-d'),
                    'end' => $request->end_date->copy()->addDay()->format('Y-m-d'),
                    'color' => $color,
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'allDay' => true,
                ];
            })
            ->values()
            ->toArray();

        return Inertia::render('HR/Dashboard', [
            'pendingRequests' => $pendingRequests,
            'totalPending' => $totalPending,
            'totalApprovedThisMonth' => $totalApprovedThisMonth,
            'totalRejectedThisMonth' => $totalRejectedThisMonth,
            'totalEmployees' => $totalEmployees,
            'calendarEvents' => $calendarEvents,
        ]);
    }

    public function showRequest($id)
    {
        $leaveRequest = LeaveRequest::with([
            'employee',
            'employee.department',
            'departmentManager',
            'hrApprover',
            'leaveType',
        ])->findOrFail($id);

        $currentYear = now()->year;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type_id, $currentYear);

        return Inertia::render('HR/ShowRequest', [
            'leaveRequest' => $leaveRequest,
            'balance' => $balance,
        ]);
    }

    public function approveRequest(Request $request, $id)
    {
        $user = Auth::user();
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        if ($leaveRequest->status !== 'dept_manager_approved') {
            return back()
                ->withErrors(['error' => 'This request cannot be approved yet. It is still pending manager approval.']);
        }

        $currentYear = now()->year;
        $leaveType = $leaveRequest->leaveType;
        $balance = $leaveRequest->employee->getLeaveBalance($leaveRequest->leave_type_id, $currentYear);

        if ($leaveType && $leaveType->isCreditedOnApproval()) {
            if (!$balance) {
                $balance = LeaveBalance::create([
                    'emp_id' => $leaveRequest->emp_id,
                    'leave_type_id' => $leaveRequest->leave_type_id,
                    'year' => $currentYear,
                    'allocated_days' => $leaveRequest->number_of_days,
                    'used_days' => 0,
                ]);
            } else {
                $needed = $balance->used_days + $leaveRequest->number_of_days;
                if ($balance->allocated_days < $needed) {
                    $balance->update(['allocated_days' => $needed]);
                }
            }
        }

        if ($balance) {
            $balance->deductDays($leaveRequest->number_of_days);
        }

        $leaveRequest->update([
            'status' => 'hr_approved',
            'approved_by_hr_id' => $user->emp_id,
            'hr_comment' => $request->input('comment'),
            'approved_by_hr_at' => now(),
        ]);

        return redirect()
            ->route('hr.dashboard')
            ->with('success', 'Leave request approved successfully.');
    }

    public function rejectRequest(Request $request, $id)
    {
        $leaveRequest = LeaveRequest::with('employee')->findOrFail($id);

        if ($leaveRequest->status !== 'dept_manager_approved') {
            return back()
                ->withErrors(['error' => 'This request cannot be rejected yet. It is still pending manager approval.']);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
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

    public function policies()
    {
        $leaveTypes = LeaveType::orderBy('leave_type')->get();

        return Inertia::render('HR/Policies', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function updatePolicy(Request $request, $id)
    {
        $validated = $request->validate([
            'annual_entitlement' => [
                'required',
                'integer',
                'min:0',
                'max:365',
            ],
        ], [
            'annual_entitlement.required' => 'Annual entitlement is required.',
            'annual_entitlement.integer' => 'Annual entitlement must be a whole number.',
            'annual_entitlement.min' => 'Annual entitlement cannot be less than 0.',
            'annual_entitlement.max' => 'Annual entitlement cannot exceed 365 days.',
        ]);

        $leaveType = LeaveType::findOrFail($id);

        DB::transaction(function () use ($leaveType, $validated) {
            $leaveType->update(['annual_entitlement' => $validated['annual_entitlement']]);

            $currentYear = now()->year;
            LeaveBalance::where('leave_type_id', $leaveType->leave_type_id)
                ->where('year', $currentYear)
                ->get()
                ->each(function (LeaveBalance $balance) use ($validated) {
                    $newBalance = max($validated['annual_entitlement'], $balance->used_days);
                    $balance->update(['allocated_days' => $newBalance]);
                });
        });

        return redirect()
            ->route('hr.policies')
            ->with('success', 'Policy updated successfully. Current year leave balances have been updated.');
    }

    public function employees()
    {
        $employees = Employee::with(['department', 'manager'])
            ->orderBy('last_name')
            ->paginate(15);

        $pendingRegistrations = PendingRegistration::with('department')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('HR/Employees', [
            'employees' => $employees,
            'pendingRegistrations' => $pendingRegistrations,
        ]);
    }

    public function approveRegistration(Request $request, int $id)
    {
        $pending = PendingRegistration::with('department')->findOrFail($id);

        if (Employee::where('email', $pending->email)->exists()) {
            $pending->delete();
            return redirect()
                ->route('hr.employees')
                ->with('error', 'This email is already registered. The pending registration was removed.');
        }

        $employeeData = [
            'first_name' => $pending->first_name,
            'last_name' => $pending->last_name,
            'gender' => $pending->gender,
            'email' => $pending->email,
            'password' => $pending->password,
            'role' => $pending->role,
            'dept_id' => $pending->dept_id,
            'manager_id' => $pending->manager_id,
        ];

        $employee = Employee::create($employeeData);

        if ($employee->role === 'dept_manager' && $employee->dept_id) {
            Department::where('dept_id', $employee->dept_id)->update(['dept_manager_id' => $employee->emp_id]);
        }

        $currentYear = now()->year;
        $leaveTypes = LeaveType::all();
        foreach ($leaveTypes as $leaveType) {
            LeaveBalance::create([
                'emp_id' => $employee->emp_id,
                'leave_type_id' => $leaveType->leave_type_id,
                'year' => $currentYear,
                'allocated_days' => $leaveType->annual_entitlement,
                'used_days' => 0,
            ]);
        }

        $pending->delete();

        return redirect()
            ->route('hr.employees')
            ->with('success', sprintf('Registration approved. %s can now sign in.', $employee->name));
    }

    public function rejectRegistration(Request $request, int $id)
    {
        $pending = PendingRegistration::findOrFail($id);
        $pending->delete();

        return redirect()
            ->route('hr.employees')
            ->with('success', 'Registration rejected and removed.');
    }

    public function createEmployee()
    {
        $departments = Department::with('manager')->get();
        $roles = ['employee', 'dept_manager'];
        $employees = Employee::whereIn('role', ['employee', 'dept_manager'])
            ->with('department')
            ->orderBy('last_name')
            ->get();

        return Inertia::render('HR/CreateEmployee', [
            'departments' => $departments,
            'roles' => $roles,
            'employees' => $employees,
        ]);
    }

    public function storeEmployee(Request $request)
    {
        $request->merge(['manager_id' => $request->input('manager_id') ?: null]);

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'gender' => 'nullable|string|max:1',
            'email' => 'required|email|max:255|unique:employees,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:employee,dept_manager',
            'dept_id' => 'required|exists:departments,dept_id',
            'manager_id' => [
                'nullable',
                'exists:employees,emp_id',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$value) return;
                    $emp = Employee::find($value);
                    if ($request->input('role') === 'employee' && $emp->dept_id != $request->input('dept_id')) {
                        $fail('The selected manager must be in the same department as the employee.');
                    }
                },
            ],
        ]);

        if ($validated['role'] === 'dept_manager') {
            $validated['manager_id'] = null;
        } elseif (!empty($validated['manager_id'])) {
            Employee::where('emp_id', $validated['manager_id'])->update(['role' => 'dept_manager']);
        } else {
            $department = Department::find($validated['dept_id']);
            $validated['manager_id'] = $department?->dept_manager_id;
        }

        $validated['password'] = Hash::make($validated['password']);
        $employee = Employee::create($validated);

        if ($employee->role === 'dept_manager' && $employee->dept_id) {
            Department::where('dept_id', $employee->dept_id)->update(['dept_manager_id' => $employee->emp_id]);
        }

        $currentYear = now()->year;
        $leaveTypes = LeaveType::all();

        foreach ($leaveTypes as $leaveType) {
            LeaveBalance::create([
                'emp_id' => $employee->emp_id,
                'leave_type_id' => $leaveType->leave_type_id,
                'year' => $currentYear,
                'allocated_days' => $leaveType->annual_entitlement,
                'used_days' => 0,
            ]);
        }

        return redirect()
            ->route('hr.employees')
            ->with('success', sprintf('Employee %s created successfully with leave balances initialized.', $employee->name));
    }

    public function editEmployee($id)
    {
        $employee = Employee::with(['department', 'manager'])->findOrFail($id);
        $departments = Department::with('manager')->get();
        $roles = ['employee', 'dept_manager', 'hr_admin', 'ceo'];
        $employees = Employee::whereIn('role', ['employee', 'dept_manager'])
            ->with('department')
            ->where('emp_id', '!=', $id)
            ->orderBy('last_name')
            ->get();

        return Inertia::render('HR/EditEmployee', [
            'employee' => $employee,
            'departments' => $departments,
            'roles' => $roles,
            'employees' => $employees,
        ]);
    }

    public function updateEmployee(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);

        $deptId = $request->input('dept_id');
        $managerId = $request->input('manager_id');

        $request->merge([
            'dept_id' => ($deptId === '' || $deptId === null) ? null : (int) $deptId,
            'manager_id' => ($managerId === '' || $managerId === null) ? null : (int) $managerId,
        ]);

        $validated = $request->validate([
            'role' => 'required|in:employee,dept_manager,hr_admin,ceo',
            'dept_id' => 'nullable|exists:departments,dept_id',
            'manager_id' => [
                'nullable',
                'exists:employees,emp_id',
                function ($attribute, $value, $fail) use ($request, $id) {
                    if ($value === null) return;
                    if ((int) $value === (int) $id) {
                        $fail('An employee cannot be their own manager.');
                    }
                    $emp = Employee::find($value);
                    if ($request->input('role') === 'employee' && $emp->dept_id != $request->input('dept_id')) {
                        $fail('The selected manager must be in the same department as the employee.');
                    }
                },
            ],
        ]);

        if ($validated['role'] === 'dept_manager' || $validated['role'] === 'hr_admin' || $validated['role'] === 'ceo') {
            $validated['manager_id'] = null;
        } elseif (!empty($validated['manager_id'])) {
            Employee::where('emp_id', $validated['manager_id'])->update(['role' => 'dept_manager']);
        } else {
            $department = Department::find($validated['dept_id']);
            $validated['manager_id'] = $department?->dept_manager_id;
        }

        $oldDeptId = $employee->dept_id;
        $oldRole = $employee->role;
        $newDeptId = $validated['dept_id'];
        $newRole = $validated['role'];

        $employee->forceFill([
            'role' => $validated['role'],
            'dept_id' => $validated['dept_id'],
            'manager_id' => $validated['manager_id'],
        ])->save();

        if ($oldRole === 'dept_manager' && $oldDeptId && Department::where('dept_id', $oldDeptId)->where('dept_manager_id', $id)->exists()) {
            Department::where('dept_id', $oldDeptId)->update(['dept_manager_id' => null]);
        }
        if ($newRole === 'dept_manager' && $newDeptId) {
            Department::where('dept_id', $newDeptId)->update(['dept_manager_id' => (int) $id]);
        }

        return redirect()
            ->route('hr.employees')
            ->with('success', 'Employee updated successfully.');
    }

    public function destroyEmployee(Request $request, int $id)
    {
        if ((int) $id === (int) Auth::id()) {
            return redirect()
                ->route('hr.employees')
                ->with('error', 'You cannot delete your own account.');
        }

        $employee = Employee::findOrFail($id);

        Department::where('dept_manager_id', $id)->update(['dept_manager_id' => null]);
        Employee::where('manager_id', $id)->update(['manager_id' => null]);

        $employee->delete();

        return redirect()
            ->route('hr.employees')
            ->with('success', 'Employee deleted successfully.');
    }

    public function departments()
    {
        $departments = Department::with('manager')->orderBy('name')->get();

        return Inertia::render('HR/Departments', [
            'departments' => $departments,
        ]);
    }

    public function storeDepartment(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:30|unique:departments,name',
            'color' => 'required|string|max:30|regex:/^#[0-9a-fA-F]{6}$/',
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
            'color.regex' => 'Color must be a valid hex code (e.g. #3b82f6).',
        ]);

        Department::create([
            'name' => $validated['name'],
            'color' => $validated['color'],
            'dept_manager_id' => null,
        ]);

        return redirect()
            ->route('hr.departments')
            ->with('success', 'Department created successfully.');
    }

    public function updateDepartment(Request $request, int $id)
    {
        $department = Department::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:30', Rule::unique('departments', 'name')->ignore($department->dept_id, 'dept_id')],
            'color' => 'required|string|max:30|regex:/^#[0-9a-fA-F]{6}$/',
        ], [
            'name.required' => 'Department name is required.',
            'name.unique' => 'A department with this name already exists.',
            'color.regex' => 'Color must be a valid hex code (e.g. #3b82f6).',
        ]);

        $department->update([
            'name' => $validated['name'],
            'color' => $validated['color'],
        ]);

        return redirect()
            ->route('hr.departments')
            ->with('success', 'Department updated successfully.');
    }

    public function destroyDepartment(Request $request, int $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();

        return redirect()
            ->route('hr.departments')
            ->with('success', 'Department deleted successfully.');
    }
}
