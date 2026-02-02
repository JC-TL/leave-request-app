# Leave Request App — Role Study Guide (Employee / Manager / HR)

This guide is written for *this repo* and points you to the exact code paths for each role:

- **Employee**: create + cancel leave requests; view balances + own request history
- **Department Manager** (`dept_manager`): view team requests; approve/reject (first stage)
- **HR Admin** (`hr_admin`): view requests needing HR action; approve/reject (final stage); manage policies and employees

---

## 1) Quick mental model: what happens when someone requests leave?

### Entities (tables/models)
- **User** (`users` table, `app/Models/User.php`)
  - Has a `role` enum: `employee`, `dept_manager`, `hr_admin`, `ceo`
  - Has `department_id` and (for employees) `manager_id`
- **Leave Request** (`requests` table, `app/Models/Request.php`)
  - Represents one leave request for one employee
  - Has a `status` state machine (see below)
- **Balance** (`balances` table, `app/Models/Balance.php`)
  - Stores how many days are available per leave type per year
- **Policy** (`policies` table, `app/Models/Policy.php`)
  - Stores annual entitlement per leave type (used to initialize balances)
- **Department** (`departments` table, `app/Models/Department.php`)
  - Departments have a color used in the HR calendar, and a dept manager id

### Status flow (the “state machine”)
The `requests.status` column moves like this:

1. `pending` (created by employee)
2. `dept_manager_approved` OR `dept_manager_rejected` (manager decision)
3. If approved by manager: `hr_approved` OR `hr_rejected` (HR decision)

Important detail:
- **Only when HR approves** do we deduct leave days from the employee’s `Balance`.

---

## 2) How role-based access works (routes + middleware)

### (A) Route files per role
`routes/web.php` loads these files:
- `routes/auth.php` (login/logout, contact admin)
- `routes/employee.php`
- `routes/manager.php`
- `routes/hr.php`

### (B) The `role:...` middleware alias
In Laravel 11 style, the alias is registered in `bootstrap/app.php`:

```php
$middleware->alias([
  'role' => \App\Http\Middleware\RoleMiddleware::class,
]);
```

So `Route::middleware(['auth', 'role:employee'])` means:
- `auth` must pass (user is logged in)
- `role:employee` must pass (user role must match)

### (C) Role checking logic
`app/Http/Middleware/RoleMiddleware.php`:
- If not logged in → redirect to `login`
- If role not allowed → `abort(403)`

### (D) Where the role value lives
`database/migrations/0001_01_01_000000_create_users_table.php` defines:
```php
$table->enum('role', ['employee', 'dept_manager', 'hr_admin', 'ceo']);
```

And `app/Models/User.php` has helpers:
- `isEmployee()`
- `isDeptManager()`
- `isHRAdmin()`
- `isCEO()`

---

## 3) Employee role — backend flow (routes → controller → model → view)

### Employee routes (`routes/employee.php`)
- `GET /employee/dashboard` → `EmployeeController@dashboard`
- `POST /employee/request` → `EmployeeController@storeRequest`
- `PATCH /request/{id}/cancel` → `EmployeeController@cancelRequest`

All of them require: `['auth', 'role:employee']`.

### (A) Employee dashboard: balances + request history
**Controller**: `app/Http/Controllers/EmployeeController.php`, method `dashboard()`

Read it like this:
- Fetch current user (`Auth::user()`)
- Determine `currentYear`
- Load balances: `$user->leaveBalances()->where('year', $currentYear)->get()`
- Load requests: `$user->leaveRequests()->orderBy(...)->paginate(10)`
- Return the Blade view: `view('employee.dashboard', compact(...))`

**View**: `resources/views/employee/dashboard.blade.php`

Read it like this:
- Uses `<x-layout>` (global layout component)
- Renders **balances** with `<x-balance-card :balance="$balance" />`
- Shows the “Submit Leave Request” form only if any balance has available days
- Shows “My Leave Requests” table with `<x-status-badge :status="$request->status" />`
- If a request is still pending → show “Cancel” button which hits `route('request.cancel')`

