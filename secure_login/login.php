<?php
/**
 * Question 19 and 20(a): HTML login form, server-side validation,
 * password verification and session authentication.
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

// Already logged in: go straight to the dashboard.
if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

const MAX_ATTEMPTS = 5;
const LOCKOUT_SECONDS = 60;

$errors   = [];
$username = '';
$notice   = '';

if (($_GET['reason'] ?? '') === 'timeout') {
    $notice = 'Your session expired. Please log in again.';
} elseif (isset($_GET['logged_out'])) {
    $notice = 'You have been logged out.';
} elseif (isset($_GET['registered'])) {
    $notice = 'Account created. You can log in now.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Brute-force protection: lock the form after too many failures ---
    $attempts = $_SESSION['login_attempts'] ?? 0;
    $lockedAt = $_SESSION['locked_at'] ?? 0;

    if ($attempts >= MAX_ATTEMPTS && time() - $lockedAt < LOCKOUT_SECONDS) {
        $wait = LOCKOUT_SECONDS - (time() - $lockedAt);
        $errors[] = "Too many failed attempts. Try again in {$wait} seconds.";
    } else {
        if ($attempts >= MAX_ATTEMPTS) {
            $attempts = 0; // lockout period is over
        }

        // --- Server-side input validation ---
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
            $errors[] = 'Your form expired. Please try again.';
        }
        if ($username === '') {
            $errors[] = 'Username is required.';
        } elseif (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $username)) {
            $errors[] = 'Username must be 3 to 50 letters, numbers or underscores.';
        }
        if ($password === '') {
            $errors[] = 'Password is required.';
        } elseif (strlen($password) > 255) {
            $errors[] = 'Password is too long.';
        }

        // --- Password verification ---
        if (!$errors) {
            $stmt = db()->prepare(
                'SELECT id, full_name, username, password_hash, role FROM users WHERE username = ? LIMIT 1'
            );
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {

                // Upgrade the hash if PHP's default algorithm has changed.
                if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
                    db()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')
                        ->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
                }

                // --- Session authentication ---
                session_regenerate_id(true); // new session ID stops session fixation
                $_SESSION['user_id']       = (int) $user['id'];
                $_SESSION['username']      = $user['username'];
                $_SESSION['full_name']     = $user['full_name'];
                $_SESSION['role']          = $user['role'];
                $_SESSION['fingerprint']   = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
                $_SESSION['created_at']    = time();
                $_SESSION['last_activity'] = time();
                unset($_SESSION['login_attempts'], $_SESSION['locked_at'], $_SESSION['csrf_token']);

                db()->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);

                header('Location: dashboard.php');
                exit;
            }

            // One generic message, so an attacker cannot tell which usernames exist.
            $errors[] = 'Invalid username or password.';
            $attempts++;
            $_SESSION['login_attempts'] = $attempts;
            if ($attempts >= MAX_ATTEMPTS) {
                $_SESSION['locked_at'] = time();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Secure Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="card">
    <h1>Log in</h1>

    <?php if ($notice): ?>
        <p class="notice"><?= e($notice) ?></p>
    <?php endif; ?>

    <?php if ($errors): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="login.php" autocomplete="off" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($username) ?>"
               required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required maxlength="255">

        <button type="submit">Log in</button>
    </form>

    <p class="muted">No account? <a href="register.php">Register here</a>.</p>
</main>
</body>
</html>
