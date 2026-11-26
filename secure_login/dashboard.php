<?php
/**
 * Question 20(b): protected page. Only logged-in users can see it.
 * includes/auth.php does the access control before any output.
 */
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/db.php';

$stmt = db()->prepare('SELECT full_name, username, email, role, created_at, last_login FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// The account was deleted after login: end the session.
if (!$user) {
    header('Location: logout.php');
    exit;
}

// Admins also see the list of users (role-based access control).
$allUsers = [];
if ($user['role'] === 'admin') {
    $allUsers = db()->query('SELECT username, email, role, last_login FROM users ORDER BY id')->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Secure Login</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<main class="card wide">
    <header class="bar">
        <h1>Welcome, <?= e($user['full_name']) ?></h1>
        <a class="button" href="logout.php">Log out</a>
    </header>

    <p class="muted">You are logged in. This page is protected by a session check.</p>

    <table>
        <tr><th>Username</th><td><?= e($user['username']) ?></td></tr>
        <tr><th>Email</th><td><?= e($user['email']) ?></td></tr>
        <tr><th>Role</th><td><?= e($user['role']) ?></td></tr>
        <tr><th>Member since</th><td><?= e($user['created_at']) ?></td></tr>
        <tr><th>Last login</th><td><?= e($user['last_login'] ?? 'First login') ?></td></tr>
    </table>

    <?php if ($allUsers): ?>
        <h2>All users (admin only)</h2>
        <table>
            <tr><th>Username</th><th>Email</th><th>Role</th><th>Last login</th></tr>
            <?php foreach ($allUsers as $row): ?>
                <tr>
                    <td><?= e($row['username']) ?></td>
                    <td><?= e($row['email']) ?></td>
                    <td><?= e($row['role']) ?></td>
                    <td><?= e($row['last_login'] ?? '-') ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>
</body>
</html>
