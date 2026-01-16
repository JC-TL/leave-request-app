# Build this app from scratch in Laravel (beginner steps)

This is a “how to rebuild it” guide. It mirrors the architecture in this repo.

> You don’t need to do all of this at once. Build the smallest version first (login + one dashboard), then grow.

---

## 0) What you are building (requirements)

### Roles

- `employee`
- `dept_manager`
- `hr_admin`
- (optional) `ceo`

### Leave request lifecycle

- employee submits → `pending`
- manager approves → `dept_manager_approved`
- manager rejects → `dept_manager_rejected`
- HR approves → `hr_approved` (deduct balance)
- HR rejects → `hr_rejected`

---

## 1) Create a fresh Laravel project

Typical setup:

```bash
composer create-project laravel/laravel leave-request-app
cd leave-request-app
php artisan key:generate
```

Set up your `.env` for a database (SQLite or MySQL).

---

## 2) Create database tables (migrations)

You’ll need tables:

- `users` (with `role`, `department_id`, `manager_id`)
- `departments` (with a color + manager reference)
- `policies` (leave types and entitlements)
- `balances` (per user/per year/per leave_type)
- `requests` (leave requests + approval fields)

Key beginner idea:

- A “Model” is your PHP class.
- A “Migration” is the versioned blueprint of the DB table behind that model.

---

## 3) Create models + relationships

For each model, define:

- `$fillable` for mass assignment
- relationships (`belongsTo`, `hasMany`)
- casts for dates/datetimes

Example relationship thinking:

- A `Request` belongs to an `employee` (a `User`)
- A `User` belongs to a `Department`
- A `Department` has many employees

---

## 4) Authentication (login/logout) + redirect by role

Implement:

- `GET /` → login form
- `POST /login` → validate credentials + start session
- `POST /logout` → end session

Then redirect users based on role:

- employee → `/employee/dashboard`
- manager → `/manager/dashboard`
- HR → `/hr/dashboard`

---

## 5) Authorization: role middleware

Create a middleware similar to this repo’s `RoleMiddleware`:

- if not logged in → redirect to login
- if logged in but role not allowed → abort 403

Use it in route groups:

- `['auth','role:employee']`
- `['auth','role:dept_manager']`
- `['auth','role:hr_admin']`

---

## 6) Controllers: implement business actions

Create:

- `EmployeeController`
  - `dashboard()` → show balances + own requests
  - `storeRequest()` → validate, calculate working days, check balance, create request
  - `cancelRequest()` → allow cancel only while pending

- `ManagerController`
  - `dashboard()` → show team + pending requests
  - `approveRequest()` / `rejectRequest()` → only for requests in their department and only when pending

- `HRController`
  - `dashboard()` → show pipeline + counts + calendar
  - `approveRequest()` / `rejectRequest()` → only after manager approval; deduct balance on final approval
  - `policies()` / `updatePolicy()` → manage entitlements
  - `employees()` / `createEmployee()` / `storeEmployee()` → add employees and initialize balances from policies

Beginner rule of thumb:

- Controllers should feel like “glue code” + business rules.
- Reusable math/logic goes into models (like `Balance::deductDays()`).

---

## 7) Views: Blade + layout component

Make:

- a shared layout: `resources/views/components/layout.blade.php`
- separate pages per role:
  - `resources/views/employee/dashboard.blade.php`
  - `resources/views/manager/dashboard.blade.php`
  - `resources/views/hr/dashboard.blade.php`

Use a component slot to inject navigation:

- pages set a `<x-slot name="navigation">...</x-slot>`
- layout prints it if present

---

## 8) Frontend UI stack

This repo uses:

- **Tailwind** + **DaisyUI** components
- **Vite** to bundle `resources/css/app.css` + `resources/js/app.js`
- **FullCalendar** for the HR calendar view

You can swap the UI framework later; the backend architecture stays the same.

---

## 9) Calendar: show only final approvals

Backend idea:

- Query only requests with `status = hr_approved`
- Convert each into an event object with:
  - `title` = employee name
  - `start/end` dates
  - `color` = department color

Frontend idea:

- Render events as badges per day if you want tight control (instead of FullCalendar default rendering).

---

## 10) Testing (optional but recommended)

When you’re ready:

- Feature test: employee can submit a request
- Feature test: manager can approve only their department requests
- Feature test: HR cannot approve a `pending` request (must be `dept_manager_approved`)

---

## If you want: “build it without Laravel”

Open `docs/crash-course/mini-app/` — it’s the same workflow in pure HTML/CSS/JS so you can focus on concepts first.

