<?php
session_start();
require_once '../db_config.php';

// Admin authentication check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Verify admin role
$user_check = db_query("SELECT user_role FROM users WHERE user_id = ?", [$_SESSION['user_id']]);
$current_user = $user_check->fetch();

if (!$current_user || $current_user['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get dashboard statistics
$total_reservations = db_query("SELECT COUNT(*) FROM reservations")->fetchColumn();
$pending_reservations = db_query("SELECT COUNT(*) FROM reservations WHERE res_status = 'pending'")->fetchColumn();
$confirmed_reservations = db_query("SELECT COUNT(*) FROM reservations WHERE res_status = 'confirmed'")->fetchColumn();
$total_income = db_query("SELECT SUM(res_total) FROM reservations WHERE res_payment_status = 'paid'")->fetchColumn();
$total_users = db_query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_accommodations = db_query("SELECT COUNT(*) FROM accommodations")->fetchColumn();

$recent_reservations = db_query("SELECT * FROM reservations ORDER BY res_booking_date DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard · HOMES DB</title>
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

        /* Sidebar Navigation */
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

        /* Stats Cards Grid */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-block {
            background: var(--pure-white);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            transition: transform 0.25s;
        }

        .stat-block:hover {
            transform: translateY(-5px);
        }

        .stat-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-head i {
            font-size: 2rem;
            color: var(--accent-burgundy);
        }

        .stat-label {
            color: var(--primary-dark);
            font-size: 0.9rem;
            opacity: 0.7;
        }

        .stat-number {
            color: var(--primary-dark);
            font-size: 2rem;
            font-weight: 600;
        }

        .stat-note {
            color: var(--accent-burgundy);
            font-size: 0.85rem;
            margin-top: 5px;
        }

        /* Data Cards */
        .data-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 25px;
            margin-bottom: 30px;
        }

        .card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-head h2 {
            color: var(--primary-dark);
            font-size: 1.3rem;
        }

        .view-link {
            color: var(--accent-burgundy);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 10px;
            color: var(--primary-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--accent-cream);
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(247,230,208,0.5);
            color: var(--primary-dark);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
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

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .action-button {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            margin: 0 3px;
            display: inline-block;
        }

        .btn-detail {
            background: var(--primary-dark);
            color: white;
        }

        .btn-modify {
            background: var(--accent-burgundy);
            color: white;
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
            <a href="dashboard.php" class="nav-item active">
                <i class="fas fa-dashboard"></i>
                <span>Dashboard</span>
            </a>
            <a href="reservations.php" class="nav-item">
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
                <h1>Dashboard Overview</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Total Reservations</span>
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-number"><?php echo $total_reservations; ?></div>
                <div class="stat-note">All time bookings</div>
            </div>

            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Pending</span>
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-number"><?php echo $pending_reservations; ?></div>
                <div class="stat-note">Awaiting confirmation</div>
            </div>

            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Confirmed</span>
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-number"><?php echo $confirmed_reservations; ?></div>
                <div class="stat-note">Active stays</div>
            </div>

            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Revenue</span>
                    <i class="fas fa-dollar-sign"></i>
                </div>
                <div class="stat-number">$<?php echo number_format($total_income ?? 0, 2); ?></div>
                <div class="stat-note">Paid reservations</div>
            </div>

            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Users</span>
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-number"><?php echo $total_users; ?></div>
                <div class="stat-note">Registered members</div>
            </div>

            <div class="stat-block">
                <div class="stat-head">
                    <span class="stat-label">Properties</span>
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-number"><?php echo $total_accommodations; ?></div>
                <div class="stat-note">Active listings</div>
            </div>
        </div>

        <!-- Recent Reservations -->
        <div class="data-card">
            <div class="card-head">
                <h2>Recent Reservations</h2>
                <a href="reservations.php" class="view-link">View All <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Guest</th>
                            <th>Property</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_reservations as $res): ?>
                        <tr>
                            <td><strong><?php echo $res['res_reference']; ?></strong></td>
                            <td><?php echo $res['res_guest_name']; ?></td>
                            <td><?php echo substr($res['res_property_name'], 0, 20); ?>...</td>
                            <td><?php echo date('M d, Y', strtotime($res['res_checkin'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($res['res_checkout'])); ?></td>
                            <td>$<?php echo $res['res_total']; ?></td>
                            <td>
                                <span class="badge <?php echo $res['res_payment_status'] == 'paid' ? 'badge-paid' : 'badge-pending'; ?>">
                                    <?php echo ucfirst($res['res_payment_status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $res['res_status']; ?>">
                                    <?php echo ucfirst($res['res_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="reservation-detail.php?id=<?php echo $res['res_id']; ?>" class="action-button btn-detail"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($recent_reservations)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 30px;">No reservations found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>