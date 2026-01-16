# Code Tour (annotated) — how *this repo* works

This is a guided walkthrough of the important files in your Laravel app.

The goal is to help you answer:

- “When I click X, what code runs?”
- “Where does data come from?”
- “Why is this check here?”
- “How does Laravel connect all these files?”

> Tip: Keep this file open and jump to the referenced files in your editor.

---

## 1) Route loading: one file pulls in the rest

**File:** `routes/web.php`

What it does: includes other PHP files so Laravel sees their routes.

```php
require __DIR__.'/auth.php';
require __DIR__.'/employee.php';
require __DIR__.'/manager.php';
require __DIR__.'/hr.php';
```

- **`require`**: PHP keyword; it literally copies the contents of those files into this file at runtime.
- **Why it matters**: it’s why routes are split by feature/role.

---

## 2) Public auth routes: login + contact admin

**File:** `routes/auth.php`

```php
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/contact-admin', [AuthController::class, 'showContactAdmin'])->name('contact-admin');
Route::post('/contact-admin', [AuthController::class, 'submitContact'])->name('contact-admin.store');
```

Line concepts:

- **`Route::get(...)` / `Route::post(...)`**: registers a route for an HTTP method.
- **`[AuthController::class, 'login']`**: “call method `login` on `AuthController`”.
- **`->name('...')`**: gives the route a name so Blade can do `route('login.store')`.
- **`->middleware('guest')`**: only allow unauthenticated users.

---

## 3) Role-gated route groups (Employee / Manager / HR)

**Files:** `routes/employee.php`, `routes/manager.php`, `routes/hr.php`

Example:

```php
Route::middleware(['auth', 'role:employee'])->group(function () {
    Route::get('/employee/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::post('/employee/request', [EmployeeController::class, 'storeRequest'])->name('employee.store-request');
});
```

Key concept:

- **Middleware runs before the controller**.
  - `auth` ensures the user is logged in
  - `role:employee` ensures the user’s `role` equals `employee`

---

## 4) Middleware: the “bouncer” at the door

**File:** `app/Http/Middleware/RoleMiddleware.php`

This is the logic behind `role:...` in route files.

```php
if (!Auth::check()) {
    return redirect()->route('login');
}

if (!in_array($user->role, $roles)) {
    abort(403, 'Unauthorized access.');
}
```

Line concepts:

- **`Auth::check()`**: “Is there a logged-in user in the session?”
- **`redirect()->route('login')`**: send browser to the named route.
- **`abort(403)`**: stop the request immediately with an HTTP 403 response.

Why this is good:

- Your controllers don’t need to re-check the role every time.

---

## 5) Authentication controller: sessions + redirect by role

**File:** `app/Http/Controllers/AuthController.php`

### Login

```php
$request->validate([
  'email' => ['required', 'email'],
  'password' => ['required', 'string'],
]);

if (!Auth::attempt($credentials, $request->boolean('remember'))) {
  throw ValidationException::withMessages([...]);
}

$request->session()->regenerate();

return match($user->role) {
  'employee' => redirect()->route('employee.dashboard'),
  'dept_manager' => redirect()->route('manager.dashboard'),
  'hr_admin', 'ceo' => redirect()->route('hr.dashboard'),
  default => redirect('/'),
};
```

Line concepts:

- **Validation**: turns bad input into errors automatically returned to the form.
- **`Auth::attempt()`**: checks credentials, and if OK, writes user ID into the session.
- **Session regeneration**: prevents “session fixation” security issues.
- **Role redirect**: chooses the correct dashboard for the role.

### Contact admin submit (ticket)

Your app currently validates and redirects with a success message.

If later you want real tickets, you’d typically:

- create a `tickets` table
- store the ticket row
- optionally send an email to admins

---

## 6) Models = tables + relationships + helper methods

### `Request` model

**File:** `app/Models/Request.php`

Core concepts:

- **`$fillable`**: allowed “mass assignment” fields.
- **`casts()`**: converts DB strings into typed objects:
  - dates become Carbon date objects
  - datetimes become Carbon datetime objects
- **relationships**:
  - `employee()` connects to `users.id` via `employee_id`
  - `departmentManager()` connects via `approved_by_dept_manager_id`
  - `hrApprover()` connects via `approved_by_hr_id`
- **scopes**: reusable query chunks like `Request::pending()`

### `Balance` model

**File:** `app/Models/Balance.php`

It implements small “domain logic” methods:

- `getAvailableBalance()`
- `hasSufficientBalance($days)`
- `deductDays($days)`

This keeps the “math” in one place so controllers stay readable.

---

## 7) Controllers: where “business rules” live

### Employee flow

**File:** `app/Http/Controllers/EmployeeController.php`

Key method: `storeRequest()`

What it enforces:

- dates are valid
- calculates working days (skips weekends)
- checks balance before creating the request
- creates a `requests` row with status `pending`

### Manager flow

**File:** `app/Http/Controllers/ManagerController.php`

What it enforces:

- manager can only act on employees in their department
- manager can only approve/reject `pending` requests
- on approve it sets:
  - `status = dept_manager_approved`
  - `approved_by_dept_manager_id`
  - `approved_by_dept_at`

### HR flow

**File:** `app/Http/Controllers/HRController.php`

What it enforces:

- HR can only approve/reject after manager approval
  - status must be `dept_manager_approved`
- on HR approval:
  - deducts balance
  - updates request status to `hr_approved`
- calendar events are derived from `hr_approved` requests only

---

## 8) Views: Blade templates + layout component

### Layout component

**File:** `resources/views/components/layout.blade.php`

Core concepts:

- **Blade component**: `<x-layout>` wraps pages with consistent HTML.
- **Slots**:
  - `$navigation` is an optional slot used by pages that have a navbar
  - `$slot` is the page body content
- **`@vite([...])`**: loads bundled CSS/JS
- **DaisyUI + FullCalendar** are included via CDN

### Blade directives

Common patterns you’ll see:

- `@if(session('success'))` → show success messages
- `@if($errors->any())` → show validation errors
- `@csrf` → CSRF protection in forms
- `{{ route('...') }}` → generate a URL by route name

