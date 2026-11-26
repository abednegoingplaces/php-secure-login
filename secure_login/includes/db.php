<?php
require_once __DIR__ . '/config.php';

/**
 * Returns one shared PDO connection.
 * PDO with prepared statements protects every query from SQL injection.
 */
function db(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Log the real error and show a safe message to the user.
            error_log('DB connection failed: ' . $e->getMessage());
            http_response_code(500);
            exit('Could not connect to the database. Check includes/config.php and that MySQL is running.');
        }
    }

    return $pdo;
}
