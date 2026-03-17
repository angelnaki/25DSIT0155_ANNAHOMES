<?php
session_start();
require 'db_config.php';

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['username']; // Form field name is 'username'
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        // Using correct column names: user_fullname, user_email, user_password
        $stmt = db_query("INSERT INTO users (user_fullname, user_email, user_password, user_role) VALUES (?, ?, ?, 'guest')", 
            [$fullname, $email, $password]);
        
        $user_id = $db_connection->lastInsertId();
        
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $fullname;
        $_SESSION['user_email'] = $email;
        
        header("Location: index.php");
        exit();
    } catch(Exception $e) {
        echo "Error: " . $e->getMessage();
        echo "<br><a href='signup.php'>Go back</a>";
    }
}
?>