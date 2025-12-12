<?php include '../app/views/header.php'; ?>

<div class="admin-header">
    <h1>Manage Users</h1>
    <p>View registered users</p>
</div>


<div class="admin-nav">
    <a href="?page=admin&action=dashboard">Dashboard</a>
    <a href="?page=admin&action=products">Products</a>
    <a href="?page=admin&action=orders">Orders</a>
    <a href="?page=admin&action=users">Users</a>
    <a href="?page=admin&action=inventory">Inventory</a>
    <a href="?page=admin&action=analytics">Analytics</a>
</div>



<div class="users-list">
    <?php if (empty($users)): ?>
        <p>No users found.</p>
    <?php else: ?>
        <div class="users-grid">
            <?php foreach ($users as $user): ?>
                <div class="user-card">
                    <h4><?= htmlspecialchars($user['first_name']) ?> <?= htmlspecialchars($user['last_name']) ?></h4>
                    <p>Email: <?= htmlspecialchars($user['email']) ?></p>
                    <p>Phone: <?= $user['phone'] ? htmlspecialchars($user['phone']) : 'Not provided' ?></p>
                    <p>Joined: <?= date('M j, Y', strtotime($user['created_at'])) ?></p>
                    <small>User ID: <?= $user['id'] ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../app/views/footer.php'; ?>