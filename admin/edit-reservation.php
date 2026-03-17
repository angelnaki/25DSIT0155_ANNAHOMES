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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guest_name = $_POST['guest_name'];
    $guest_email = $_POST['guest_email'];
    $guest_phone = $_POST['guest_phone'];
    $checkin = $_POST['checkin'];
    $checkout = $_POST['checkout'];
    $guests = $_POST['guests'];
    $status = $_POST['status'];
    $payment_status = $_POST['payment_status'];
    
    // Recalculate nights and total
    $checkin_date = new DateTime($checkin);
    $checkout_date = new DateTime($checkout);
    $nights = $checkin_date->diff($checkout_date)->days;
    $subtotal = $nights * $reservation['res_price_per_night'];
    $total = $subtotal + $reservation['res_service_fee'];
    
    db_query("UPDATE reservations SET 
        res_guest_name = ?, res_guest_email = ?, res_guest_phone = ?,
        res_checkin = ?, res_checkout = ?, res_guests = ?, res_nights = ?,
        res_subtotal = ?, res_total = ?, res_status = ?, res_payment_status = ?
        WHERE res_id = ?", 
        [$guest_name, $guest_email, $guest_phone, $checkin, $checkout, $guests, 
         $nights, $subtotal, $total, $status, $payment_status, $res_id]);
    
    header("Location: reservation-detail.php?id=$res_id&updated=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reservation · HOMES DB</title>
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

        .form-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 30px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: span 2;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary-dark);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--accent-cream);
            border-radius: 8px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 0.95rem;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-burgundy);
        }

        .readonly-field {
            background: var(--accent-cream);
            opacity: 0.8;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid var(--accent-cream);
        }

        .btn-primary {
            background: var(--accent-burgundy);
            color: white;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.25s;
        }

        .btn-primary:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: var(--primary-dark);
            color: white;
            padding: 12px 30px;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.25s;
        }

        .btn-secondary:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .info-box {
            background: var(--primary-light);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            border-left: 4px solid var(--accent-burgundy);
        }

        .info-box h3 {
            color: var(--primary-dark);
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .info-box p {
            color: var(--primary-dark);
            opacity: 0.8;
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
                <h1>Edit Reservation</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <a href="reservation-detail.php?id=<?php echo $res_id; ?>" class="back-link"><i class="fas fa-arrow-left"></i> Back to Details</a>

        <div class="form-card">
            <div class="info-box">
                <h3><i class="fas fa-info-circle"></i> Reservation: <?php echo $reservation['res_reference']; ?></h3>
                <p>Property: <?php echo htmlspecialchars($reservation['res_property_name']); ?> | Location: <?php echo $reservation['res_location']; ?> | Price per night: $<?php echo $reservation['res_price_per_night']; ?></p>
            </div>

            <form method="POST" action="">
                <div class="form-grid">
                    <div class="form-group">
                        <label>Guest Full Name *</label>
                        <input type="text" name="guest_name" class="form-control" value="<?php echo htmlspecialchars($reservation['res_guest_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Guest Email *</label>
                        <input type="email" name="guest_email" class="form-control" value="<?php echo htmlspecialchars($reservation['res_guest_email']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Guest Phone *</label>
                        <input type="text" name="guest_phone" class="form-control" value="<?php echo htmlspecialchars($reservation['res_guest_phone']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Number of Guests *</label>
                        <input type="number" name="guests" class="form-control" value="<?php echo $reservation['res_guests']; ?>" min="1" required>
                    </div>

                    <div class="form-group">
                        <label>Check-in Date *</label>
                        <input type="date" name="checkin" class="form-control" value="<?php echo $reservation['res_checkin']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Check-out Date *</label>
                        <input type="date" name="checkout" class="form-control" value="<?php echo $reservation['res_checkout']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Booking Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" <?php echo $reservation['res_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="confirmed" <?php echo $reservation['res_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                            <option value="cancelled" <?php echo $reservation['res_status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            <option value="completed" <?php echo $reservation['res_status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Payment Status *</label>
                        <select name="payment_status" class="form-control" required>
                            <option value="pending" <?php echo $reservation['res_payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="paid" <?php echo $reservation['res_payment_status'] == 'paid' ? 'selected' : ''; ?>>Paid</option>
                            <option value="failed" <?php echo $reservation['res_payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label>Payment Method</label>
                        <input type="text" class="form-control readonly-field" value="<?php echo ucfirst(str_replace('_', ' ', $reservation['res_payment_method'])); ?>" readonly>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-primary"><i class="fas fa-save"></i> Update Reservation</button>
                    <a href="reservation-detail.php?id=<?php echo $res_id; ?>" class="btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>