<?php
require_once 'includes/header.php';

// Handle settings update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // In a real app, you'd save these to a settings table
    // For now, we'll just show a success message
    $success = "Settings saved successfully!";
}
?>

<h1 style="color: var(--deep-navy); margin-bottom: 20px;">Admin Settings</h1>

<?php if(isset($success)): ?>
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 10px; margin-bottom: 20px;">
        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    <!-- General Settings -->
    <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">General Settings</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>Website Name</label>
                <input type="text" name="site_name" value="ANNA HOMES">
            </div>
            
            <div class="form-group">
                <label>Contact Email</label>
                <input type="email" name="contact_email" value="admin@annahomes.com">
            </div>
            
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" name="phone" value="+256 786 749299">
            </div>
            
            <div class="form-group">
                <label>Currency</label>
                <select name="currency">
                    <option value="USD" selected>USD ($)</option>
                    <option value="UGX">UGX (Shillings)</option>
                </select>
            </div>
            
            <button type="submit" style="padding: 12px 30px; background: var(--burgundy); color: white; border: none; border-radius: 30px; cursor: pointer;">
                Save Settings
            </button>
        </form>
    </div>
    
    <!-- Payment Settings -->
    <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">Payment Settings</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>Mobile Money Number (MTN)</label>
                <input type="text" name="mtn_number" value="+256 786 749299">
            </div>
            
            <div class="form-group">
                <label>Mobile Money Number (Airtel)</label>
                <input type="text" name="airtel_number" value="+256 786 749299">
            </div>
            
            <div class="form-group">
                <label>Bank Name</label>
                <input type="text" name="bank_name" value="Stanbic Bank Uganda">
            </div>
            
            <div class="form-group">
                <label>Account Number</label>
                <input type="text" name="account_number" value="9030012345678">
            </div>
            
            <div class="form-group">
                <label>Account Name</label>
                <input type="text" name="account_name" value="ANNA HOMES LTD">
            </div>
            
            <div class="form-group">
                <label>SWIFT Code</label>
                <input type="text" name="swift" value="SBICUGKX">
            </div>
            
            <button type="submit" style="padding: 12px 30px; background: var(--burgundy); color: white; border: none; border-radius: 30px; cursor: pointer;">
                Save Payment Settings
            </button>
        </form>
    </div>
    
    <!-- Service Fees -->
    <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">Service Fees</h2>
        
        <form method="POST">
            <div class="form-group">
                <label>Service Fee (Fixed amount)</label>
                <input type="number" name="service_fee" value="15">
            </div>
            
            <div class="form-group">
                <label>Service Fee Type</label>
                <select name="fee_type">
                    <option value="fixed" selected>Fixed Amount</option>
                    <option value="percentage">Percentage of Booking</option>
                </select>
            </div>
            
            <button type="submit" style="padding: 12px 30px; background: var(--burgundy); color: white; border: none; border-radius: 30px; cursor: pointer;">
                Save Fee Settings
            </button>
        </form>
    </div>
    
    <!-- Email Templates -->
    <div style="background: white; border-radius: 15px; padding: 25px; box-shadow: var(--shadow);">
        <h2 style="margin-bottom: 20px;">Email Templates</h2>
        
        <p style="margin-bottom: 20px;">Configure email notifications sent to guests.</p>
        
        <div style="display: grid; gap: 15px;">
            <a href="#" style="display: block; padding: 15px; background: #f5f5f5; border-radius: 10px; text-decoration: none; color: var(--deep-navy);">
                <i class="fas fa-check-circle" style="color: var(--burgundy);"></i> Booking Confirmation
            </a>
            <a href="#" style="display: block; padding: 15px; background: #f5f5f5; border-radius: 10px; text-decoration: none; color: var(--deep-navy);">
                <i class="fas fa-credit-card" style="color: var(--burgundy);"></i> Payment Received
            </a>
            <a href="#" style="display: block; padding: 15px; background: #f5f5f5; border-radius: 10px; text-decoration: none; color: var(--deep-navy);">
                <i class="fas fa-times-circle" style="color: var(--burgundy);"></i> Booking Cancelled
            </a>
            <a href="#" style="display: block; padding: 15px; background: #f5f5f5; border-radius: 10px; text-decoration: none; color: var(--deep-navy);">
                <i class="fas fa-star" style="color: var(--burgundy);"></i> Review Request
            </a>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>