# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Common Commands

- **Full local dev environment** (Laravel server, queue listener, Pail logs, Vite HMR):
  ```bash
  composer run dev
  ```
- **Initial project setup**:
  ```bash
  composer run setup
  php artisan db:seed --class=SiteSettingSeeder
  php artisan storage:link
  ```
- **Frontend assets** (`.npmrc` sets `ignore-scripts=true`):
  ```bash
  npm run dev
  npm run build
  ```
- **Run all tests** (uses in-memory SQLite per `phpunit.xml`):
  ```bash
  composer run test
  # or
  php artisan test
  ```
- **Run a single test file or method**:
  ```bash
  php artisan test tests/Feature/ExampleTest.php
  php artisan test --filter=test_the_application_returns_a_successful_response
  ```
- **Lint / format PHP code (Laravel Pint)**:
  ```bash
  ./vendor/bin/pint
  ./vendor/bin/pint --test
  ```

## Architecture & Domain Structure

This is a **Laravel 13 (PHP 8.3+)** web application for **Indo Berkah Konstruksi** combining a public company profile, a client project portal, and a **Filament v5** admin panel, styled with **Tailwind CSS v4** (`@tailwindcss/vite`) and **Vite 8**.

### 1. Three-Tier Routing & Access Control
- **Public Frontend (`routes/web.php`, `resources/views/frontend/`)**:
  - Company profile pages, news (`BeritaController`), team directory (`TeamMemberController`), portfolios, and active videos.
  - Service detail pages (`/layanan/{slug}` via `LayananController`) use hardcoded step/detail presets for the 4 core slugs (`pembangunan-rumah`, `gedung-komersial`, `renovasi-bangunan`, `konsultasi-ahli`) and fall back to a generic template if an admin customizes the service slug in `SiteSetting`.
  - Public lead/submission forms: Quote requests (`/penawaran` -> `Penawaran`), contract uploads (`/surat-perjanjian` -> `SuratPerjanjian`), and job applications (`/join-us` -> `TeamMember` created with `status = 'pending'` and `is_visible = false`).
- **Client Portal (`/dashbord`, `resources/views/user/`)**:
  - Guarded by `['auth', 'user']` middleware (`App\Http\Middleware\UserMiddleware`, registered in `bootstrap/app.php`), which allows users with `role` in `['owner', 'admin']`. Public registration (`/register` via `AuthController`) always creates users with `role = 'owner'`.
  - Note the intentional route URL and view spelling `/dashbord` (`resources/views/user/dashbord.blade.php`, route name `dashboard`) and `/dashbord/project/{project}` (`projects.show`). Preserve this spelling when referencing dashboard URLs or views.
  - Inside a project (`ProjectController@show`), clients upload design files, view admin progress photos, submit `UserFeedback` and `Review`, and see `SuratPerjanjian` records matched loosely by the project owner's `email` or `nama`.
- **Filament v5 Admin Panel (`/admin`, `app/Filament/Admin/Resources/`)**:
  - Configured in `App\Providers\Filament\AdminPanelProvider`. `User::canAccessPanel()` restricts `/admin` strictly to `role === 'admin'`.
  - Uses Filament v5 `Filament\Schemas\Schema` API (`form(Schema $schema)` and `infolist(Schema $schema)`). Most resources delegate schema and table definitions to `Schemas/*Form.php` and `Tables/*Table.php` classes, though some (`ProjectResource`, `UserFileResource`) define them inline on the Resource class.

### 2. Key Cross-Model Workflows & Side Effects
- **`Project` -> `Portfolio` Auto-Sync (`app/Models/Project.php`)**:
  - `Project::booted()` listens to `saved` and `deleted` model events.
  - Saving a `Project` with `status === 'selesai'` and `portfolio_approved === true` automatically `updateOrCreate`s a linked `Portfolio` record (populating `main_image` and `gallery` from the project's `UserFile` records if not explicitly set on the project).
  - Saving a project in any other state or deleting it automatically deletes the linked `Portfolio` row.
- **Dual Resources Over `UserFile` (`app/Models/UserFile.php`)**:
  - `UserFile` stores both client-uploaded designs (`type = 'design'`, scope `designs()`) and admin-uploaded construction progress photos (`type = 'progress'`, scope `progress()`).
  - In Filament, two separate resources query this same model: `UserDesignResource` (read-only list filtered to `type = 'design'`) and `UserFileResource` (used by admins to upload `type = 'progress'` files for a client's project).
- **Global `$settings` Injection (`App\Models\SiteSetting`, `AppServiceProvider`)**:
  - `SiteSetting::all()` overrides `Model::all()` to return a `pluck('value', 'key')` collection rather than a collection of `SiteSetting` models.
  - `AppServiceProvider::boot()` shares `$settings` (`SiteSetting::all()->toArray()`) with all Blade views via `View::composer('*', ...)`, wrapped in a `try/catch` so migrations can run before the table exists.
  - Admins edit settings through the custom page `App\Filament\Admin\Resources\SiteSettings\Pages\EditSiteSettings`, which merges database values over `$this->defaults`.
  - `AppServiceProvider::boot()` also forces `https` URL generation whenever `APP_ENV !== 'local'`.
- **Job Application Approval & Email (`TeamMembersTable`)**:
  - Pending `TeamMember` applications from `/join-us` have custom Filament table actions (`terima` / `tolak`) in `App\Filament\Admin\Resources\TeamMembers\Tables\TeamMembersTable` that update `status` (`accepted` / `rejected`), toggle `is_visible`, and send `App\Mail\ApplicationAccepted` or `App\Mail\ApplicationRejected`.
- **Reference Snapshots (`documen/`)**:
  - The `documen/` directory holds `.txt` copies of early Blade templates for reference only; it is not loaded by Laravel.
