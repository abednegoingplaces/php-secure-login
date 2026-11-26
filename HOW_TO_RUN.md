# How to run the Section F projects

Two separate projects, same features:

- `secure_login` = plain PHP, runs in XAMPP (Apache + MySQL)
- `secure_login_laravel` = Laravel, runs with `php artisan serve` (uses XAMPP's MySQL)

Demo logins for both:

| Username | Password     | Role  |
|----------|--------------|-------|
| admin    | Admin@1234   | admin |
| student  | Student@1234 | user  |

---

## A. Plain PHP version (no Laravel)

On this PC everything is already set up. Just:

1. Open **C:\xampp\xampp-control.exe**.
2. Click **Start** next to **Apache** and **MySQL** (both turn green).
3. Open the browser at **http://localhost/secure_login/**
4. Log in with a demo account. Try Log out, Register, and opening
   http://localhost/secure_login/dashboard.php while logged out (it sends you to login).

The running copy lives in `C:\xampp\htdocs\secure_login`.
The copy in this Academics folder is the source. If you edit it, copy it into htdocs again.

### Setting it up on another computer (e.g. lecturer's or a lab PC)

1. Install XAMPP and start Apache and MySQL.
2. Copy the `secure_login` folder into `C:\xampp\htdocs\`.
3. Open http://localhost/phpmyadmin, go to **Import**, choose `database.sql`, click **Go**.
4. Open http://localhost/secure_login/seed.php once (creates the demo users), then delete `seed.php`.
5. Open http://localhost/secure_login/

---

## B. Laravel version

On this PC everything is already set up. Just:

1. In the XAMPP Control Panel, make sure **MySQL** is started.
2. Open a terminal (PowerShell, CMD or Git Bash) and go to the project:

   ```
   cd "C:\Users\Admin\Academics\Projects\PHP Practical - Section F\secure_login_laravel"
   ```

3. Start Laravel's server:

   ```
   php artisan serve
   ```

4. Open **http://127.0.0.1:8000** and log in with a demo account.
5. Press **Ctrl + C** in the terminal to stop the server.

Useful commands (run inside the project folder):

| Command | What it does |
|---|---|
| `php artisan migrate:fresh --seed` | Rebuilds the tables and re-creates the demo users |
| `php artisan route:list` | Shows all routes (login, dashboard, logout, register) |
| `php artisan test` | Runs the automated tests |

### Setting it up on another computer

1. Install XAMPP (start MySQL) and Composer.
2. Copy the `secure_login_laravel` folder (you can leave out `vendor` to make it small).
3. In phpMyAdmin run: `CREATE DATABASE secure_login_laravel;`
4. In a terminal inside the folder:

   ```
   composer install
   copy .env.example .env
   php artisan key:generate
   php artisan migrate --seed
   php artisan serve
   ```

5. Open http://127.0.0.1:8000

---

## Tools installed on this PC

| Tool | Where | Check it works (open a NEW terminal) |
|---|---|---|
| XAMPP 8.2.12 (Apache, MariaDB, PHP 8.2.12, phpMyAdmin) | `C:\xampp` | open `C:\xampp\xampp-control.exe` |
| PHP 8.2.12 | `C:\xampp\php` (on PATH) | `php -v` |
| Composer 2.11.2 | `C:\xampp\php\composer.phar` (on PATH) | `composer --version` |
| Laravel Installer 5.33.0 | `%APPDATA%\Composer\vendor\bin` (on PATH) | `laravel --version` |
| MySQL client | `C:\xampp\mysql\bin` (on PATH) | `mysql -u root` |

PHP extensions `zip`, `intl`, `gd` and `sodium` were enabled in `C:\xampp\php\php.ini`.
The original file is saved as `php.ini.bak-original`.

## If something goes wrong

- **Apache won't start / port 80 busy:** another program (often Skype or IIS) is using port 80. Close it, or in XAMPP click Config > httpd.conf and change `Listen 80` to `Listen 8080`, then use http://localhost:8080/secure_login/
- **MySQL won't start:** another MySQL is already running. Stop it in Task Manager (mysqld.exe) and click Start again.
- **"Could not connect to the database":** MySQL isn't started in XAMPP.
- **`php` or `composer` not recognised:** close and reopen the terminal (PATH changes only apply to new terminals).
- **Laravel shows "419 Page Expired":** the form sat open too long. Refresh the page and try again.
