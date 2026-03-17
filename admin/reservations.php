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
if (isset($_POST['update_reservation'])) {
    $res_id = $_POST['reservation_id'];
    $status = $_POST['reservation_status'];
    $payment = $_POST['payment_status'];
    
    db_query("UPDATE reservations SET res_status = ?, res_payment_status = ? WHERE res_id = ?", [$status, $payment, $res_id]);
    
    header("Location: reservations.php?updated=1");
    exit();
}

// Get all reservations
$all_reservations = db_query("SELECT * FROM reservations ORDER BY res_booking_date DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reservations · HOMES DB</title>
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
            flex-wrap: wrap;
            gap: 15px;
        }

        .card-head h2 {
            color: var(--primary-dark);
            font-size: 1.3rem;
        }

        .filter-bar {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 8px 15px;
            border: 1px solid var(--accent-cream);
            border-radius: 8px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 0.9rem;
            min-width: 150px;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-input {
            padding: 8px 15px;
            border: 1px solid var(--accent-cream);
            border-radius: 8px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-size: 0.9rem;
            width: 250px;
        }

        .search-btn {
            background: var(--accent-burgundy);
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.25s;
        }

        .search-btn:hover {
            background: #9f4545;
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
            font-size: 0.9rem;
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid rgba(247,230,208,0.5);
            color: var(--primary-dark);
            font-size: 0.9rem;
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

        .badge-completed {
            background: #cce5ff;
            color: #004085;
        }

        .badge-paid {
            background: #d4edda;
            color: #155724;
        }

        .badge-failed {
            background: #f8d7da;
            color: #721c24;
        }

        .action-button {
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.85rem;
            margin: 0 3px;
            display: inline-block;
            border: none;
            cursor: pointer;
        }

        .btn-detail {
            background: var(--primary-dark);
            color: white;
        }

        .btn-modify {
            background: var(--accent-burgundy);
            color: white;
        }

        .btn-edit {
            background: #17a2b8;
            color: white;
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

        .pagination {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .pagination button {
            padding: 8px 12px;
            border: 1px solid var(--accent-cream);
            background: var(--pure-white);
            color: var(--primary-dark);
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.25s;
        }

        .pagination button:hover {
            background: var(--accent-burgundy);
            color: white;
        }

        .export-btns {
            display: flex;
            gap: 10px;
        }

        .export-btn {
            background: var(--primary-dark);
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .export-btn:hover {
            background: var(--accent-burgundy);
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
                <h1>Manage Reservations</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <?php if(isset($_GET['updated'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Reservation status updated successfully!
        </div>
        <?php endif; ?>

        <div class="data-card">
            <div class="card-head">
                <h2>All Reservations</h2>
                <div class="export-btns">
                    <a href="export.php?type=csv" class="export-btn"><i class="fas fa-file-csv"></i> CSV</a>
                    <a href="export.php?type=pdf" class="export-btn"><i class="fas fa-file-pdf"></i> PDF</a>
                </div>
            </div>
            
            <div class="filter-bar">
                <select class="filter-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="completed">Completed</option>
                </select>
                <select class="filter-select" id="paymentFilter">
                    <option value="">All Payments</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                </select>
                <select class="filter-select" id="locationFilter">
                    <option value="">All Locations</option>
                    <option value="Mukono">Mukono</option>
                    <option value="Entebbe">Entebbe</option>
                    <option value="Jinja">Jinja</option>
                </select>
                <div class="search-box">
                    <input type="text" class="search-input" id="searchInput" placeholder="Search by guest, ref, email...">
                    <button class="search-btn" id="searchBtn"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div class="table-wrapper">
                <table id="reservationsTable">
                    <thead>
                        <tr>
                            <th>Reference</th>
                            <th>Guest Name</th>
                            <th>Property</th>
                            <th>Location</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Guests</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Booked On</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($all_reservations as $res): ?>
                        <tr>
                            <td><strong><?php echo $res['res_reference']; ?></strong></td>
                            <td><?php echo htmlspecialchars($res['res_guest_name']); ?></td>
                            <td><?php echo htmlspecialchars(substr($res['res_property_name'], 0, 20)) . '...'; ?></td>
                            <td><?php echo htmlspecialchars($res['res_location']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($res['res_checkin'])); ?></td>
                            <td><?php echo date('M d, Y', strtotime($res['res_checkout'])); ?></td>
                            <td><?php echo $res['res_guests']; ?></td>
                            <td>$<?php echo number_format($res['res_total'], 2); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $res['res_payment_status']; ?>">
                                    <?php echo ucfirst($res['res_payment_status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $res['res_status']; ?>">
                                    <?php echo ucfirst($res['res_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($res['res_booking_date'])); ?></td>
                            <td>
                                <a href="reservation-detail.php?id=<?php echo $res['res_id']; ?>" class="action-button btn-detail" title="View Details"><i class="fas fa-eye"></i></a>
                                <a href="edit-reservation.php?id=<?php echo $res['res_id']; ?>" class="action-button btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <button onclick="openStatusModal(<?php echo $res['res_id']; ?>, '<?php echo $res['res_status']; ?>', '<?php echo $res['res_payment_status']; ?>')" class="action-button btn-modify" title="Update Status"><i class="fas fa-sync-alt"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($all_reservations)): ?>
                        <tr>
                            <td colspan="12" style="text-align: center; padding: 40px;">
                                <i class="fas fa-calendar-times" style="font-size: 3rem; color: var(--accent-cream); margin-bottom: 15px;"></i>
                                <p>No reservations found</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <button><i class="fas fa-chevron-left"></i></button>
                <button>1</button>
                <button>2</button>
                <button>3</button>
                <button><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: white; padding: 30px; border-radius: 15px; width: 400px; max-width: 90%;">
            <h3 style="color: var(--primary-dark); margin-bottom: 20px;">Update Reservation Status</h3>
            <form method="POST" action="">
                <input type="hidden" name="reservation_id" id="modal_res_id">
                
                <div style="margin-bottom: 15px;">
                    <label style="display: block; margin-bottom: 5px; color: var(--primary-dark);">Reservation Status</label>
                    <select name="reservation_status" id="modal_status" style="width: 100%; padding: 10px; border: 1px solid var(--accent-cream); border-radius: 8px;">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 5px; color: var(--primary-dark);">Payment Status</label>
                    <select name="payment_status" id="modal_payment" style="width: 100%; padding: 10px; border: 1px solid var(--accent-cream); border-radius: 8px;">
                        <option value="pending">Pending</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" onclick="closeStatusModal()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer;">Cancel</button>
                    <button type="submit" name="update_reservation" style="padding: 10px 20px; background: var(--accent-burgundy); color: white; border: none; border-radius: 8px; cursor: pointer;">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStatusModal(id, status, payment) {
            document.getElementById('modal_res_id').value = id;
            document.getElementById('modal_status').value = status;
            document.getElementById('modal_payment').value = payment;
            document.getElementById('statusModal').style.display = 'flex';
        }

        function closeStatusModal() {
            document.getElementById('statusModal').style.display = 'none';
        }

        // Filter functionality
        document.getElementById('statusFilter').addEventListener('change', filterTable);
        document.getElementById('paymentFilter').addEventListener('change', filterTable);
        document.getElementById('locationFilter').addEventListener('change', filterTable);
        document.getElementById('searchBtn').addEventListener('click', filterTable);
        document.getElementById('searchInput').addEventListener('keyup', function(e) {
            if(e.key === 'Enter') filterTable();
        });

        function filterTable() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const payment = document.getElementById('paymentFilter').value.toLowerCase();
            const location = document.getElementById('locationFilter').value.toLowerCase();
            const search = document.getElementById('searchInput').value.toLowerCase();
            
            const rows = document.querySelectorAll('#reservationsTable tbody tr');
            
            rows.forEach(row => {
                if(row.cells.length < 2) return; // Skip empty rows
                
                const rowStatus = row.cells[9]?.innerText.toLowerCase().trim() || '';
                const rowPayment = row.cells[8]?.innerText.toLowerCase().trim() || '';
                const rowLocation = row.cells[3]?.innerText.toLowerCase().trim() || '';
                const rowRef = row.cells[0]?.innerText.toLowerCase() || '';
                const rowGuest = row.cells[1]?.innerText.toLowerCase() || '';
                const rowProperty = row.cells[2]?.innerText.toLowerCase() || '';
                
                const matchesStatus = !status || rowStatus.includes(status);
                const matchesPayment = !payment || rowPayment.includes(payment);
                const matchesLocation = !location || rowLocation.includes(location);
                const matchesSearch = !search || 
                    rowRef.includes(search) || 
                    rowGuest.includes(search) || 
                    rowProperty.includes(search);
                
                if(matchesStatus && matchesPayment && matchesLocation && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('statusModal');
            if (event.target == modal) {
                closeStatusModal();
            }
        }
    </script>
</body>
</html>