### (B) Submitting a leave request
**Route**: `POST /employee/request` (`employee.store-request`)

**Controller**: `EmployeeController@storeRequest(Request $request)`

Read it line-by-line:
1. `$request->validate(...)` ensures leave type + dates + reason are present and sane.
2. `Carbon::parse(...)` converts strings into date objects.
3. `calculateWorkingDays(...)` counts days excluding weekends.
4. `$user->getLeaveBalance($leave_type, $year)` finds the matching `Balance` record.
5. `hasSufficientBalance($days)` prevents negative balances.
6. `LeaveRequest::create([... 'status' => 'pending'])` inserts into `requests`.
7. Redirect back to dashboard with a success flash message.

### (C) Cancelling a pending request
**Route**: `PATCH /request/{id}/cancel` (`request.cancel`)

**Controller**: `EmployeeController@cancelRequest($id)`

Critical checks:
- Employee can only cancel their own request: `employee_id === $user->id`
- Only pending requests can be cancelled: `$leaveRequest->isPending()`
- Then it deletes the request row

Note: cancelling does **not** refund balance because balance is only deducted at HR approval.

---

## 4) Manager role — backend flow

### Manager routes (`routes/manager.php`)
- `GET /manager/dashboard` → `ManagerController@dashboard`
- `GET /manager/request/{id}` → `ManagerController@showRequest`
- `PATCH /manager/request/{id}/approve` → `ManagerController@approveRequest`
- `PATCH /manager/request/{id}/reject` → `ManagerController@rejectRequest`

All require: `['auth', 'role:dept_manager']`.

### (A) Manager dashboard: “My Team” + pending approvals
**Controller**: `app/Http/Controllers/ManagerController.php`, method `dashboard()`

Key idea:
- It finds team members by matching the manager’s `department_id` and role `employee`.
- It loads pending requests for those employee ids.
- It computes simple dashboard stats (pending count, approved this month, team count).

**View**: `resources/views/manager/dashboard.blade.php`
- Shows stats cards
- Shows a table of pending requests; each row links to `manager.show-request`
- Shows a team member table and a quick “Vacation Leave” balance lookup

### (B) Request details page (manager)
**Controller**: `ManagerController@showRequest($id)`

Security check:
- Manager can only view requests from their department:
  - `$leaveRequest->employee->department_id !== $user->department_id` → 403

It also loads the employee’s current leave balance for the request’s leave type.

**View**: `resources/views/manager/show-request.blade.php`
- Shows employee + request details and current balance.
- If request is pending, renders:
  - Approve form (optional comment) → `manager.approve-request`
  - Reject form (required reason) → `manager.reject-request`

### (C) Approve (manager)
**Controller**: `ManagerController@approveRequest(Request $request, $id)`

Steps:
1. Load request + employee.
2. Department check (same as show).
3. Must be pending: `isPending()`.
4. Must have enough balance **at time of approval** (defense-in-depth).
5. Update request:
   - `status` becomes `dept_manager_approved`
   - Track manager id + comment + timestamp

### (D) Reject (manager)
**Controller**: `ManagerController@rejectRequest(Request $request, $id)`

Steps:
1. Load request + employee.
2. Department check
3. Must be pending
4. Validate `reason`
5. Update request:
   - `status` becomes `dept_manager_rejected`
   - Store rejection reason in `dept_manager_comment`
   - Set `rejected_at`

---

## 5) HR Admin role — backend flow

### HR routes (`routes/hr.php`)
- `GET /hr/dashboard` → `HRController@dashboard`
- `GET /hr/request/{id}` → `HRController@showRequest`
- `PATCH /hr/request/{id}/approve` → `HRController@approveRequest`
- `PATCH /hr/request/{id}/reject` → `HRController@rejectRequest`
- `GET /hr/policies` → `HRController@policies`
- `PATCH /hr/policy/{id}` → `HRController@updatePolicy`
- `GET /hr/employees` → `HRController@employees`
- `GET /hr/employees/create` → `HRController@createEmployee`
- `POST /hr/employees` → `HRController@storeEmployee`

All require: `['auth', 'role:hr_admin']`.

