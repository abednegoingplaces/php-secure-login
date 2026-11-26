# Secure Login System (PHP and Laravel)

A small, secure login system: users register, log in, see a protected dashboard and log out.
Admins also see a list of all users.

It is built twice for the PHP Programming practical (Section F):

- **`secure_login/`** - plain PHP with MySQL (PDO)
- **`secure_login_laravel/`** - the same features in Laravel 12

## What it does

- **Register** with a full name, username, email and password. The password is stored only as a hash.
- **Log in** with server-side validation and password verification.
- **Protected dashboard** that only logged-in users can open. Visitors are sent to the login page.
- **Roles:** `admin` and `user`. Admins see an extra list of all users.
- **Log out**, which fully destroys the session.

### Security built in

| Protection | Plain PHP | Laravel |
|---|---|---|
| Passwords stored as hashes | `password_hash` / `password_verify` | `hashed` cast, `Auth::attempt` |
| SQL injection | PDO prepared statements | Eloquent |
| Cross-site scripting (XSS) | `htmlspecialchars` on all output | Blade `{{ }}` escaping |
| Forged forms (CSRF) | token in every form | `@csrf` |
| Session hijacking and fixation | new session ID at login, browser fingerprint, idle timeout | `session()->regenerate()` |
| Brute force | locked for 60 s after 5 failed attempts | Laravel rate limiting |
| Direct access to config files | `includes/.htaccess` blocks it | files live outside `public/` |

## Running it locally (step by step)

Every grey box below is a command you can copy (use the copy button on the box) and paste into a terminal.
The commands work in **PowerShell** and **Command Prompt** on Windows.

### Step 1: Install the tools (once)

| Tool | Needed for | Download |
|---|---|---|
| XAMPP 8.2 (Apache, MySQL, PHP 8.2) | both versions | https://www.apachefriends.org/ |
| Composer | Laravel version | https://getcomposer.org/ |
| Git | downloading the code | https://git-scm.com/ |

Open a **new** terminal and check that they work. Each command should print a version number:

```
php -v
composer --version
git --version
```

If `php` is "not recognised", add `C:\xampp\php` to your Windows PATH, then open a new terminal.

### Step 2: Download the code

```
git clone https://github.com/abednegoingplaces/php-secure-login.git
cd php-secure-login
```

Run all the commands below from this `php-secure-login` folder unless a step says otherwise.

### Step 3: Start Apache and MySQL

Open the **XAMPP Control Panel** and click **Start** next to **Apache** and **MySQL** (both turn green):

```
C:\xampp\xampp-control.exe
```

### Step 4a: Run the plain PHP version

**1. Copy the site into XAMPP's web folder:**

```
xcopy /E /I /Y secure_login C:\xampp\htdocs\secure_login
```

**2. Create the database and its `users` table:**

```
C:\xampp\mysql\bin\mysql.exe -u root -e "source secure_login/database.sql"
```

**3. Add the two demo accounts**, then delete the seed script from the web folder so nobody can run it from a browser:

```
php secure_login/seed.php
del C:\xampp\htdocs\secure_login\seed.php
```

**4. Open the site:** http://localhost/secure_login/ and log in with a [demo account](#demo-accounts).

### Step 4b: Run the Laravel version

**1. Create the database:**

```
C:\xampp\mysql\bin\mysql.exe -u root -e "CREATE DATABASE IF NOT EXISTS secure_login_laravel"
```

**2. Go into the Laravel folder:**

```
cd secure_login_laravel
```

**3. Set it up (first time only).** These commands download the packages, create the settings file and secret key, then build the tables and demo accounts:

```
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate --seed
```

On macOS / Linux, use `cp .env.example .env` instead of the `copy` line.

**4. Start the site:**

```
php artisan serve
```

**5. Open the site:** http://127.0.0.1:8000 and log in with a [demo account](#demo-accounts).
To stop the site, press **Ctrl + C** in the terminal.

**Next time** you only need to start MySQL in XAMPP and run these from the `php-secure-login` folder:

```
cd secure_login_laravel
php artisan serve
```

### Step 5: Run the automated tests (optional)

From the `secure_login_laravel` folder:

```
php artisan test
```

## Demo accounts

The same in both versions:

| Username | Password | Role |
|---|---|---|
| `admin` | `Admin@1234` | admin (also sees all users) |
| `student` | `Student@1234` | user |

You can also create your own account on the **Register** page.

## Things to try

- Open the dashboard while logged out (http://localhost/secure_login/dashboard.php or http://127.0.0.1:8000/dashboard). You are sent to the login page.
- Enter a wrong password five times in the plain PHP version. The form locks for 60 seconds.
- Log in as `admin` and then as `student`, and compare the dashboards.

## If something goes wrong

| Problem | Fix |
|---|---|
| "Could not connect to the database" | MySQL isn't running. Start it in the XAMPP Control Panel. |
| Apache won't start (port 80 busy) | Close Skype or IIS, or change `Listen 80` to `Listen 8080` in XAMPP's `httpd.conf` and use http://localhost:8080/secure_login/ |
| `php` or `composer` "not recognised" | Install the tool, add it to PATH, then open a **new** terminal. |
| Laravel: "No application encryption key" | Run `php artisan key:generate`. |
| Laravel: "419 Page Expired" | The form sat open too long. Refresh the page and try again. |
| Laravel: port 8000 already in use | Run `php artisan serve --port=8010` and open http://127.0.0.1:8010 |
| Your MySQL has a password | Plain PHP: set `DB_PASS` in `secure_login/includes/config.php`. Laravel: set `DB_PASSWORD` in `.env`. |

## Project structure

```
secure_login/                  plain PHP version
  login.php                    login form, validation, password check, starts the session
  register.php                 creates accounts with hashed passwords
  dashboard.php                protected page (admins also see all users)
  logout.php                   destroys the session and its cookie
  database.sql                 database and users table
  seed.php                     adds the two demo accounts (run once, then delete)
  includes/                    config, database connection, secure session, access guard
secure_login_laravel/          Laravel version
  app/Http/Controllers/        AuthController (login, register, logout), DashboardController
  routes/web.php               routes; the auth middleware guards /dashboard
  resources/views/             Blade pages
  database/migrations/         users table
  database/seeders/            demo accounts
HOW_TO_RUN.md                  extra notes for running both versions
```

More detail on each version is in [`secure_login/README.md`](secure_login/README.md) and [`secure_login_laravel/README_SECTION_F.md`](secure_login_laravel/README_SECTION_F.md).
