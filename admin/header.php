<?php
require_once __DIR__ . '/auth.php';
checkAdminAuth();

// Get unread counts for notifications
global $pdo;
$pending_bookings = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$total_bookings_today = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(booking_date) = CURDATE()")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard · ANNA HOMES</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        :root {
            --deep-navy: #1A2B3C;
            --soft-white: #F9F7F4;
            --champagne: #F7E6D0;
            --burgundy: #8B3A3A;
            --pure-white: #FFFFFF;
            --shadow: 0 4px 20px rgba(0,0,0,0.08);
            --sidebar-width: 280px;
        }

        body {
            background-color: #f5f5f5;
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--deep-navy);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(247,230,208,0.2);
        }

        .sidebar-header h2 {
            color: var(--champagne);
            font-style: italic;
            font-size: 1.8rem;
        }

        .sidebar-header p {
            color: var(--soft-white);
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 5px;
        }

        .admin-info {
            padding: 20px;
            background: rgba(247,230,208,0.1);
            margin: 15px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            border: 1px solid rgba(247,230,208,0.2);
        }

        .admin-avatar {
            width: 45px;
            height: 45px;
            background: var(--burgundy);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .admin-details h4 {
            font-size: 1rem;
            margin-bottom: 3px;
        }

        .admin-details span {
            font-size: 0.8rem;
            opacity: 0.7;
        }

        .nav-menu {
            padding: 15px;
        }

        .nav-item {
            list-style: none;
            margin-bottom: 5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--soft-white);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            gap: 12px;
        }

        .nav-link:hover, .nav-link.active {
            background: var(--burgundy);
            color: white;
        }

        .nav-link i {
            width: 22px;
            font-size: 1.1rem;
        }

        .nav-link .badge {
            background: var(--champagne);
            color: var(--deep-navy);
            padding: 2px 8px;
            border-radius: 30px;
            font-size: 0.7rem;
            margin-left: auto;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: var(--sidebar-width);
            padding: 20px 30px;
        }

        .top-bar {
            background: white;
            padding: 15px 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 1.5rem;
            color: var(--deep-navy);
            font-weight: 500;
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .notification-badge {
            position: relative;
            cursor: pointer;
        }

        .notification-badge i {
            font-size: 1.3rem;
            color: var(--deep-navy);
        }

        .badge-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: var(--burgundy);
            color: white;
            font-size: 0.6rem;
            padding: 2px 5px;
            border-radius: 10px;
        }

        .logout-btn {
            background: var(--burgundy);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 15px;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            background: rgba(139,58,58,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--burgundy);
            font-size: 1.8rem;
        }

        .stat-content h3 {
            font-size: 1.8rem;
            color: var(--deep-navy);
            margin-bottom: 5px;
        }

        .stat-content p {
            color: #666;
            font-size: 0.9rem;
        }

        /* Tables */
        .table-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow);
            margin-top: 20px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .table-header h2 {
            color: var(--deep-navy);
            font-size: 1.3rem;
        }

        .table-header .btn {
            background: var(--burgundy);
            color: white;
            padding: 8px 16px;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.3s;
        }

        .table-header .btn:hover {
            background: #9f4545;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 15px 10px;
            color: var(--deep-navy);
            font-weight: 600;
            border-bottom: 2px solid var(--champagne);
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid #eee;
        }

        tr:hover {
            background: rgba(247,230,208,0.2);
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 30px;
            font-size: 0.8rem;
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

        .action-btn {
            background: none;
            border: none;
            color: var(--burgundy);
            cursor: pointer;
            margin: 0 5px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .action-btn:hover {
            color: var(--deep-navy);
            transform: scale(1.1);
        }

        /* Forms */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--deep-navy);
            font-weight: 500;
        }

        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid var(--champagne);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--burgundy);
            box-shadow: 0 0 0 3px rgba(139,58,58,0.1);
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: var(--deep-navy);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                opacity: 0;
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>ANNA HOMES</h2>
            <p>Admin Dashboard</p>
        </div>
        
        <div class="admin-info">
            <div class="admin-avatar">
                <i class="fas fa-user-cog"></i>
            </div>
            <div class="admin-details">
                <h4><?php echo $_SESSION['username']; ?></h4>
                <span>Administrator</span>
            </div>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="fas fa-dashboard"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="bookings.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'bookings') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-check"></i> Bookings
                    <?php if($pending_bookings > 0): ?>
                        <span class="badge"><?php echo $pending_bookings; ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="properties.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'properties') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i> Properties
                </a>
            </li>
            <li class="nav-item">
                <a href="users.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'users') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-users"></i> Users
                </a>
            </li>
            <li class="nav-item">
                <a href="reports.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'reports') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-chart-line"></i> Reports
                </a>
            </li>
            <li class="nav-item">
                <a href="settings.php" class="nav-link <?php echo strpos($_SERVER['PHP_SELF'], 'settings') !== false ? 'active' : ''; ?>">
                    <i class="fas fa-cog"></i> Settings
                </a>
            </li>
        </ul>
    </div>
    
    <!-- Main Content -->
    <div class="main-content">
        <div class="top-bar">
            <div class="page-title">
                <?php
                $page = basename($_SERVER['PHP_SELF'], '.php');
                echo ucfirst($page == 'index' ? 'Dashboard' : $page);
                ?>
            </div>
            <div class="top-bar-actions">
                <div class="notification-badge">
                    <i class="far fa-bell"></i>
                    <?php if($pending_bookings > 0): ?>
                        <span class="badge-count"><?php echo $pending_bookings; ?></span>
                    <?php endif; ?>
                </div>
                <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>