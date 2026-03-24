<?php
require_once 'includes/header.php';

$booking_id = isset($_GET['id']) ? $_GET['id'] : 0;

if (!$booking_id) {
    header("Location: bookings.php");
    exit();
}

// Get booking details
$stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
$stmt->execute([$booking_id]);
$booking = $stmt->fetch();

if (!$booking) {
    header("Location: bookings.php");
    exit();
}

// Handle admin notes update
if (isset($_POST['update_notes'])) {
    $notes = $_POST['admin_notes'];
    $stmt = $pdo->prepare("UPDATE bookings SET admin_notes = ? WHERE id = ?");
    $stmt->execute([$notes, $booking_id]);
    header("Location: booking-details.php?id=$booking_id&updated=1");
    exit();
}
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="color: var(--deep-navy);">Booking Details: <?php echo $booking['booking_ref']; ?></h1>
    <div>
        <a href="bookings.php" class="btn" style="background: var(--deep-navy); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none;">
            <i class="fas fa-arrow-left"></i> Back to Bookings
        </a>
        <a href="send-email.php?booking=<?php echo $booking['id']; ?>" class="btn" style="background: var(--burgundy); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none;">
            <i class="fas fa-envelope"></i> Send Email
        </a>
    </div>
</div>

<!-- Status Banner -->
<div style="background: <?php 
    echo $booking['status'] == 'confirmed' ? '#d4edda' : 
         ($booking['status'] == 'pending' ? '#fff3cd' : 
         ($booking['status'] == 'cancelled' ? '#f8d7da' : '#cce5ff')); 
?>; padding: 15px 20px; border-radius: 10px; margin-bottom: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <div>
            <strong>Status: <?php echo ucfirst($booking['status']); ?></strong> | 
            Payment: <?php echo ucfirst($booking['payment_status']); ?> | 
            Method: <?php echo str_replace('_', ' ', $booking['payment_method']); ?>
        </div>
        <div>
            Booked on: <?php echo date('F d, Y H:i', strtotime($booking['booking_date'])); ?>
        </div>
    </div>
</div>

