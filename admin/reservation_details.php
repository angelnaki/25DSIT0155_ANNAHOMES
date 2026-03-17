<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_check = db_query("SELECT user_role FROM users WHERE user_id = ?", [$_SESSION['user_id']]);
$current_user = $user_check->fetch();

if (!$current_user || $current_user['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

$res_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get reservation details
$reservation = db_query("SELECT * FROM reservations WHERE res_id = ?", [$res_id])->fetch();

if (!$reservation) {
    header("Location: reservations.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation Details · HOMES DB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, sans-serif;
        }

        :root {
            --primary-dark: #1A2B3C;
            --primary-light: #F9F7F4;
            --accent-cream: #F7E6D0;
            --accent-burgundy: #8B3A3A;
            --pure-white: #FFFFFF;
            --shadow-lg: 0 25px 50px -12px rgba(26,43,60,0.2);
        }

        body {
            background-color: var(--primary-light);
            display: flex;
            min-height: 100vh;
        }

        .nav-sidebar {
            width: 280px;
            background: var(--primary-dark);
            color: var(--primary-light);
            padding: 30px 0;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            box-shadow: var(--shadow-lg);
        }

        .sidebar-header {
            padding: 0 25px 30px;
            border-bottom: 1px solid rgba(247,230,208,0.2);
        }

        .sidebar-header h2 {
            color: var(--accent-cream);
            font-style: italic;
            font-size: 1.8rem;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            color: var(--primary-light);
            opacity: 0.7;
            font-size: 0.9rem;
        }

        .nav-menu {
            padding: 20px 0;
        }

        .nav-item {
            padding: 12px 25px;
            display: flex;
            align-items: center;
            gap: 15px;
            color: var(--primary-light);
            text-decoration: none;
            transition: all 0.25s;
            border-left: 4px solid transparent;
        }

        .nav-item:hover, .nav-item.active {
            background: rgba(247,230,208,0.1);
            border-left-color: var(--accent-burgundy);
            color: var(--accent-cream);
        }

        .nav-item i {
            width: 24px;
            font-size: 1.2rem;
            color: var(--accent-burgundy);
        }

        .main-panel {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
        }

        .top-header {
            background: var(--pure-white);
            padding: 15px 30px;
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border: 1px solid var(--accent-cream);
        }

        .page-header h1 {
            color: var(--primary-dark);
            font-size: 1.8rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-profile span {
            color: var(--primary-dark);
            font-weight: 500;
        }

        .logout-link {
            background: var(--accent-burgundy);
            color: white;
            padding: 8px 20px;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.25s;
        }

        .logout-link:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent-burgundy);
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }

        .detail-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 25px;
            margin-bottom: 25px;
        }

        .card-title {
            color: var(--primary-dark);
            font-size: 1.2rem;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-cream);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-title i {
            color: var(--accent-burgundy);
        }

        .info-row {
            display: flex;
            margin-bottom: 15px;
            padding: 8px 0;
            border-bottom: 1px dashed var(--accent-cream);
        }

        .info-label {
            width: 140px;
            font-weight: 600;
            color: var(--primary-dark);
            opacity: 0.8;
        }

        .info-value {
            flex: 1;
            color: var(--primary-dark);
        }

        .badge-large {
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 1rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-pending {
            background: #fff3cd;
            color: #856404;
        }

        .badge-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .badge-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-completed {
            background: #cce5ff;
            color: #004085;
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn-primary {
            background: var(--accent-burgundy);
            color: white;
            padding: 12px 25px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
            border: none;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--primary-dark);
            color: white;
            padding: 12px 25px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
        }

        .btn-secondary:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .status-box {
            background: var(--primary-light);
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
        }

        .price-summary {
            background: var(--primary-light);
            border-radius: 10px;
            padding: 20px;
        }

        .price-row {
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
            color: var(--primary-dark);
        }

        .price-total {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--accent-burgundy);
            border-top: 2px solid var(--accent-cream);
            padding-top: 15px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="nav-sidebar">
        <div class="sidebar-header">
            <h2>HOMES DB</h2>
            <p>Management Console</p>
        </div>
        <div class="nav-menu">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="reservations.php" class="nav-item active">
                <i class="fas fa-calendar-check"></i>
                <span>Reservations</span>
            </a>
            <a href="accommodations.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Accommodations</span>
            </a>
            <a href="users.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Users</span>
            </a>
            <a href="inquiries.php" class="nav-item">
                <i class="fas fa-envelope"></i>
                <span>Inquiries</span>
            </a>
            <a href="configuration.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Configuration</span>
            </a>
            <a href="analytics.php" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Analytics</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-panel">
        <div class="top-header">
            <div class="page-header">
                <h1>Reservation Details</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <a href="reservations.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Reservations</a>

        <div class="detail-grid">
            <div>
                <!-- Guest Information -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-user"></i> Guest Information
                    </div>
                    <div class="info-row">
                        <span class="info-label">Full Name:</span>
                        <span class="info-value"><?php echo htmlspecialchars($reservation['res_guest_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Email Address:</span>
                        <span class="info-value"><?php echo htmlspecialchars($reservation['res_guest_email']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Phone Number:</span>
                        <span class="info-value"><?php echo htmlspecialchars($reservation['res_guest_phone']); ?></span>
                    </div>
                </div>

                <!-- Property Information -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-home"></i> Property Details
                    </div>
                    <div class="info-row">
                        <span class="info-label">Property:</span>
                        <span class="info-value"><?php echo htmlspecialchars($reservation['res_property_name']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Location:</span>
                        <span class="info-value"><?php echo htmlspecialchars($reservation['res_location']); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Price/Night:</span>
                        <span class="info-value">$<?php echo number_format($reservation['res_price_per_night'], 2); ?></span>
                    </div>
                </div>

                <!-- Stay Details -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-calendar-alt"></i> Stay Information
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check-in Date:</span>
                        <span class="info-value"><?php echo date('F d, Y', strtotime($reservation['res_checkin'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Check-out Date:</span>
                        <span class="info-value"><?php echo date('F d, Y', strtotime($reservation['res_checkout'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Number of Nights:</span>
                        <span class="info-value"><?php echo $reservation['res_nights']; ?> nights</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Number of Guests:</span>
                        <span class="info-value"><?php echo $reservation['res_guests']; ?> guest(s)</span>
                    </div>
                </div>

                <!-- Special Requests -->
                <?php if(!empty($reservation['res_special_requests'])): ?>
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-comment"></i> Special Requests
                    </div>
                    <p style="line-height: 1.8;"><?php echo nl2br(htmlspecialchars($reservation['res_special_requests'])); ?></p>
                </div>
                <?php endif; ?>
            </div>

            <div>
                <!-- Status Card -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i> Status Information
                    </div>
                    
                    <div class="status-box">
                        <div style="margin-bottom: 20px;">
                            <div style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 5px;">Booking Reference</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-burgundy);"><?php echo $reservation['res_reference']; ?></div>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 5px;">Booking Status</div>
                            <span class="badge-large badge-<?php echo $reservation['res_status']; ?>">
                                <?php echo ucfirst($reservation['res_status']); ?>
                            </span>
                        </div>

                        <div style="margin-bottom: 15px;">
                            <div style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 5px;">Payment Status</div>
                            <span class="badge-large badge-<?php echo $reservation['res_payment_status']; ?>">
                                <?php echo ucfirst($reservation['res_payment_status']); ?>
                            </span>
                        </div>

                        <div>
                            <div style="font-size: 0.9rem; opacity: 0.7; margin-bottom: 5px;">Payment Method</div>
                            <div style="font-weight: 500;">
                                <?php 
                                $method = $reservation['res_payment_method'];
                                if($method == 'mobile_money') echo 'Mobile Money';
                                elseif($method == 'bank_transfer') echo 'Bank Transfer';
                                else echo 'Credit/Debit Card';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-calculator"></i> Price Summary
                    </div>
                    
                    <div class="price-summary">
                        <div class="price-row">
                            <span>$<?php echo number_format($reservation['res_price_per_night'], 2); ?> x <?php echo $reservation['res_nights']; ?> night(s)</span>
                            <span>$<?php echo number_format($reservation['res_subtotal'], 2); ?></span>
                        </div>
                        <div class="price-row">
                            <span>Service Fee</span>
                            <span>$<?php echo number_format($reservation['res_service_fee'], 2); ?></span>
                        </div>
                        <div class="price-row price-total">
                            <span>Total Amount</span>
                            <span>$<?php echo number_format($reservation['res_total'], 2); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Booking Date -->
                <div class="detail-card">
                    <div class="card-title">
                        <i class="fas fa-clock"></i> Booking Information
                    </div>
                    <div class="info-row">
                        <span class="info-label">Booked On:</span>
                        <span class="info-value"><?php echo date('F d, Y \a\t h:i A', strtotime($reservation['res_booking_date'])); ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Booking ID:</span>
                        <span class="info-value">#<?php echo $reservation['res_id']; ?></span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="edit-reservation.php?id=<?php echo $reservation['res_id']; ?>" class="btn-primary">
                        <i class="fas fa-edit"></i> Edit Reservation
                    </a>
                    <button onclick="window.print()" class="btn-secondary">
                        <i class="fas fa-print"></i> Print Details
                    </button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>