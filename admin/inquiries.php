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

// Handle status update
if (isset($_POST['update_status'])) {
    $inquiry_id = $_POST['inquiry_id'];
    $status = $_POST['status'];
    db_query("UPDATE inquiries SET inquiry_status = ? WHERE inquiry_id = ?", [$status, $inquiry_id]);
    header("Location: inquiries.php?updated=1");
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $inquiry_id = $_GET['delete'];
    db_query("DELETE FROM inquiries WHERE inquiry_id = ?", [$inquiry_id]);
    header("Location: inquiries.php?deleted=1");
    exit();
}

// Get all inquiries
$inquiries = db_query("SELECT * FROM inquiries ORDER BY inquiry_created_at DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries · HOMES DB</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* Copy styles from users.php and add/modify: */
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
        .inquiry-message {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .btn-view {
            background: var(--primary-dark);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Sidebar (same as users.php) -->
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
                <h1>Contact Inquiries</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <?php if(isset($_GET['updated'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Inquiry status updated!
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['deleted'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Inquiry deleted!
        </div>
        <?php endif; ?>

        <div class="data-card">
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($inquiries as $inq): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($inq['inquiry_created_at'])); ?></td>
                            <td><?php echo htmlspecialchars($inq['inquiry_name']); ?></td>
                            <td><?php echo htmlspecialchars($inq['inquiry_email']); ?></td>
                            <td><?php echo htmlspecialchars($inq['inquiry_phone'] ?: 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($inq['inquiry_subject'] ?: 'No Subject'); ?></td>
                            <td class="inquiry-message" title="<?php echo htmlspecialchars($inq['inquiry_message']); ?>">
                                <?php echo htmlspecialchars(substr($inq['inquiry_message'], 0, 50)) . '...'; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $inq['inquiry_status']; ?>">
                                    <?php echo ucfirst($inq['inquiry_status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view-inquiry.php?id=<?php echo $inq['inquiry_id']; ?>" class="action-button btn-view"><i class="fas fa-eye"></i></a>
                                <form method="POST" style="display: inline-block;">
                                    <input type="hidden" name="inquiry_id" value="<?php echo $inq['inquiry_id']; ?>">
                                    <select name="status" onchange="this.form.submit()" style="padding: 5px; border-radius: 4px;">
                                        <option value="new" <?php echo $inq['inquiry_status'] == 'new' ? 'selected' : ''; ?>>New</option>
                                        <option value="read" <?php echo $inq['inquiry_status'] == 'read' ? 'selected' : ''; ?>>Read</option>
                                        <option value="replied" <?php echo $inq['inquiry_status'] == 'replied' ? 'selected' : ''; ?>>Replied</option>
                                    </select>
                                    <input type="hidden" name="update_status">
                                </form>
                                <a href="?delete=<?php echo $inq['inquiry_id']; ?>" class="action-button btn-delete" onclick="return confirm('Delete this inquiry?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($inquiries)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px;">
                                <i class="fas fa-envelope-open" style="font-size: 3rem; color: var(--accent-cream); margin-bottom: 15px;"></i>
                                <p>No inquiries yet</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>