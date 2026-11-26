<?php
/**
 * Question 20(c): session destruction (logout).
 */
require_once __DIR__ . '/includes/session.php';

$query = (($_GET['reason'] ?? '') === 'timeout') ? 'reason=timeout' : 'logged_out=1';

// 1. Empty the session array.
$_SESSION = [];

// 2. Expire the session cookie in the browser.
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $p['path'],
        'domain'   => $p['domain'],
        'secure'   => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => $p['samesite'] ?? 'Strict',
    ]);
}

// 3. Destroy the session data on the server.
session_destroy();

// 4. Send the user back to the login page.
header('Location: login.php?' . $query);
exit;
