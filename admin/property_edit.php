<?php
require_once 'includes/header.php';

$id = isset($_GET['id']) ? $_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : 'edit';
$property = null;

// Handle toggle status
if (isset($_GET['toggle'])) {
    $toggle_id = $_GET['toggle'];
    $stmt = $pdo->prepare("UPDATE properties SET is_active = NOT is_active WHERE id = ?");
    $stmt->execute([$toggle_id]);
    header("Location: properties.php");
    exit();
}

if ($id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM properties WHERE id = ?");
    $stmt->execute([$id]);
    $property = $stmt->fetch();
    
    if (!$property) {
        header("Location: properties.php");
        exit();
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $property_name = $_POST['property_name'];
    $property_slug = strtolower(str_replace(' ', '-', $property_name));
    $location = $_POST['location'];
    $description = $_POST['description'];
    $price_per_night = $_POST['price_per_night'];
    $bedrooms = $_POST['bedrooms'];
    $bathrooms = $_POST['bathrooms'];
    $max_guests = $_POST['max_guests'];
    $amenities = $_POST['amenities'];
    $rating = $_POST['rating'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    if ($id > 0) {
        // Update
        $stmt = $pdo->prepare("UPDATE properties SET 
            property_name = ?, property_slug = ?, location = ?, description = ?,
            price_per_night = ?, bedrooms = ?, bathrooms = ?, max_guests = ?,
            amenities = ?, rating = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$property_name, $property_slug, $location, $description,
                       $price_per_night, $bedrooms, $bathrooms, $max_guests,
                       $amenities, $rating, $is_active, $id]);
        header("Location: properties.php?updated=1");
    } else {
        // Insert
        $stmt = $pdo->prepare("INSERT INTO properties 
            (property_name, property_slug, location, description, price_per_night,
             bedrooms, bathrooms, max_guests, amenities, rating, is_active) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$property_name, $property_slug, $location, $description,
                       $price_per_night, $bedrooms, $bathrooms, $max_guests,
                       $amenities, $rating, $is_active]);
        header("Location: properties.php?added=1");
    }
    exit();
}
?>

<h1 style="color: var(--deep-navy); margin-bottom: 20px;">
    <?php echo $id > 0 ? 'Edit Property' : 'Add New Property'; ?>
</h1>

<div style="background: white; border-radius: 15px; padding: 30px; box-shadow: var(--shadow);">
    <form method="POST" action="">
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
            <!-- Left Column -->
            <div>
                <div class="form-group">
                    <label>Property Name</label>
                    <input type="text" name="property_name" value="<?php echo $property ? $property['property_name'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Location</label>
                    <select name="location" required>
                        <option value="">Select Location</option>
                        <option value="Mukono" <?php echo $property && $property['location'] == 'Mukono' ? 'selected' : ''; ?>>Mukono</option>
                        <option value="Entebbe" <?php echo $property && $property['location'] == 'Entebbe' ? 'selected' : ''; ?>>Entebbe</option>
                        <option value="Jinja" <?php echo $property && $property['location'] == 'Jinja' ? 'selected' : ''; ?>>Jinja</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" required><?php echo $property ? $property['description'] : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Price per Night ($)</label>
                    <input type="number" name="price_per_night" step="0.01" value="<?php echo $property ? $property['price_per_night'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Amenities (comma separated)</label>
                    <input type="text" name="amenities" value="<?php echo $property ? $property['amenities'] : ''; ?>" placeholder="WiFi,Kitchen,Parking">
                </div>
            </div>
            
            <!-- Right Column -->
            <div>
                <div class="form-group">
                    <label>Bedrooms</label>
                    <input type="number" name="bedrooms" min="1" value="<?php echo $property ? $property['bedrooms'] : '1'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Bathrooms</label>
                    <input type="number" name="bathrooms" min="1" value="<?php echo $property ? $property['bathrooms'] : '1'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Max Guests</label>
                    <input type="number" name="max_guests" min="1" value="<?php echo $property ? $property['max_guests'] : '2'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Rating (0-5)</label>
                    <input type="number" name="rating" step="0.1" min="0" max="5" value="<?php echo $property ? $property['rating'] : '4.5'; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="is_active" <?php echo !$property || $property['is_active'] ? 'checked' : ''; ?>>
                        Active (visible on website)
                    </label>
                </div>
                
                <?php if($id > 0): ?>
                <div style="margin-top: 20px; padding: 15px; background: #f5f5f5; border-radius: 10px;">
                    <p><strong>Property Slug:</strong> <?php echo $property['property_slug']; ?></p>
                    <p><strong>Created:</strong> <?php echo date('M d, Y', strtotime($property['created_at'])); ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="margin-top: 30px; display: flex; gap: 15px;">
            <button type="submit" style="padding: 12px 30px; background: var(--burgundy); color: white; border: none; border-radius: 30px; cursor: pointer;">
                <i class="fas fa-save"></i> Save Property
            </button>
            <a href="properties.php" style="padding: 12px 30px; background: #f5f5f5; color: var(--deep-navy); text-decoration: none; border-radius: 30px;">
                Cancel
            </a>
        </div>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>