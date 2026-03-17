<?php
session_start();
require 'db_config.php'; // Make sure this points to your new db_config.php

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';

    // FIXED: Changed from 'email' to 'user_email' to match database
    $stmt = db_query("SELECT * FROM users WHERE user_email = ?", [$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['user_password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['user_fullname']; // FIXED: from 'username' to 'user_fullname'
        $_SESSION['user_email'] = $user['user_email'];
        
        // Redirect admin to admin panel, regular users to requested page
        if ($user['user_role'] == 'admin') {
            header("Location: admin-panel/dashboard.php");
        } else {
            header("Location: $redirect");
        }
        exit();
    } else {
        echo "Invalid credentials. <a href='login.php?redirect=" . urlencode($redirect) . "'>Try again</a>";
    }
}
?>