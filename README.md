<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>
# Todo Pro

Todo Pro is a modern Laravel task management app with private user workspaces.
Each user can register, log in, and manage only their own tasks using a fast AJAX-powered dashboard.

## Features

- User authentication
  - Register, login, logout
  - Account settings (profile update, password change, account delete)
- Private task ownership
  - Every task belongs to one user
  - Users can only view and modify their own tasks
- Task management
  - Create, edit, delete, show details
  - Status: `pending` / `done`
  - Priority: `low` / `medium` / `high`
  - Due date and notes
- Productivity tools
  - Search, filter, tabs, pagination
  - Bulk actions (mark done / delete)
  - Quick status toggle
- AJAX dashboard UX
  - Filtering and pagination without full page reload
  - Inline updates and toast feedback
- Demo data tools
  - Seeder with sample users/tasks
  - Sidebar quick fill button to generate test tasks

## Tech Stack

- Laravel (PHP)
- Blade templates
- Bootstrap 5
- Vanilla JavaScript (AJAX + DOM updates)
- SQLite (default local setup)

## Project Structure (Key Files)

- Routes: `routes/web.php`
- Controllers:
  - `app/Http/Controllers/TaskController.php`
  - `app/Http/Controllers/AuthController.php`
  - `app/Http/Controllers/AccountController.php`
- Models:
  - `app/Models/User.php`
  - `app/Models/Task.php`
- Views:
  - `resources/views/welcome.blade.php`
  - `resources/views/tasks/index.blade.php`
  - `resources/views/tasks/partials/table.blade.php`
  - `resources/views/layouts/app.blade.php`

## Installation

1. Clone the project
2. Install dependencies

```bash
composer install
```

3. Create environment file

```bash
cp .env.example .env
```

4. Generate app key

```bash
php artisan key:generate
```

5. Run migrations

```bash
php artisan migrate
```

6. Seed demo data (optional but recommended)

```bash
php artisan db:seed
```

7. Start the app

```bash
php artisan serve
```

Open: `http://127.0.0.1:8000`

## Demo Account

After seeding:

- Email: `demo@todopro.test`
- Password: `password123`

## Testing

Run tests with:

```bash
php artisan test
```

## Notes

- AJAX endpoints are protected by `auth` middleware.
- Task isolation is enforced at controller level and database relationship level.
- If you change database settings, update `.env` before running migrations/seeds.

## License

This project is open-source and available under the MIT license.

