<?php
/**
 * Access control. Include this at the top of any page that needs a logged-in user.
 */
require_once __DIR__ . '/session.php';

// 1. No user in the session: send them to the login page.
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

// 2. Session hijacking check: the browser fingerprint must match the one saved at login.
$fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] ?? '');
if (!hash_equals($_SESSION['fingerprint'] ?? '', $fingerprint)) {
    header('Location: logout.php');
    exit;
}

// 3. Idle timeout.
if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_IDLE_LIMIT) {
    header('Location: logout.php?reason=timeout');
    exit;
}
$_SESSION['last_activity'] = time();

// 4. Change the session ID every 10 minutes to shorten the life of a stolen ID.
if (time() - ($_SESSION['created_at'] ?? 0) > 600) {
    session_regenerate_id(true);
    $_SESSION['created_at'] = time();
}

/** Role-based access control: stop the page unless the user has the role. */
function require_role(string $role): void
{
    if (($_SESSION['role'] ?? '') !== $role) {
        http_response_code(403);
        exit('403 Forbidden: you do not have permission to view this page.');
    }
}
