<?php
session_start();
require 'connect.php'; // This now uses an_home

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data
    $property = $_POST['property'];
    $property_name = $_POST['property_name'];
    $price_per_night = $_POST['price_per_night'];
    $location = $_POST['location'];
    $guest_name = $_POST['guest_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = $_POST['guests'];
    $requests = $_POST['requests'];
    $payment_method = $_POST['payment_method'];
    
    // Calculate total
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $nights = $checkin_date->diff($checkout_date)->days;
    $subtotal = $nights * $price_per_night;
    $service_fee = 15;
    $total = $subtotal + $service_fee;
    
    // Generate booking reference
    $booking_ref = 'ANNA' . strtoupper(uniqid());
    
    // Insert into database
    try {
        $stmt = $pdo->prepare("INSERT INTO bookings 
            (booking_ref, property, property_name, location, guest_name, email, phone, 
             checkin_date, checkout_date, guests, nights, price_per_night, subtotal, 
             service_fee, total, payment_method, special_requests, booking_date, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
        
        $stmt->execute([
            $booking_ref, $property, $property_name, $location, $guest_name, $email, $phone,
            $checkin, $checkout, $guests, $nights, $price_per_night, $subtotal,
            $service_fee, $total, $payment_method, $requests
        ]);
        
        // Store booking info in session for confirmation page
        $_SESSION['booking_ref'] = $booking_ref;
        $_SESSION['booking_total'] = $total;
        $_SESSION['property_name'] = $property_name;
        
        // Redirect to confirmation page
        header("Location: booking_confirmation.php?ref=" . $booking_ref);
        exit();
        
    } catch(Exception $e) {
        echo "Error processing booking: " . $e->getMessage();
    }
}
?>