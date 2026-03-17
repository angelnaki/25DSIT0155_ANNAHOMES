<?php
session_start();
require 'db_config.php'; // Using the new db_config.php

$booking_ref = isset($_GET['ref']) ? $_GET['ref'] : '';

if(empty($booking_ref)) {
    header("Location: index.php");
    exit();
}

// Fetch booking details from database using correct column names
$stmt = db_query("SELECT * FROM reservations WHERE res_reference = ?", [$booking_ref]);
$booking = $stmt->fetch();

if(!$booking) {
    header("Location: index.php");
    exit();
}

// Get payment settings
$settings = [];
$settings_result = db_query("SELECT * FROM configuration");
while($row = $settings_result->fetch()) {
    $settings[$row['config_key']] = $row['config_value'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation · ANNA HOMES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
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
            line-height: 1.6;
        }

        .navbar {
            background: rgba(26, 43, 60, 0.85);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            color: var(--soft-white);
            box-shadow: 0 25px 50px -12px rgba(26,43,60,0.2);
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
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }

        .confirmation-card {
            background: var(--pure-white);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(26,43,60,0.2);
            border: 1px solid var(--champagne);
            text-align: center;
        }

        .success-icon {
            font-size: 5rem;
            color: #28a745;
            margin-bottom: 20px;
        }

        h1 {
            color: var(--deep-navy);
            margin-bottom: 10px;
        }

        .booking-ref {
            background: var(--champagne);
            padding: 15px;
            border-radius: 50px;
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--burgundy);
            margin: 20px 0;
            display: inline-block;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 30px 0;
            text-align: left;
        }

        .detail-item {
            padding: 15px;
            background: var(--soft-white);
            border-radius: 10px;
        }

        .detail-label {
            font-size: 0.9rem;
            opacity: 0.7;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--deep-navy);
        }

        .payment-instructions {
            background: rgba(139,58,58,0.1);
            border-left: 4px solid var(--burgundy);
            padding: 20px;
            text-align: left;
            margin: 30px 0;
            border-radius: 10px;
        }

        .payment-instructions h3 {
            color: var(--burgundy);
            margin-bottom: 15px;
        }

        .payment-detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed var(--champagne);
        }

        .button {
            display: inline-block;
            background: var(--burgundy);
            color: white;
            padding: 15px 40px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            margin: 20px 0;
            transition: all 0.25s;
        }

        .button:hover {
            background: #9f4545;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(139,58,58,0.3);
        }

        .print-btn {
            background: var(--deep-navy);
            margin-left: 15px;
        }

        .note {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            font-size: 0.95rem;
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
        <div class="confirmation-card">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            
            <h1>Booking Request Received!</h1>
            <p>Thank you for choosing ANNA HOMES. Your booking has been received and is pending confirmation.</p>
            
            <div class="booking-ref">
                <?php echo $booking['res_reference']; ?>
            </div>
            
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Property</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['res_property_name']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Location</div>
                    <div class="detail-value"><?php echo htmlspecialchars($booking['res_location']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Check-in</div>
                    <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['res_checkin'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Check-out</div>
                    <div class="detail-value"><?php echo date('M d, Y', strtotime($booking['res_checkout'])); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Guests</div>
                    <div class="detail-value"><?php echo $booking['res_guests']; ?> guest(s)</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total Amount</div>
                    <div class="detail-value">$<?php echo number_format($booking['res_total'], 2); ?></div>
                </div>
            </div>
            
            <div class="payment-instructions">
                <h3><i class="fas fa-info-circle"></i> Payment Instructions</h3>
                
                <?php if($booking['res_payment_method'] == 'mobile_money'): ?>
                    <div class="payment-detail-row">
                        <span>Mobile Money Number:</span>
                        <strong><?php echo $settings['mobile_money_number'] ?? '+256 786 749299'; ?></strong>
                    </div>
                    <div class="payment-detail-row">
                        <span>Account Name:</span>
                        <strong><?php echo $settings['bank_account_name'] ?? 'ANNA HOMES LTD'; ?></strong>
                    </div>
                    <p style="margin-top: 15px;">
                        <i class="fas fa-exclamation-circle" style="color: var(--burgundy);"></i> 
                        Please use your booking reference <strong><?php echo $booking['res_reference']; ?></strong> as payment reference.
                    </p>
                
                <?php elseif($booking['res_payment_method'] == 'bank_transfer'): ?>
                    <div class="payment-detail-row">
                        <span>Bank:</span>
                        <strong><?php echo $settings['bank_name'] ?? 'Stanbic Bank Uganda'; ?></strong>
                    </div>
                    <div class="payment-detail-row">
                        <span>Account Name:</span>
                        <strong><?php echo $settings['bank_account_name'] ?? 'ANNA HOMES LTD'; ?></strong>
                    </div>
                    <div class="payment-detail-row">
                        <span>Account Number:</span>
                        <strong><?php echo $settings['bank_account_number'] ?? '9030012345678'; ?></strong>
                    </div>
                    <div class="payment-detail-row">
                        <span>Branch:</span>
                        <strong><?php echo $settings['bank_branch'] ?? 'Kampala Main'; ?></strong>
                    </div>
                    <p style="margin-top: 15px;">
                        <i class="fas fa-exclamation-circle" style="color: var(--burgundy);"></i> 
                        Reference: <strong><?php echo $booking['res_reference']; ?></strong>
                    </p>
                
                <?php else: ?>
                    <p><strong>Card Payment:</strong> You will receive a secure payment link via email shortly.</p>
                <?php endif; ?>
                
                <div class="note">
                    <i class="fas fa-clock"></i> 
                    Your booking is pending payment confirmation. We'll confirm your stay once payment is verified (usually within 24 hours).
                </div>
            </div>
            
            <p>A confirmation email has been sent to <strong><?php echo htmlspecialchars($booking['res_guest_email']); ?></strong></p>
            
            <div>
                <a href="index.php" class="button">
                    <i class="fas fa-home"></i> Browse More Properties
                </a>
                <button onclick="window.print()" class="button print-btn">
                    <i class="fas fa-print"></i> Print Details
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto-print dialog? Uncomment if you want print dialog to appear automatically
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>