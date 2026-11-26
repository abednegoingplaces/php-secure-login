<?php
/**
 * Creates user accounts with a hashed password.
 * Supports question 21: users are stored in the users table.
 */
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/db.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

$errors = [];
$old = ['full_name' => '', 'username' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['full_name'] = trim($_POST['full_name'] ?? '');
    $old['username']  = trim($_POST['username'] ?? '');
    $old['email']     = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm          = $_POST['confirm_password'] ?? '';

    // --- Server-side validation ---
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $errors[] = 'Your form expired. Please try again.';
    }
    if ($old['full_name'] === '' || strlen($old['full_name']) > 100) {
        $errors[] = 'Full name is required (max 100 characters).';
    }
    if (!preg_match('/^[A-Za-z0-9_]{3,50}$/', $old['username'])) {
        $errors[] = 'Username must be 3 to 50 letters, numbers or underscores.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 120) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        $errors[] = 'Password must be at least 8 characters and include a letter and a number.';
    } elseif (strlen($password) > 255) {
        $errors[] = 'Password is too long.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $check = db()->prepare('SELECT 1 FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->execute([$old['username'], $old['email']]);
        if ($check->fetch()) {
            $errors[] = 'That username or email is already registered.';
        }
    }

    if (!$errors) {
        $stmt = db()->prepare(
            'INSERT INTO users (full_name, username, email, password_hash) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([
            $old['full_name'],
            $old['username'],
            $old['email'],
            password_hash($password, PASSWORD_DEFAULT), // bcrypt hash with a random salt
        ]);
        unset($_SESSION['csrf_token']);
        header('Location: login.php?registered=1');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Secure Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="card">
    <h1>Create account</h1>

    <?php if ($errors): ?>
        <ul class="errors">
            <?php foreach ($errors as $error): ?>
                <li><?= e($error) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post" action="register.php" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" required maxlength="100">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="<?= e($old['username']) ?>"
               required minlength="3" maxlength="50" pattern="[A-Za-z0-9_]+">

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" required maxlength="120">

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8" maxlength="255">

        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8" maxlength="255">

        <button type="submit">Register</button>
    </form>

    <p class="muted">Already registered? <a href="login.php">Log in</a>.</p>
</main>
</body>
</html>
