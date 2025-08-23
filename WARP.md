# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

Project: Absensi App (Laravel 12, Vite, Tailwind, Pest, Spatie Permission)

1) Common commands (pwsh-friendly)

Prereqs
- PHP >= 8.2, Composer
- Node.js >= 18 and npm
- A database (MySQL/MariaDB or compatible)

Initial setup
- Copy env and generate key
  - pwsh: Copy-Item .env.example .env
  - php artisan key:generate
- Install dependencies
  - composer install
  - npm install
- Migrate database
  - php artisan migrate
- Optional tables when using these drivers
  - Sessions: php artisan session:table && php artisan migrate
  - Cache: php artisan cache:table && php artisan migrate
  - Queue: php artisan queue:table && php artisan migrate
- Optional vendor publishes (if used)
  - Spatie Permission migrations: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations" && php artisan migrate
  - DomPDF config: php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config
- Serving public storage (needed for generated payslips stored on the public disk)
  - php artisan storage:link

Run (development)
- All-in-one dev script (Laravel server + queue listener + Vite):
  - composer dev
- Or run individually:
  - php artisan serve
  - php artisan queue:listen --tries=1
  - npm run dev

Build frontend (production)
- npm run build

Tests (Pest + Laravel test runner)
- Run full test suite: composer test
- Run one test file (Pest): vendor/bin/pest tests/Feature/ExampleTest.php
- Run a single test by name:
  - Pest: vendor/bin/pest --filter "test name or pattern"
  - Artisan: php artisan test --filter "ClassName::methodName"
- Run a single test at a line:
  - vendor/bin/pest tests/Feature/ExampleTest.php:42

Formatting (PHP)
- Check/format with Laravel Pint:
  - vendor/bin/pint
  - Preview only: vendor/bin/pint --test
  - Alternative: composer exec pint

Database utilities
- Reset DB (destructive): php artisan migrate:fresh

Domain jobs and utilities
- Generate monthly payroll for all employees (produces PDF payslips into storage/app/public/payslips):
  - php artisan payroll:run --month=<1-12> --year=<YYYY>
  - Example: php artisan payroll:run --month=7 --year=2025

2) High-level architecture and flow

Overview
- This is an attendance and HR features app built on Laravel 12 with a server-rendered UI (Blade) and Vite-driven assets (Tailwind, Alpine).
- Roles are enforced with Spatie Permission. Roles referenced in code: Admin, HR, Karyawan.
- Locale/timezone are set to Indonesian defaults (id, Asia/Jakarta) via config/app.php and AppServiceProvider.

Key domain models and relationships
- User (App\Models\User)
  - Uses Spatie\Permission\Traits\HasRoles
  - Employee fields: nik, departemen, jabatan, tanggal_masuk, gaji_pokok, status_karyawan
- Attendance (App\Models\Attendance)
  - Belongs to User; daily record with date, check_in, check_out, ip_address, location
- OvertimeRequest (App\Models\OvertimeRequest)
  - Belongs to User; approver is a User via approved_by; fields: date, hours (decimal:2), reason, status
- LeaveRequest (App\Models\LeaveRequest)
  - Belongs to User; approver via approved_by; fields: type (izin/sakit/cuti), start_date, end_date, notes, attachment_path, status
- Payroll (App\Models\Payroll)
  - Belongs to User; stores month/year, basic_salary, overtime_pay, deductions, net_salary, pdf_path, paid_at
- Announcement (App\Models\Announcement)
  - Belongs to author (User via posted_by); has title/content and visible_from/visible_to window

HTTP and routing
- routes/web.php
  - Dashboard route aggregates per-user status without a dedicated controller: today’s attendance status, pending counts for overtime/leave based on role, and active announcements.
  - Authenticated routes group contains resource routes:
    - Attendance: index, store (all users); first-or-create today’s record and toggle check-in/check-out.
    - Overtime: index/create/store/show/update
    - Leave: index/create/store/show/update
  - Admin/HR-only subgroup (middleware role:Admin|HR):
    - CSV exports: overtime.export.csv, leave.export.csv
    - Payroll: full resource controller + payroll.export.csv
    - Announcements: full resource controller
    - Users: basic user CRUD
- Middleware aliases include Spatie role/permission middleware in app/Http/Kernel.php.

Controllers and behavior
- AttendanceController
  - index: first-or-create today’s record for the current user; if Admin/HR, also fetches all today’s records.
  - store: toggles check_in then check_out for the day, storing timestamps and IP; idempotent per day.
- OvertimeController
  - index: Admin/HR see all; others see own. store uses StoreOvertimeRequest. update uses UpdateOvertimeStatusRequest. CSV export streams all or own entries with approver/user info.
- LeaveController
  - index: Admin/HR see all; others see own. store validates and persists optional attachment to the public disk. update handles status changes by Admin/HR. CSV export similar to Overtime.
- PayrollController
  - index: lists payrolls and provides a user list to create entries; store computes overtime pay (1.5% of basic_salary per approved overtime hour) and optional deductions; renders a PDF slip via DomPDF and stores it to storage/app/public/payslips; update marks as paid; export streams CSV.

Policies and requests
- Policies explicitly mapped in AppServiceProvider for OvertimeRequest and LeaveRequest; Admin/HR can update, owners can view their own.
- Form Requests guard inputs and authorization:
  - StoreOvertimeRequest: Karyawan only; validates date/hours/reason
  - UpdateOvertimeStatusRequest: Admin/HR only; validates status
  - StoreLeaveRequest: Karyawan only; validates type/date range/attachment

Console command
- App\Console\Commands\PayrollRun (payroll:run)
  - Iterates Karyawan users; computes overtime pay and lateness/absence deductions using Attendance and approved Leave within working days (Mon–Fri); generates/updates Payroll records and writes PDF slips to the public disk.

Time, locale, and config
- AppServiceProvider sets Date locale and PHP default timezone from config/app.php (locale id, timezone Asia/Jakarta).
- phpunit.xml uses an in-memory SQLite database and array stores for cache/session/queue, enabling fast and isolated tests.

3) Notes distilled from README.md
- Use composer dev for an integrated dev loop (server + queue listener + Vite).
- Build assets with npm run build for production.
- Testing entrypoint provided: composer test.
- Follow PSR-12 with Laravel Pint for formatting.

4) Role-based access quick reference
- Admin/HR: manage payroll, announcements, users; view all leave/overtime; update statuses; access CSV exports.
- Karyawan: submit and view own attendance, overtime, and leave; cannot approve.

5) Troubleshooting quick checks
- If payslip PDFs aren’t accessible in the browser, ensure storage symlink exists: php artisan storage:link
- For Vite asset issues, reinstall frontend deps: remove node_modules and run npm install (see README)
- Ensure storage/ and bootstrap/cache/ are writable by the web server