<!-- Two Column Layout -->
<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
    <!-- Left Column - Booking Info -->
    <div>
        <!-- Guest Information -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h2 style="margin-bottom: 20px;">Guest Information</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Full Name</label>
                    <p style="font-weight: 600;"><?php echo $booking['guest_name']; ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Email</label>
                    <p><a href="mailto:<?php echo $booking['email']; ?>" style="color: var(--burgundy);"><?php echo $booking['email']; ?></a></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Phone</label>
                    <p><?php echo $booking['phone']; ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Number of Guests</label>
                    <p><?php echo $booking['guests']; ?> guests</p>
                </div>
            </div>
        </div>
        
        <!-- Property & Stay Details -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h2 style="margin-bottom: 20px;">Stay Details</h2>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Property</label>
                    <p style="font-weight: 600;"><?php echo $booking['property_name']; ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Location</label>
                    <p><i class="fas fa-map-marker-alt" style="color: var(--burgundy);"></i> <?php echo $booking['location']; ?></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Check-in Date</label>
                    <p><strong><?php echo date('l, F d, Y', strtotime($booking['checkin_date'])); ?></strong></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Check-out Date</label>
                    <p><strong><?php echo date('l, F d, Y', strtotime($booking['checkout_date'])); ?></strong></p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Nights</label>
                    <p><?php echo $booking['nights']; ?> nights</p>
                </div>
                <div>
                    <label style="color: #666; font-size: 0.9rem;">Price per Night</label>
                    <p>$<?php echo $booking['price_per_night']; ?></p>
                </div>
            </div>
            
            <?php if($booking['special_requests']): ?>
            <div style="margin-top: 20px;">
                <label style="color: #666; font-size: 0.9rem;">Special Requests</label>
                <div style="background: #f5f5f5; padding: 15px; border-radius: 10px; margin-top: 5px;">
                    <?php echo nl2br($booking['special_requests']); ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Payment Details -->
        <div class="table-container">
            <h2 style="margin-bottom: 20px;">Payment Details</h2>
            
            <div style="background: #f5f5f5; padding: 20px; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Subtotal (<?php echo $booking['nights']; ?> nights)</span>
                    <span>$<?php echo number_format($booking['subtotal'], 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Service Fee</span>
                    <span>$<?php echo number_format($booking['service_fee'], 2); ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-weight: 600; font-size: 1.2rem; border-top: 2px dashed #ccc; padding-top: 10px; margin-top: 10px;">
                    <span>Total</span>
                    <span style="color: var(--burgundy);">$<?php echo number_format($booking['total'], 2); ?></span>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <h3 style="margin-bottom: 10px;">Payment Instructions</h3>
                <?php if($booking['payment_method'] == 'mobile_money'): ?>
                    <p><i class="fas fa-mobile-alt" style="color: var(--burgundy);"></i> <strong>MTN/Airtel:</strong> +256 786 749299</p>
                    <p><strong>Account Name:</strong> ANNA HOMES LTD</p>
                <?php elseif($booking['payment_method'] == 'bank_transfer'): ?>
                    <p><i class="fas fa-university" style="color: var(--burgundy);"></i> <strong>Stanbic Bank Uganda</strong></p>
                    <p><strong>Account:</strong> 9030012345678</p>
                    <p><strong>Account Name:</strong> ANNA HOMES LTD</p>
                <?php else: ?>
                    <p><i class="fas fa-credit-card" style="color: var(--burgundy);"></i> Card payment will be processed via secure link</p>
                <?php endif; ?>
                <p style="margin-top: 10px; font-size: 0.9rem; color: #666;">
                    Reference: <strong><?php echo $booking['booking_ref']; ?></strong>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Actions & Notes -->
    <div>
        <!-- Status Update -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h2 style="margin-bottom: 20px;">Update Status</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Booking Status</label>
                    <select name="status" class="form-control">
                        <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status" class="form-control">
                        <option value="pending" <?php echo $booking['payment_status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="confirmed" <?php echo $booking['payment_status'] == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                        <option value="failed" <?php echo $booking['payment_status'] == 'failed' ? 'selected' : ''; ?>>Failed</option>
                        <option value="refunded" <?php echo $booking['payment_status'] == 'refunded' ? 'selected' : ''; ?>>Refunded</option>
                    </select>
                </div>
                
                <button type="submit" name="update_status" class="btn" style="width: 100%; padding: 12px; background: var(--burgundy); color: white; border: none; border-radius: 10px; cursor: pointer;">
                    Update Status
                </button>
            </form>
        </div>
        
        <!-- Admin Notes -->
        <div class="table-container" style="margin-bottom: 20px;">
            <h2 style="margin-bottom: 20px;">Admin Notes</h2>
            
            <form method="POST" action="">
                <div class="form-group">
                    <textarea name="admin_notes" rows="5" style="width: 100%; padding: 10px; border: 1px solid var(--champagne); border-radius: 10px;"><?php echo $booking['admin_notes'] ?? ''; ?></textarea>
                </div>
                
                <button type="submit" name="update_notes" class="btn" style="width: 100%; padding: 12px; background: var(--deep-navy); color: white; border: none; border-radius: 10px; cursor: pointer;">
                    Save Notes
                </button>
            </form>
        </div>
        
        <!-- Quick Actions -->
        <div class="table-container">
            <h2 style="margin-bottom: 20px;">Quick Actions</h2>
            
            <div style="display: grid; gap: 10px;">
                <a href="#" class="btn" style="display: block; text-align: center; padding: 12px; background: #f5f5f5; color: var(--deep-navy); text-decoration: none; border-radius: 10px;">
                    <i class="fas fa-receipt"></i> Print Invoice
                </a>
                <a href="#" class="btn" style="display: block; text-align: center; padding: 12px; background: #f5f5f5; color: var(--deep-navy); text-decoration: none; border-radius: 10px;">
                    <i class="fas fa-calendar"></i> Add to Calendar
                </a>
                <a href="#" class="btn" style="display: block; text-align: center; padding: 12px; background: #f5f5f5; color: var(--deep-navy); text-decoration: none; border-radius: 10px;">
                    <i class="fas fa-sms"></i> Send SMS Reminder
                </a>
                <a href="#" onclick="return confirm('Are you sure you want to cancel this booking?')" class="btn" style="display: block; text-align: center; padding: 12px; background: #f8d7da; color: #721c24; text-decoration: none; border-radius: 10px;">
                    <i class="fas fa-times-circle"></i> Cancel Booking
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>