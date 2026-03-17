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

// Get all settings
$settings = [];
$result = db_query("SELECT * FROM configuration");
while($row = $result->fetch()) {
    $settings[$row['config_key']] = $row['config_value'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach($_POST as $key => $value) {
        if($key != 'submit') {
            db_query("UPDATE configuration SET config_value = ? WHERE config_key = ?", [$value, $key]);
        }
    }
    header("Location: configuration.php?updated=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuration · HOMES DB</title>
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

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }

        .settings-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 25px;
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

        .form-group {
            margin-bottom: 20px;
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
            margin-top: 20px;
        }

        .btn-primary:hover {
            background: #9f4545;
            transform: translateY(-2px);
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
            <a href="inquiries.php" class="nav-item">
                <i class="fas fa-envelope"></i>
                <span>Inquiries</span>
            </a>
            <a href="configuration.php" class="nav-item active">
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
                <h1>Site Configuration</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <?php if(isset($_GET['updated'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Settings updated successfully!
        </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="settings-grid">
                <!-- Site Information -->
                <div class="settings-card">
                    <div class="card-title">
                        <i class="fas fa-globe"></i> Site Information
                    </div>
                    <div class="form-group">
                        <label>Site Title</label>
                        <input type="text" name="site_title" class="form-control" value="<?php echo htmlspecialchars($settings['site_title'] ?? 'HOMES DB'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo htmlspecialchars($settings['contact_email'] ?? 'info@homes.com'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? '+256 700 000000'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Contact Address</label>
                        <input type="text" name="contact_address" class="form-control" value="<?php echo htmlspecialchars($settings['contact_address'] ?? 'Kampala, Uganda'); ?>">
                    </div>
                </div>

                <!-- Mobile Money Settings -->
                <div class="settings-card">
                    <div class="card-title">
                        <i class="fas fa-mobile-alt"></i> Mobile Money
                    </div>
                    <div class="form-group">
                        <label>Mobile Money Number</label>
                        <input type="text" name="mobile_money_number" class="form-control" value="<?php echo htmlspecialchars($settings['mobile_money_number'] ?? '+256 700 000000'); ?>">
                    </div>
                </div>

                <!-- Bank Transfer Settings -->
                <div class="settings-card">
                    <div class="card-title">
                        <i class="fas fa-university"></i> Bank Transfer
                    </div>
                    <div class="form-group">
                        <label>Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="<?php echo htmlspecialchars($settings['bank_name'] ?? 'Stanbic Bank Uganda'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Account Name</label>
                        <input type="text" name="bank_account_name" class="form-control" value="<?php echo htmlspecialchars($settings['bank_account_name'] ?? 'HOMES DB LTD'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Account Number</label>
                        <input type="text" name="bank_account_number" class="form-control" value="<?php echo htmlspecialchars($settings['bank_account_number'] ?? '9030012345678'); ?>">
                    </div>
                    <div class="form-group">
                        <label>Branch</label>
                        <input type="text" name="bank_branch" class="form-control" value="<?php echo htmlspecialchars($settings['bank_branch'] ?? 'Kampala Main'); ?>">
                    </div>
                    <div class="form-group">
                        <label>SWIFT Code</label>
                        <input type="text" name="bank_swift_code" class="form-control" value="<?php echo htmlspecialchars($settings['bank_swift_code'] ?? 'SBICUGKX'); ?>">
                    </div>
                </div>

                <!-- Fees & Charges -->
                <div class="settings-card">
                    <div class="card-title">
                        <i class="fas fa-dollar-sign"></i> Fees & Charges
                    </div>
                    <div class="form-group">
                        <label>Service Fee ($)</label>
                        <input type="number" name="service_fee_amount" class="form-control" step="0.01" value="<?php echo htmlspecialchars($settings['service_fee_amount'] ?? '15'); ?>">
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn-primary"><i class="fas fa-save"></i> Save All Settings</button>
        </form>
    </div>
</body>
</html>