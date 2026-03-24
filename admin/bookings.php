<?php
require_once 'includes/header.php';

// Handle status update
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE bookings SET status = ?, payment_status = ? WHERE id = ?");
    $stmt->execute([$new_status, $new_status, $booking_id]);
    
    header("Location: bookings.php?updated=1");
    exit();
}

// Filter parameters
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';
$location_filter = isset($_GET['location']) ? $_GET['location'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query
$query = "SELECT * FROM bookings WHERE 1=1";
$params = [];

if ($status_filter && $status_filter != 'all') {
    $query .= " AND status = ?";
    $params[] = $status_filter;
}

if ($location_filter && $location_filter != 'all') {
    $query .= " AND location = ?";
    $params[] = $location_filter;
}

if ($search) {
    $query .= " AND (booking_ref LIKE ? OR guest_name LIKE ? OR email LIKE ? OR property_name LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY booking_date DESC";

$bookings = $pdo->prepare($query);
$bookings->execute($params);
$bookings = $bookings->fetchAll();

// Get all locations for filter
$locations = $pdo->query("SELECT DISTINCT location FROM properties")->fetchAll();

// Get counts by status
$count_pending = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'pending'")->fetchColumn();
$count_confirmed = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'confirmed'")->fetchColumn();
$count_completed = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'completed'")->fetchColumn();
$count_cancelled = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status = 'cancelled'")->fetchColumn();
?>

<!-- Status Tabs -->
<div style="margin-bottom: 20px;">
    <a href="bookings.php" class="btn" style="background: <?php echo !$status_filter ? 'var(--burgundy)' : '#eee'; ?>; color: <?php echo !$status_filter ? 'white' : '#333'; ?>; padding: 8px 16px; border-radius: 30px; text-decoration: none; margin-right: 5px;">All</a>
    <a href="bookings.php?status=pending" class="btn" style="background: <?php echo $status_filter == 'pending' ? 'var(--burgundy)' : '#eee'; ?>; color: <?php echo $status_filter == 'pending' ? 'white' : '#333'; ?>; padding: 8px 16px; border-radius: 30px; text-decoration: none; margin-right: 5px;">
        Pending (<?php echo $count_pending; ?>)
    </a>
    <a href="bookings.php?status=confirmed" class="btn" style="background: <?php echo $status_filter == 'confirmed' ? 'var(--burgundy)' : '#eee'; ?>; color: <?php echo $status_filter == 'confirmed' ? 'white' : '#333'; ?>; padding: 8px 16px; border-radius: 30px; text-decoration: none; margin-right: 5px;">
        Confirmed (<?php echo $count_confirmed; ?>)
    </a>
    <a href="bookings.php?status=completed" class="btn" style="background: <?php echo $status_filter == 'completed' ? 'var(--burgundy)' : '#eee'; ?>; color: <?php echo $status_filter == 'completed' ? 'white' : '#333'; ?>; padding: 8px 16px; border-radius: 30px; text-decoration: none; margin-right: 5px;">
        Completed (<?php echo $count_completed; ?>)
    </a>
    <a href="bookings.php?status=cancelled" class="btn" style="background: <?php echo $status_filter == 'cancelled' ? 'var(--burgundy)' : '#eee'; ?>; color: <?php echo $status_filter == 'cancelled' ? 'white' : '#333'; ?>; padding: 8px 16px; border-radius: 30px; text-decoration: none;">
        Cancelled (<?php echo $count_cancelled; ?>)
    </a>
</div>

<!-- Search and Filter -->
<div style="display: flex; gap: 15px; margin-bottom: 20px;">
    <form method="GET" style="flex: 1; display: flex; gap: 10px;">
        <input type="text" name="search" placeholder="Search by reference, guest, property..." value="<?php echo htmlspecialchars($search); ?>" style="flex: 1; padding: 10px; border: 1px solid var(--champagne); border-radius: 10px;">
        <select name="location" style="padding: 10px; border: 1px solid var(--champagne); border-radius: 10px;">
            <option value="all">All Locations</option>
            <?php foreach($locations as $loc): ?>
                <option value="<?php echo $loc['location']; ?>" <?php echo $location_filter == $loc['location'] ? 'selected' : ''; ?>>
                    <?php echo $loc['location']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="background: var(--burgundy); color: white; border: none; padding: 10px 20px; border-radius: 10px; cursor: pointer;">
            <i class="fas fa-search"></i> Filter
        </button>
    </form>
    
    <a href="bookings.php?export=1" style="background: var(--deep-navy); color: white; padding: 10px 20px; border-radius: 10px; text-decoration: none;">
        <i class="fas fa-download"></i> Export
    </a>
</div>

<!-- Bookings Table -->
<div class="table-container">
    <div class="table-header">
        <h2>Manage Bookings</h2>
        <span>Total: <?php echo count($bookings); ?> bookings</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Booking Ref</th>
                <th>Guest Details</th>
                <th>Property</th>
                <th>Check In/Out</th>
                <th>Guests</th>
                <th>Nights</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(count($bookings) > 0): ?>
                <?php foreach($bookings as $booking): ?>
                <tr>
                    <td><strong><?php echo $booking['booking_ref']; ?></strong></td>
                    <td>
                        <?php echo $booking['guest_name']; ?><br>
                        <small style="color: #666;"><?php echo $booking['email']; ?></small>
                    </td>
                    <td>
                        <?php echo $booking['property_name']; ?><br>
                        <small style="color: #666;"><i class="fas fa-map-marker-alt"></i> <?php echo $booking['location']; ?></small>
                    </td>
                    <td>
                        <?php echo date('M d, Y', strtotime($booking['checkin_date'])); ?><br>
                        <small>to <?php echo date('M d, Y', strtotime($booking['checkout_date'])); ?></small>
                    </td>
                    <td><?php echo $booking['guests']; ?></td>
                    <td><?php echo $booking['nights']; ?></td>
                    <td><strong>$<?php echo number_format($booking['total'], 2); ?></strong></td>
                    <td>
                        <span class="status-badge status-<?php echo $booking['payment_status']; ?>">
                            <?php echo ucfirst($booking['payment_status']); ?>
                        </span><br>
                        <small><?php echo str_replace('_', ' ', $booking['payment_method']); ?></small>
                    </td>
                    <td>
                        <span class="status-badge status-<?php echo $booking['status']; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </td>
                    <td>
                        <a href="booking-details.php?id=<?php echo $booking['id']; ?>" class="action-btn" title="View Details">
                            <i class="fas fa-eye"></i>
                        </a>
                        <button onclick="openStatusModal(<?php echo $booking['id']; ?>, '<?php echo $booking['status']; ?>')" class="action-btn" title="Update Status">
                            <i class="fas fa-edit"></i>
                        </button>
                        <a href="send-email.php?booking=<?php echo $booking['id']; ?>" class="action-btn" title="Send Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 30px;">
                        <i class="fas fa-inbox" style="font-size: 3rem; color: #ccc; margin-bottom: 10px;"></i>
                        <p>No bookings found</p>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Status Update Modal -->
<div class="modal" id="statusModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Update Booking Status</h3>
            <button class="close-modal" onclick="closeStatusModal()">&times;</button>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="booking_id" id="modal_booking_id">
            
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="modal_status" class="form-control">
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Payment Status (will update automatically)</label>
                <p style="padding: 10px; background: #f5f5f5; border-radius: 10px;">
                    Payment status will be updated to match booking status
                </p>
            </div>
            
            <button type="submit" name="update_status" class="btn" style="width: 100%; padding: 12px; background: var(--burgundy); color: white; border: none; border-radius: 10px; cursor: pointer;">
                Update Status
            </button>
        </form>
    </div>
</div>

<script>
function openStatusModal(id, currentStatus) {
    document.getElementById('modal_booking_id').value = id;
    document.getElementById('modal_status').value = currentStatus;
    document.getElementById('statusModal').classList.add('active');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>