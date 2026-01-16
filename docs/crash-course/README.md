# Leave Request App — Visual Crash Course (Beginner Friendly)

This folder is a **ground-up, visual crash course** for the `leave-request-app` you’re working on.

It has two parts:

- **Laravel walkthrough (this project):** how routes → middleware → controllers → models → views → database work together.
- **Mini clone (plain HTML/CSS/JS):** a tiny in-browser version of the same business flow so you can learn the *concepts* without Laravel complexity.

## Open the visual guide

- Open `docs/crash-course/index.html` in your browser.

## Deep dives (recommended next)

- `docs/crash-course/01-code-tour.md`: annotated “code tour” of the key files in *this* repo.
- `docs/crash-course/02-build-from-scratch-laravel.md`: step-by-step how to build this in Laravel from zero.

## Run the mini clone (no backend)

- Open `docs/crash-course/mini-app/index.html` in your browser.
- It stores data in `localStorage` so it behaves like a tiny “app”.

## What you’ll learn (mapped to *this* repo)

- **Routing**: `routes/web.php` loads `routes/auth.php`, `routes/employee.php`, `routes/manager.php`, `routes/hr.php`
- **Auth + sessions**: `app/Http/Controllers/AuthController.php`
- **Role-based access control**: `app/Http/Middleware/RoleMiddleware.php`
- **Business actions**:
  - Employee creates requests: `EmployeeController`
  - Manager approves/rejects (first gate): `ManagerController`
  - HR approves/rejects (final gate + balance deduction): `HRController`
- **Data model (Eloquent)**:
  - `User`, `Request`, `Balance`, `Department`, `Policy`
- **Views (Blade)**:
  - Layout shell: `resources/views/components/layout.blade.php`
  - Pages: `resources/views/*/*.blade.php`

