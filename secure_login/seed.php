<?php
/**
 * ONE-TIME SETUP: adds two demo accounts with properly hashed passwords.
 * Open http://localhost/secure_login/seed.php once, then DELETE this file.
 * The demo usernames and passwords are listed in README.md.
 */
require_once __DIR__ . '/includes/db.php';

$demoUsers = [
    // full name,             username,  email,                  password,       role
    ['System Administrator', 'admin',   'admin@example.com',   'Admin@1234',   'admin'],
    ['Demo Student',         'student', 'student@example.com', 'Student@1234', 'user'],
];

$stmt = db()->prepare(
    'INSERT IGNORE INTO users (full_name, username, email, password_hash, role) VALUES (?, ?, ?, ?, ?)'
);

$added = 0;
foreach ($demoUsers as [$name, $username, $email, $password, $role]) {
    $stmt->execute([$name, $username, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
    $added += $stmt->rowCount();
}

header('Content-Type: text/plain; charset=utf-8');
echo "Seed complete: {$added} demo user(s) added.\n";
echo "Now delete seed.php and open login.php.\n";
