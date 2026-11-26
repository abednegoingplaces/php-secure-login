# Secure Login: PHP Programming, Section F (Practical Project)

A secure, multi-page PHP and MySQL web application with a login form, server-side
validation, password verification, session authentication, access control and logout.

## Project structure (Question 20)

```
secure_login/
├── login.php          (a) login form, validation, password check, starts the session
├── dashboard.php      (b) protected page, only for logged-in users
├── logout.php         (c) destroys the session and cookie
├── register.php       creates new accounts with hashed passwords
├── index.php          redirects to login or dashboard
├── seed.php           ONE-TIME script that adds two demo accounts (delete after use)
├── database.sql       Question 21: database and users table
├── assets/style.css
└── includes/
    ├── config.php     database settings
    ├── db.php         PDO connection
    ├── session.php    secure session start, output escaping, CSRF tokens
    ├── auth.php       access-control guard used by protected pages
    └── .htaccess      blocks direct browser access to includes/
```

## How to run it with XAMPP

1. Copy the whole `secure_login` folder into `C:\xampp\htdocs\`.
2. Open the XAMPP Control Panel and start **Apache** and **MySQL**.
3. Go to `http://localhost/phpmyadmin`, open the **Import** tab, choose `database.sql` and click **Go**.
4. Open `http://localhost/secure_login/seed.php` once to create the demo accounts. Then delete `seed.php`.
5. Open `http://localhost/secure_login/login.php` and log in.

## How to run it with Laragon

1. Copy the `secure_login` folder into `C:\laragon\www\`.
2. Click **Start All** in Laragon.
3. Click **Database** to open HeidiSQL or phpMyAdmin, and run `database.sql`.
4. Open `http://localhost/secure_login/seed.php` once, then delete `seed.php`.
5. Open `http://localhost/secure_login/login.php`. Laragon also gives you `http://secure_login.test/`.

If your MySQL root user has a password, put it in `includes/config.php`.

## Demo accounts (created by seed.php)

| Username | Password     | Role  |
|----------|--------------|-------|
| admin    | Admin@1234   | admin |
| student  | Student@1234 | user  |

You can also create your own account on `register.php`.

## How each requirement is met (Question 19)

| Requirement | Where | How |
|---|---|---|
| HTML login form | `login.php` | HTML5 form that sends the username and password by POST. |
| Server-side input validation | `login.php`, `register.php` | Input is trimmed and checked for empty values, length, allowed characters and email format. Errors are shown to the user. |
| Password verification | `login.php` | Passwords are stored with `password_hash()` (bcrypt) and checked with `password_verify()`. Plain-text passwords are never stored. |
| Session authentication | `login.php`, `includes/session.php` | After a correct login the user's ID, name and role are saved in `$_SESSION`. The session ID is regenerated to stop session fixation. |
| Access control | `includes/auth.php`, `dashboard.php` | Every protected page includes `auth.php`, which redirects visitors without a session to the login page. Admins see an extra user list (role-based access). |
| Session destruction (logout) | `logout.php` | Empties `$_SESSION`, expires the session cookie, calls `session_destroy()` and redirects to the login page. |

### Extra security measures

- **SQL injection:** every query uses PDO prepared statements.
- **Cross-site scripting (XSS):** all output goes through `htmlspecialchars()` via the `e()` helper.
- **Cross-site request forgery (CSRF):** forms carry a random token that the server checks.
- **Session hijacking:** cookies are HttpOnly and SameSite=Strict, the session is tied to the browser's user agent, and the ID is regenerated every 10 minutes.
- **Idle timeout:** users are logged out after 15 minutes without activity.
- **Brute force:** the login form locks for 60 seconds after 5 failed attempts.
- **User enumeration:** a single message, "Invalid username or password", is shown for any failed login.

## Database design (Question 21)

Database `secure_login`, table `users`:

| Field | Type | Purpose |
|---|---|---|
| id | INT UNSIGNED, primary key, auto increment | Unique user ID stored in the session |
| full_name | VARCHAR(100) | Name shown on the dashboard |
| username | VARCHAR(50), unique | Login name |
| email | VARCHAR(120), unique | Contact email, also unique |
| password_hash | VARCHAR(255) | Output of `password_hash()`, never the real password |
| role | ENUM('user','admin') | Used for access control |
| created_at | TIMESTAMP | When the account was created |
| last_login | DATETIME, nullable | Updated on every successful login |
