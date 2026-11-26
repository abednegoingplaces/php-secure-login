<?php
/**
 * Secure session start, used by every page.
 * The cookie settings must be applied BEFORE session_start().
 */
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');   // reject session IDs the server did not create (fixation)
    ini_set('session.use_only_cookies', '1');  // never accept the session ID from the URL

    session_set_cookie_params([
        'lifetime' => 0,                         // cookie dies when the browser closes
        'path'     => '/',
        'secure'   => !empty($_SERVER['HTTPS']), // HTTPS-only cookie when the site uses HTTPS
        'httponly' => true,                      // JavaScript cannot read it (XSS cookie theft)
        'samesite' => 'Strict',                  // not sent on cross-site requests (CSRF)
    ]);

    session_name('SECURELOGINSID');
    session_start();
}

const SESSION_IDLE_LIMIT = 900; // log out after 15 minutes of inactivity

/** Escape output for HTML to prevent cross-site scripting. */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/** CSRF token stored in the session and embedded in every form. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_is_valid(?string $token): bool
{
    return is_string($token)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}
