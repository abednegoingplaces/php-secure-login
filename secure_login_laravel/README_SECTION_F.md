# Secure Login (Laravel version): PHP Programming, Section F

The same practical as the plain-PHP `secure_login` project, built with the Laravel framework.
It has a login form, server-side validation, password verification, session authentication,
access control and logout.

## Where each requirement lives

| Requirement | File | How |
|---|---|---|
| HTML login form | `resources/views/auth/login.blade.php` | Blade form that POSTs to `/login` |
| Server-side validation | `app/Http/Controllers/AuthController.php` | `$request->validate()` rules for required, length, characters, email and unique |
| Password verification | `AuthController@login` | `Auth::attempt()` checks the password against the bcrypt hash |
| Session authentication | `AuthController@login` | `session()->regenerate()` after login stops session fixation |
| Access control | `routes/web.php` | `auth` middleware guards `/dashboard`; admins see an extra user list |
| Logout (session destruction) | `AuthController@logout` | `Auth::logout()`, `session()->invalidate()`, `regenerateToken()` |
| Users database (Q21) | `database/migrations/0001_01_01_000000_create_users_table.php` | `users` table with username, email, password hash, role, last_login |

How the plain-PHP files map to Laravel:

| Plain PHP | Laravel |
|---|---|
| `login.php` | `GET/POST /login` in `AuthController` + `auth/login.blade.php` |
| `dashboard.php` | `GET /dashboard` in `DashboardController` + `dashboard.blade.php` |
| `logout.php` | `POST /logout` in `AuthController@logout` |
| `database.sql` | the users migration |
| `seed.php` | `database/seeders/DatabaseSeeder.php` |

### Extra security that Laravel provides

- **SQL injection:** Eloquent and the query builder use prepared statements.
- **XSS:** Blade's `{{ }}` escapes every value.
- **CSRF:** `@csrf` tokens on every form, checked automatically.
- **Brute force:** `RateLimiter` locks login for 60 seconds after 5 failures.
- **Sessions:** HttpOnly, SameSite=Strict cookies, a 15-minute idle limit, and the session ends when the browser closes.
- **Mass assignment:** `role` is not fillable, so nobody can register as an admin.

## How to run it

Needs PHP 8.2+, Composer and MySQL/MariaDB. XAMPP provides PHP and MySQL.

1. Start **MySQL** in the XAMPP Control Panel.
2. Create the database. In phpMyAdmin, run: `CREATE DATABASE secure_login_laravel;`
3. In a terminal inside this folder, run:

```
composer install
copy .env.example .env      (only if .env is missing)
php artisan key:generate    (only if APP_KEY in .env is empty)
php artisan migrate
php artisan db:seed
php artisan serve
```

4. Open `http://127.0.0.1:8000` and log in.

The database settings are in `.env` (`DB_DATABASE=secure_login_laravel`, user `root`, empty password).

## Demo accounts (created by `php artisan db:seed`)

| Username | Password     | Role  |
|----------|--------------|-------|
| admin    | Admin@1234   | admin |
| student  | Student@1234 | user  |
