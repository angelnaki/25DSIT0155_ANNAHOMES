<?php
session_start();
require 'db_config.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=my_bookings.php");
    exit();
}

// Get current user's email from session
$user_email = $_SESSION['user_email'] ?? '';

// If email not in session, fetch from database
if (empty($user_email)) {
    $user = db_query("SELECT user_email FROM users WHERE user_id = ?", [$_SESSION['user_id']])->fetch();
    $user_email = $user['user_email'] ?? '';
}

// Fetch bookings for this user
$bookings = db_query("SELECT * FROM reservations WHERE res_guest_email = ? ORDER BY res_booking_date DESC", [$user_email])->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings · ANNA HOMES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        :root {
            --deep-navy: #1A2B3C;
            --soft-white: #F9F7F4;
            --champagne: #F7E6D0;
            --burgundy: #8B3A3A;
            --pure-white: #FFFFFF;
        }

        body {
            background-color: var(--soft-white);
            color: var(--deep-navy);
        }

        .navbar {
            background: rgba(26, 43, 60, 0.85);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            color: var(--soft-white);
            position: sticky;
            top: 0;
            z-index: 100;
            border-bottom: 1px solid rgba(247,230,208,0.2);
        }

        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo h1 {
            font-size: 1.8rem;
            color: var(--champagne);
            font-weight: 700;
            font-style: italic;
        }

        .nav-links a {
            color: var(--soft-white);
            text-decoration: none;
            margin-left: 20px;
            transition: all 0.25s;
        }

        .nav-links a:hover {
            color: var(--champagne);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .page-header {
            margin: 40px 0 30px;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: var(--deep-navy);
            border-left: 6px solid var(--burgundy);
            padding-left: 20px;
        }

        .bookings-grid {
            display: grid;
            gap: 25px;
            margin-bottom: 50px;
        }

        .booking-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: 1px solid var(--champagne);
            overflow: hidden;
            transition: transform 0.25s;
        }

        .booking-card:hover {
            transform: translateY(-5px);
        }

        .booking-header {
            background: var(--champagne);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .booking-ref {
            font-weight: 700;
            color: var(--burgundy);
            font-size: 1.1rem;
        }

        .booking-date {
            color: var(--deep-navy);
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .booking-body {
            padding: 20px;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .booking-body {
                grid-template-columns: 1fr;
            }
        }

        .property-info h3 {
            color: var(--deep-navy);
            margin-bottom: 8px;
        }

        .property-location {
            color: var(--burgundy);
            font-size: 0.9rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .dates-info {
            background: var(--soft-white);
            padding: 15px;
            border-radius: 10px;
        }

        .date-row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
        }

        .date-label {
            opacity: 0.7;
        }

        .date-value {
            font-weight: 600;
        }

        .price-info {
            text-align: right;
        }

        .price-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--burgundy);
        }

        .price-night {
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .status-completed {
            background: #cce5ff;
            color: #004085;
        }

        .payment-badge {
            padding: 5px 15px;
            border-radius: 40px;
            font-size: 0.85rem;
            font-weight: 500;
            display: inline-block;
            margin-left: 10px;
        }

        .payment-paid {
            background: #d4edda;
            color: #155724;
        }

        .booking-footer {
            padding: 15px 20px;
            background: var(--soft-white);
            border-top: 1px solid var(--champagne);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .view-btn {
            background: var(--burgundy);
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        .no-bookings {
            text-align: center;
            padding: 60px;
            background: var(--pure-white);
            border-radius: 15px;
            border: 1px solid var(--champagne);
        }

        .no-bookings i {
            font-size: 4rem;
            color: var(--champagne);
            margin-bottom: 20px;
        }

        .no-bookings h2 {
            color: var(--deep-navy);
            margin-bottom: 10px;
        }

        .no-bookings p {
            color: var(--deep-navy);
            opacity: 0.7;
            margin-bottom: 25px;
        }

        .browse-btn {
            background: var(--burgundy);
            color: white;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            display: inline-block;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1>ANNA HOMES</h1>
            </div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="my_bookings.php">My Bookings</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Sign Up</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1>My Bookings</h1>
        </div>

        <?php if(empty($bookings)): ?>
            <div class="no-bookings">
                <i class="fas fa-calendar-times"></i>
                <h2>No Bookings Yet</h2>
                <p>You haven't made any bookings with ANNA HOMES yet.</p>
                <a href="index.php" class="browse-btn">Browse Properties</a>
            </div>
        <?php else: ?>
            <div class="bookings-grid">
                <?php foreach($bookings as $booking): ?>
                <div class="booking-card">
                    <div class="booking-header">
                        <div>
                            <span class="booking-ref">
                                <i class="fas fa-tag"></i> <?php echo $booking['res_reference']; ?>
                            </span>
                            <span class="status-badge status-<?php echo $booking['res_status']; ?>">
                                <?php echo ucfirst($booking['res_status']); ?>
                            </span>
                            <span class="payment-badge payment-<?php echo $booking['res_payment_status']; ?>">
                                Payment: <?php echo ucfirst($booking['res_payment_status']); ?>
                            </span>
                        </div>
                        <div class="booking-date">
                            <i class="far fa-calendar-alt"></i> 
                            Booked: <?php echo date('M d, Y', strtotime($booking['res_booking_date'])); ?>
                        </div>
                    </div>

                    <div class="booking-body">
                        <div class="property-info">
                            <h3><?php echo htmlspecialchars($booking['res_property_name']); ?></h3>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i> <?php echo $booking['res_location']; ?>
                            </div>
                            <div><i class="fas fa-user"></i> <?php echo $booking['res_guests']; ?> guest(s)</div>
                        </div>

                        <div class="dates-info">
                            <div class="date-row">
                                <span class="date-label">Check-in:</span>
                                <span class="date-value"><?php echo date('M d, Y', strtotime($booking['res_checkin'])); ?></span>
                            </div>
                            <div class="date-row">
                                <span class="date-label">Check-out:</span>
                                <span class="date-value"><?php echo date('M d, Y', strtotime($booking['res_checkout'])); ?></span>
                            </div>
                            <div class="date-row">
                                <span class="date-label">Nights:</span>
                                <span class="date-value"><?php echo $booking['res_nights']; ?> nights</span>
                            </div>
                        </div>

                        <div class="price-info">
                            <div class="price-total">$<?php echo number_format($booking['res_total'], 2); ?></div>
                            <div class="price-night">$<?php echo $booking['res_price_per_night']; ?>/night</div>
                            <div style="margin-top: 10px; font-size: 0.85rem;">
                                Payment: <?php echo str_replace('_', ' ', $booking['res_payment_method']); ?>
                            </div>
                        </div>
                    </div>

                    <div class="booking-footer">
                        <div>
                            <?php if(!empty($booking['res_special_requests'])): ?>
                                <i class="fas fa-comment" style="color: var(--burgundy);"></i> 
                                <small>Special requests included</small>
                            <?php endif; ?>
                        </div>
                        <a href="booking_confirmation.php?ref=<?php echo $booking['res_reference']; ?>" class="view-btn">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>