# Absensi App (Laravel)

Aplikasi absensi berbasis Laravel untuk kebutuhan pencatatan kehadiran yang modular, modern, dan siap dikembangkan lebih lanjut.

Developer: Teuku Vaickal Rizki Irdian

—
This is a Laravel-based attendance application designed to be modular, modern, and ready for further development.

Developer: Teuku Vaickal Rizki Irdian


## Ringkasan (Bahasa Indonesia)

Absensi App dibangun dengan Laravel 12, Vite, dan Tailwind CSS. Proyek ini menyiapkan fondasi untuk fitur-fitur absensi seperti manajemen pengguna, peran/izin, export/import data, serta pembuatan dokumen (PDF). Paket yang telah terpasang:

- laravel/framework ^12
- spatie/laravel-permission (role & permission)
- barryvdh/laravel-dompdf (PDF)
- maatwebsite/excel (export/import Excel)
- laravel/breeze (starter auth; dev)
- pestphp/pest + pest-plugin-laravel (pengujian)
- laravel/pint (formatting)

### Persyaratan Sistem
- PHP >= 8.2, Composer
- Node.js >= 18 dan npm
- MySQL/MariaDB (atau database lain yang kompatibel) dan ekstensi pdo

### Instalasi
1) Salin konfigurasi environment
- Duplikat .env.example menjadi .env dan sesuaikan kredensial database.

2) Pasang dependensi backend & frontend
- composer install
- npm install

3) Generate APP_KEY
- php artisan key:generate

4) Migrasi database (dan seed bila tersedia)
- php artisan migrate

Jika Anda memakai driver database untuk session/cache/queue, pastikan tabel tersedia:
- php artisan session:table && php artisan migrate
- php artisan cache:table && php artisan migrate
- php artisan queue:table && php artisan migrate

Untuk paket tambahan (opsional, jika digunakan):
- Spatie Permission: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations" && php artisan migrate
- DomPDF: php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config

### Menjalankan Aplikasi (Mode Pengembangan)
Anda dapat menggunakan skrip yang sudah disediakan:
- composer dev

Atau jalankan secara terpisah:
- php artisan serve
- php artisan queue:listen --tries=1
- npm run dev

Aplikasi akan tersedia di http://localhost:8000 (default artisan serve) dan Vite dev server untuk aset frontend.

### Build Frontend (Produksi)
- npm run build

### Pengujian
- composer test

### Variabel Lingkungan Utama
Lihat .env.example untuk referensi lengkap. Beberapa yang penting:
- APP_NAME, APP_ENV, APP_DEBUG, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- SESSION_DRIVER, QUEUE_CONNECTION, CACHE_STORE
- MAIL_*
- REDIS_*

### Pedoman Koding (Coding Guidelines)
- PHP Style: PSR-12 + Laravel Pint.
  - Jalankan formatting: vendor/bin/pint atau composer exec pint
- Penamaan
  - Kelas: StudlyCase (App\Services\UserService)
  - Metode & variabel: camelCase
  - Konstanta: UPPER_SNAKE_CASE
  - Migration: timestamp_name_table
- Struktur & Praktik
  - Pisahkan logika domain ke service/action class bila kompleks.
  - Gunakan Form Request untuk validasi.
  - Gunakan Eloquent Resource/DTO untuk respons API terstruktur.
  - Tulis test (Pest) untuk fitur penting.
- Commit & Branch
  - Ikuti Conventional Commits (feat:, fix:, refactor:, test:, docs:, chore:)
  - Gunakan branch feature/..., fix/..., docs/...

### Struktur Proyek (Ringkas)
- app/ — kode aplikasi (Models, Http, Policies, dll.)
- bootstrap/ — bootstrap aplikasi
- config/ — konfigurasi
- database/ — migrations, seeders
- public/ — dokumen publik
- resources/ — views, assets (CSS/JS)
- routes/ — route files (web.php, api.php)
- tests/ — pengujian (Pest)