### (A) HR dashboard
**Controller**: `app/Http/Controllers/HRController.php`, method `dashboard()`

Three big things happen here:
- Load requests that need HR attention:
  - status in `['pending', 'dept_manager_approved']`
- Compute summary stats (pending total, approved/rejected this month, employee count)
- Build calendar events for **approved** (`hr_approved`) requests, colored by department

**View**: `resources/views/hr/dashboard.blade.php`
- Renders stats + a pending requests table.
- Builds a calendar UI with inline JS and a FullCalendar CDN include from the layout.
- It uses `$calendarEvents` to paint per-day “leave badges”.

### (B) Request details page (HR)
**Controller**: `HRController@showRequest($id)`
- Loads request + employee + department + departmentManager + hrApprover
- Loads employee balance for leave type

**View**: `resources/views/hr/show-request.blade.php`
- Shows details + an “Approval Timeline”
- Only shows HR actions if status is `dept_manager_approved`

### (C) Approve (HR) — *final approval + balance deduction*
**Controller**: `HRController@approveRequest(Request $request, $id)`

Read it line-by-line:
1. Only allowed if status is `dept_manager_approved`
2. Check employee balance again
3. **Deduct days**: `$balance->deductDays($leaveRequest->number_of_days)`
4. Update request:
   - `status` becomes `hr_approved`
   - Track HR approver id + optional comment + timestamp

### (D) Reject (HR)
**Controller**: `HRController@rejectRequest(Request $request, $id)`
- Also requires status `dept_manager_approved`
- Validates rejection reason
- Updates status to `hr_rejected`, saves `hr_comment`, sets `rejected_at`

### (E) Policies (HR)
**Controller**:
- `policies()` loads all policies
- `updatePolicy()` validates `annual_entitlement`, then updates a policy record

**View**: `resources/views/hr/policies.blade.php`
- Table of policies
- Each row opens a modal `<dialog>` with a PATCH form to update the entitlement

### (F) Employees (HR)
**Controller**:
- `employees()` lists employees + dept managers with department + manager relationship
- `createEmployee()` loads departments, managers, and allowed roles
- `storeEmployee()`:
  - Validates input (including confirmed password)
  - Clears `manager_id` if the new user is a manager
  - Hashes password
  - Creates leave balances for the current year based on all policies

**Views**:
- `resources/views/hr/employees.blade.php` (list)
- `resources/views/hr/create-employee.blade.php` (create form + tiny JS to show manager field only for employees)

---

## 6) Frontend “view components” used everywhere

### Layout
`resources/views/components/layout.blade.php`
- Includes fonts + DaisyUI + Vite assets
- Renders optional `navigation` slot (each role page provides its own navbar)
- Renders the main page slot

### Status badge
`resources/views/components/status-badge.blade.php`
- Maps each `requests.status` string to a badge color + label

### Balance card
`resources/views/components/balance-card.blade.php`
- Calculates remaining percentage and picks color bands:
  - Grey (0)
  - Green (>50%)
  - Yellow (10–50%)
  - Red (<10%)

---

## 7) One important “gotcha” to notice

`app/Http/Controllers/AuthController.php` redirects `ceo` to `hr.dashboard`, but `routes/hr.php` is locked to `role:hr_admin`.

So as-written, a CEO user will be redirected to a page they cannot access (403).

If your intent is “CEO can access HR dashboard”, you’d change `routes/hr.php` to allow `hr_admin,ceo`:
```php
Route::middleware(['auth', 'role:hr_admin,ceo'])->group(function () {
  // ...
});
```

---

## 8) How to study this app efficiently (recommended reading order)

1. `routes/web.php` → see role route files being loaded
2. `bootstrap/app.php` + `app/Http/Middleware/RoleMiddleware.php` → understand access control
3. `app/Models/User.php` + `app/Models/Request.php` + `app/Models/Balance.php`
4. Follow each role:
   - Employee: `EmployeeController` → `resources/views/employee/dashboard.blade.php`
   - Manager: `ManagerController` → `resources/views/manager/*`
   - HR: `HRController` → `resources/views/hr/*`

