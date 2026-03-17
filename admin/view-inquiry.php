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

$inquiry_id = isset($_GET['id']) ? $_GET['id'] : 0;

// Get inquiry details
$inquiry = db_query("SELECT * FROM inquiries WHERE inquiry_id = ?", [$inquiry_id])->fetch();

if (!$inquiry) {
    header("Location: inquiries.php");
    exit();
}

// Mark as read if it's new
if ($inquiry['inquiry_status'] == 'new') {
    db_query("UPDATE inquiries SET inquiry_status = 'read' WHERE inquiry_id = ?", [$inquiry_id]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inquiry · HOMES DB</title>
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

        .inquiry-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 30px;
        }

        .inquiry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--accent-cream);
        }

        .inquiry-subject {
            font-size: 1.5rem;
            color: var(--primary-dark);
            font-weight: 600;
        }

        .inquiry-date {
            color: var(--primary-dark);
            opacity: 0.7;
        }

        .inquiry-meta {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
            padding: 20px;
            background: var(--primary-light);
            border-radius: 10px;
        }

        .meta-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .meta-label {
            font-size: 0.85rem;
            opacity: 0.7;
            color: var(--primary-dark);
        }

        .meta-value {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-dark);
        }

        .inquiry-message-box {
            margin-top: 30px;
        }

        .message-label {
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-dark);
            margin-bottom: 15px;
        }

        .message-content {
            background: var(--primary-light);
            padding: 25px;
            border-radius: 10px;
            line-height: 1.8;
            color: var(--primary-dark);
            white-space: pre-wrap;
        }

        .action-buttons {
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
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
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.25s;
        }

        .btn-secondary:hover {
            background: #2c3e50;
            transform: translateY(-2px);
        }

        .badge {
            padding: 5px 15px;
            border-radius: 40px;
            font-size: 0.9rem;
            font-weight: 500;
            display: inline-block;
        }

        .badge-new {
            background: #dc3545;
            color: white;
        }
        .badge-read {
            background: #ffc107;
            color: #212529;
        }
        .badge-replied {
            background: #28a745;
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
            <a href="dashboard.php" class="nav-item">
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
            <a href="inquiries.php" class="nav-item active">
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
                <h1>View Inquiry</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <a href="inquiries.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Inquiries</a>

        <div class="inquiry-card">
            <div class="inquiry-header">
                <div>
                    <span class="badge badge-<?php echo $inquiry['inquiry_status']; ?>">
                        <?php echo ucfirst($inquiry['inquiry_status']); ?>
                    </span>
                </div>
                <div class="inquiry-date">
                    <i class="far fa-calendar-alt"></i> 
                    <?php echo date('F d, Y \a\t h:i A', strtotime($inquiry['inquiry_created_at'])); ?>
                </div>
            </div>

            <div class="inquiry-subject">
                <?php echo htmlspecialchars($inquiry['inquiry_subject'] ?: 'No Subject'); ?>
            </div>

            <div class="inquiry-meta">
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-user"></i> Name</span>
                    <span class="meta-value"><?php echo htmlspecialchars($inquiry['inquiry_name']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-envelope"></i> Email</span>
                    <span class="meta-value"><?php echo htmlspecialchars($inquiry['inquiry_email']); ?></span>
                </div>
                <div class="meta-item">
                    <span class="meta-label"><i class="fas fa-phone"></i> Phone</span>
                    <span class="meta-value"><?php echo htmlspecialchars($inquiry['inquiry_phone'] ?: 'Not provided'); ?></span>
                </div>
            </div>

            <div class="inquiry-message-box">
                <div class="message-label">Message:</div>
                <div class="message-content">
                    <?php echo nl2br(htmlspecialchars($inquiry['inquiry_message'])); ?>
                </div>
            </div>

            <div class="action-buttons">
                <a href="mailto:<?php echo $inquiry['inquiry_email']; ?>" class="btn-primary">
                    <i class="fas fa-reply"></i> Reply via Email
                </a>
                <a href="inquiries.php" class="btn-secondary">
                    <i class="fas fa-times"></i> Close
                </a>
            </div>
        </div>
    </div>
</body>
</html>