### Deployment Singkat
- Set APP_ENV=production, APP_DEBUG=false
- Jalankan migrasi: php artisan migrate --force
- Build aset: npm run build
- Cache konfigurasi & route: php artisan config:cache && php artisan route:cache
- Pastikan queue worker berjalan bila fitur queue dipakai.

### Troubleshooting
- Periksa permission storage/ dan bootstrap/cache/ (dapat ditulis)
- Cek koneksi database sesuai .env
- Jika error Vite, hapus node_modules dan install ulang: rm -rf node_modules && npm install

### Lisensi & Kredit
- Lisensi: MIT (mengikuti lisensi Laravel skeleton)
- Dikembangkan oleh: Teuku Vaickal Rizki Irdian


## Overview (English)

Absensi App is built with Laravel 12, Vite, and Tailwind CSS. It provides a foundation for attendance features such as user management, roles/permissions, data export/import, and document generation (PDF). Bundled packages include:

- laravel/framework ^12
- spatie/laravel-permission (roles & permissions)
- barryvdh/laravel-dompdf (PDF)
- maatwebsite/excel (Excel export/import)
- laravel/breeze (starter auth; dev)
- pestphp/pest + pest-plugin-laravel (testing)
- laravel/pint (formatting)

### Requirements
- PHP >= 8.2, Composer
- Node.js >= 18 and npm
- MySQL/MariaDB (or compatible) with pdo extension

### Installation
1) Copy environment config
- Duplicate .env.example to .env and adjust DB credentials.

2) Install dependencies
- composer install
- npm install

3) Generate APP_KEY
- php artisan key:generate

4) Run database migrations (and seed if available)
- php artisan migrate

If you use database drivers for session/cache/queue, ensure tables exist:
- php artisan session:table && php artisan migrate
- php artisan cache:table && php artisan migrate
- php artisan queue:table && php artisan migrate

Optional (if used):
- Spatie Permission: php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider" --tag="migrations" && php artisan migrate
- DomPDF: php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider" --tag=config

### Run (Development)
Use the provided script:
- composer dev

Or run separately:
- php artisan serve
- php artisan queue:listen --tries=1
- npm run dev

App will be served at http://localhost:8000 (artisan) with Vite dev server for assets.

### Build Frontend (Production)
- npm run build

### Testing
- composer test

### Key Environment Variables
See .env.example for the full list. Notable ones:
- APP_NAME, APP_ENV, APP_DEBUG, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- SESSION_DRIVER, QUEUE_CONNECTION, CACHE_STORE
- MAIL_*
- REDIS_*

### Coding Guidelines
- PHP Style: PSR-12 + Laravel Pint.
  - Format code: vendor/bin/pint or composer exec pint
- Naming
  - Classes: StudlyCase (App\Services\UserService)
  - Methods & variables: camelCase
  - Constants: UPPER_SNAKE_CASE
  - Migrations: timestamp_name_table
- Structure & Practices
  - Extract complex domain logic into service/action classes.
  - Use Form Requests for validation.
  - Use Eloquent Resources/DTOs for consistent API responses.
  - Write tests (Pest) for critical paths.
- Commits & Branching
  - Follow Conventional Commits (feat:, fix:, refactor:, test:, docs:, chore:)
  - Use feature/..., fix/..., docs/... branches

### Project Structure (Brief)
- app/ — application code (Models, Http, Policies, etc.)
- bootstrap/ — app bootstrap
- config/ — configuration
- database/ — migrations, seeders
- public/ — public document root
- resources/ — views, assets (CSS/JS)
- routes/ — route files (web.php, api.php)
- tests/ — tests (Pest)

### Deployment (Quick)
- Set APP_ENV=production, APP_DEBUG=false
- Migrate DB: php artisan migrate --force
- Build assets: npm run build
- Cache config and routes: php artisan config:cache && php artisan route:cache
- Ensure a queue worker is running if queue features are used.

### Troubleshooting
- Check write permissions for storage/ and bootstrap/cache/
- Verify DB connectivity per .env
- If Vite errors, reinstall frontend deps: remove node_modules and npm install

### License & Credits
- License: MIT
- Developed by: Teuku Vaickal Rizki Irdian
