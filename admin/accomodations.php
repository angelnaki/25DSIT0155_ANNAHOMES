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

// Handle delete
if (isset($_GET['delete'])) {
    $accom_id = $_GET['delete'];
    db_query("DELETE FROM accommodations WHERE accom_id = ?", [$accom_id]);
    header("Location: accommodations.php?deleted=1");
    exit();
}

// Get all accommodations
$accommodations = db_query("SELECT * FROM accommodations ORDER BY accom_location, accom_name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Accommodations · HOMES DB</title>
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

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .add-btn {
            background: var(--accent-burgundy);
            color: white;
            padding: 12px 25px;
            border-radius: 40px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.25s;
        }

        .add-btn:hover {
            background: #9f4545;
            transform: translateY(-2px);
        }

        .filter-section {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-select {
            padding: 10px 15px;
            border: 1px solid var(--accent-cream);
            border-radius: 8px;
            background: var(--primary-light);
            color: var(--primary-dark);
            min-width: 150px;
        }

        .data-card {
            background: var(--pure-white);
            border-radius: 15px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--accent-cream);
            padding: 25px;
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

        .property-image {
            width: 60px;
            height: 60px;
            background: var(--accent-cream);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--accent-burgundy);
        }

        .badge {
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
        }

        .badge-featured {
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

        .btn-view {
            background: var(--primary-dark);
            color: white;
        }

        .btn-edit {
            background: #17a2b8;
            color: white;
        }

        .btn-delete {
            background: #dc3545;
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

        .rating-stars {
            color: #FFD700;
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
            <a href="accommodations.php" class="nav-item active">
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
                <h1>Manage Accommodations</h1>
            </div>
            <div class="user-profile">
                <span><i class="fas fa-user-circle" style="color: var(--accent-burgundy);"></i> <?php echo $_SESSION['username']; ?></span>
                <a href="logout.php" class="logout-link"><i class="fas fa-sign-out-alt"></i> Exit</a>
            </div>
        </div>

        <?php if(isset($_GET['deleted'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Accommodation deleted successfully!
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['added'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> New accommodation added successfully!
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['updated'])): ?>
        <div class="success-message">
            <i class="fas fa-check-circle"></i> Accommodation updated successfully!
        </div>
        <?php endif; ?>

        <div class="action-bar">
            <a href="add-accommodation.php" class="add-btn"><i class="fas fa-plus"></i> Add New Property</a>
            <div class="filter-section">
                <select class="filter-select" id="locationFilter">
                    <option value="">All Locations</option>
                    <option value="Mukono">Mukono</option>
                    <option value="Entebbe">Entebbe</option>
                    <option value="Jinja">Jinja</option>
                </select>
                <select class="filter-select" id="priceFilter">
                    <option value="">Sort by Price</option>
                    <option value="low-high">Low to High</option>
                    <option value="high-low">High to Low</option>
                </select>
            </div>
        </div>

        <div class="data-card">
            <div class="table-wrapper">
                <table id="accommodationsTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Property Name</th>
                            <th>Location</th>
                            <th>Price/Night</th>
                            <th>Bedrooms</th>
                            <th>Max Guests</th>
                            <th>Rating</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($accommodations as $acc): ?>
                        <tr>
                            <td>
                                <div class="property-image">
                                    <i class="fas fa-home"></i>
                                </div>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($acc['accom_name']); ?></strong><br>
                                <small style="color: #666;"><?php echo htmlspecialchars($acc['accom_slug']); ?></small>
                            </td>
                            <td><?php echo htmlspecialchars($acc['accom_location']); ?></td>
                            <td><strong>$<?php echo $acc['accom_price']; ?></strong></td>
                            <td><?php echo $acc['accom_bedrooms']; ?></td>
                            <td><?php echo $acc['accom_max_guests']; ?></td>
                            <td>
                                <div class="rating-stars">
                                    <?php 
                                    $rating = $acc['accom_rating'];
                                    for($i = 1; $i <= 5; $i++) {
                                        if($i <= $rating) {
                                            echo '<i class="fas fa-star"></i>';
                                        } elseif($i - 0.5 <= $rating) {
                                            echo '<i class="fas fa-star-half-alt"></i>';
                                        } else {
                                            echo '<i class="far fa-star"></i>';
                                        }
                                    }
                                    ?>
                                    (<?php echo $acc['accom_reviews']; ?>)
                                </div>
                            </td>
                            <td>
                                <?php if($acc['accom_featured']): ?>
                                    <span class="badge badge-featured"><i class="fas fa-star"></i> Featured</span>
                                <?php else: ?>
                                    <span style="opacity: 0.5;">No</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit-accommodation.php?id=<?php echo $acc['accom_id']; ?>" class="action-button btn-edit" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="view-accommodation.php?id=<?php echo $acc['accom_id']; ?>" class="action-button btn-view" title="View"><i class="fas fa-eye"></i></a>
                                <a href="?delete=<?php echo $acc['accom_id']; ?>" class="action-button btn-delete" title="Delete" onclick="return confirm('Are you sure you want to delete this property?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($accommodations)): ?>
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px;">
                                <i class="fas fa-home" style="font-size: 3rem; color: var(--accent-cream); margin-bottom: 15px;"></i>
                                <p>No accommodations found. Click "Add New Property" to get started.</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('locationFilter').addEventListener('change', filterTable);
        document.getElementById('priceFilter').addEventListener('change', filterTable);

        function filterTable() {
            const location = document.getElementById('locationFilter').value.toLowerCase();
            const priceSort = document.getElementById('priceFilter').value;
            const tbody = document.querySelector('#accommodationsTable tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));

            // Filter by location
            rows.forEach(row => {
                if(row.cells.length < 3) return;
                const rowLocation = row.cells[2]?.innerText.toLowerCase() || '';
                
                if(!location || rowLocation.includes(location)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });

            // Sort by price
            if(priceSort) {
                const visibleRows = rows.filter(row => row.style.display !== 'none');
                visibleRows.sort((a, b) => {
                    const priceA = parseFloat(a.cells[3]?.innerText.replace('$', '') || 0);
                    const priceB = parseFloat(b.cells[3]?.innerText.replace('$', '') || 0);
                    
                    if(priceSort === 'low-high') {
                        return priceA - priceB;
                    } else {
                        return priceB - priceA;
                    }
                });

                // Reappend sorted rows
                visibleRows.forEach(row => tbody.appendChild(row));
            }
        }
    </script>
</body>
</html>