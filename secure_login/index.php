<?php
// Opening the project folder sends the user to the right page.
require_once __DIR__ . '/includes/session.php';
header('Location: ' . (is_logged_in() ? 'dashboard.php' : 'login.php'));
exit;
