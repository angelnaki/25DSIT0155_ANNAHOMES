<?php
require_once 'includes/header.php';

// Get all properties
$properties = $pdo->query("SELECT * FROM properties ORDER BY location, property_name")->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h1 style="color: var(--deep-navy);">Manage Properties</h1>
    <a href="property-edit.php?action=add" class="btn" style="background: var(--burgundy); color: white; padding: 10px 20px; border-radius: 30px; text-decoration: none;">
        <i class="fas fa-plus"></i> Add New Property
    </a>
</div>

<!-- Properties Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
    <?php foreach($properties as $property): ?>
    <div style="background: white; border-radius: 15px; overflow: hidden; box-shadow: var(--shadow);">
        <div style="height: 150px; background: linear-gradient(130deg, rgba(247,230,208,0.7), rgba(232,221,208,0.8)); display: flex; align-items: center; justify-content: center; position: relative;">
            <i class="fas fa-home" style="font-size: 3rem; color: var(--burgundy); opacity: 0.5;"></i>
            <?php if(!$property['is_active']): ?>
                <span style="position: absolute; top: 10px; right: 10px; background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 20px; font-size: 0.7rem;">Inactive</span>
            <?php endif; ?>
        </div>
        
        <div style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <h3 style="color: var(--deep-navy);"><?php echo $property['property_name']; ?></h3>
                <span style="background: var(--champagne); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;">
                    <i class="fas fa-map-marker-alt" style="color: var(--burgundy);"></i> <?php echo $property['location']; ?>
                </span>
            </div>
            
            <div style="display: flex; gap: 15px; margin-bottom: 15px; color: #666; font-size: 0.9rem;">
                <span><i class="fas fa-bed" style="color: var(--burgundy);"></i> <?php echo $property['bedrooms']; ?> beds</span>
                <span><i class="fas fa-bath" style="color: var(--burgundy);"></i> <?php echo $property['bathrooms']; ?> baths</span>
                <span><i class="fas fa-users" style="color: var(--burgundy);"></i> Max <?php echo $property['max_guests']; ?></span>
            </div>
            
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <span style="font-size: 1.3rem; font-weight: 600; color: var(--burgundy);">$<?php echo $property['price_per_night']; ?></span>
                    <span style="font-size: 0.8rem; color: #666;">/night</span>
                </div>
                <div>
                    <span style="color: #FFD700;">★</span> <?php echo $property['rating']; ?> (<?php echo $property['reviews_count']; ?>)
                </div>
            </div>
            
            <div style="display: flex; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                <a href="property-edit.php?id=<?php echo $property['id']; ?>" style="flex: 1; text-align: center; padding: 8px; background: var(--deep-navy); color: white; text-decoration: none; border-radius: 8px;">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="bookings.php?property=<?php echo $property['property_slug']; ?>" style="flex: 1; text-align: center; padding: 8px; background: var(--burgundy); color: white; text-decoration: none; border-radius: 8px;">
                    <i class="fas fa-calendar"></i> View Bookings
                </a>
                <button onclick="toggleProperty(<?php echo $property['id']; ?>)" style="flex: 0.5; padding: 8px; background: <?php echo $property['is_active'] ? '#f8d7da' : '#d4edda'; ?>; color: <?php echo $property['is_active'] ? '#721c24' : '#155724'; ?>; border: none; border-radius: 8px; cursor: pointer;">
                    <i class="fas <?php echo $property['is_active'] ? 'fa-eye-slash' : 'fa-eye'; ?>"></i>
                </button>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<script>
function toggleProperty(id) {
    if (confirm('Toggle property status?')) {
        window.location.href = 'property-edit.php?toggle=' + id;
    }
}
</script>

<?php require_once 'includes/footer.php'; ?>