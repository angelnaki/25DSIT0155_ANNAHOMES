<?php
// =============================================================
// db_connect.php — Database Configuration File
// Anna Homes · Project Milestone: From Static to Data-Driven
// =============================================================
// This file creates a single, reusable PDO connection object ($pdo)
// that all server-side scripts can require to talk to the database.
// =============================================================

$host = 'localhost';
$db   = 'homes_db';
$user = 'root';
$pass = '';  // Default XAMPP/WAMP password is empty

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass
    );
    // Throw exceptions on any database error (makes debugging easy)
    $pdo->setAttribute(PDO::ATTR_ERRMODE,        PDO::ERRMODE_EXCEPTION);
    // Return rows as associative arrays by default
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Stop execution and show a friendly error if connection fails
    die('<p style="color:red;font-family:sans-serif;">
            <strong>Database connection failed:</strong> ' . $e->getMessage() . '
         </p>');
}
?>
