<?php
// Database configuration for homes_db
$db_host = 'localhost';
$db_name = 'homes_db';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is empty

try {
    $db_connection = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db_connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Connection failed to database $db_name : " . $e->getMessage());
}

// Helper function for database operations
function db_query($sql, $params = []) {
    global $db_connection;
    $stmt = $db_connection->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}
?>