<?php
require_once 'includes/header.php';

// Handle user role update
if (isset($_POST['update_role'])) {
    $user_id = $_POST['user_id'];
    $role = $_POST['role'];
    
    $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->execute([$role, $user_id]);
    header("Location: users.php?updated=1");
    exit();
}

// Get all users
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h1 style="color: var(--deep-navy); margin-bottom: 20px;">Manage Users</h1>

<div class="table-container">
    <div class="table-header">
        <h2>All Users</h2>
        <span>Total: <?php echo count($users); ?> users</span>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($users as $user): ?>
            <tr>
                <td>#<?php echo $user['id']; ?></td>
                <td><strong><?php echo $user['username']; ?></strong></td>
                <td><?php echo $user['email']; ?></td>
                <td>
                    <span class="status-badge" style="background: <?php echo $user['role'] == 'admin' ? '#d4edda' : '#cce5ff'; ?>; color: <?php echo $user['role'] == 'admin' ? '#155724' : '#004085'; ?>;">
                        <?php echo ucfirst($user['role']); ?>
                    </span>
                </td>
                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                <td>
                    <button onclick="openRoleModal(<?php echo $user['id']; ?>, '<?php echo $user['role']; ?>')" class="action-btn" title="Change Role">
                        <i class="fas fa-user-tag"></i>
                    </button>
                    <a href="user-bookings.php?id=<?php echo $user['id']; ?>" class="action-btn" title="View Bookings">
                        <i class="fas fa-calendar"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Role Update Modal -->
<div class="modal" id="roleModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Change User Role</h3>
            <button class="close-modal" onclick="closeRoleModal()">&times;</button>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="user_id" id="modal_user_id">
            
            <div class="form-group">
                <label>Role</label>
                <select name="role" id="modal_role" class="form-control">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            
            <button type="submit" name="update_role" class="btn" style="width: 100%; padding: 12px; background: var(--burgundy); color: white; border: none; border-radius: 10px; cursor: pointer;">
                Update Role
            </button>
        </form>
    </div>
</div>

<script>
function openRoleModal(id, currentRole) {
    document.getElementById('modal_user_id').value = id;
    document.getElementById('modal_role').value = currentRole;
    document.getElementById('roleModal').classList.add('active');
}

function closeRoleModal() {
    document.getElementById('roleModal').classList.remove('active');
}
</script>

<?php require_once 'includes/footer.php'; ?>