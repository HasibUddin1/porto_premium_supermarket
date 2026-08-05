<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/admin_check.php';

$users = $conn->query("SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

$adminErrors  = $_SESSION['admin_users_errors'] ?? [];
$adminSuccess = $_SESSION['admin_users_success'] ?? '';
unset($_SESSION['admin_users_errors'], $_SESSION['admin_users_success']);

function e_dash($v)
{
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
}
?>


<head>
    <?php
    $pageInfo = [
        "title" => "Porto Premium Supermarket - Dashboard",
    ];
    ?>

    <?php include_once __DIR__ . '/includes/head.php'; ?>
</head>

<div class="container" style="margin: 40px auto;">
    <div style="display: flex; gap: 30px;">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <div style="flex: 1;">
            <h2>Manage Users</h2>

            <?php if (!empty($adminErrors)): ?>
                <div class="alert alert-danger">
                    <ul><?php foreach ($adminErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($adminSuccess): ?>
                <div class="alert alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="table-responsive" style="margin-top: 20px;">
                <table class="table table-1">
                    <tr>
                        <th><span>Name</span></th>
                        <th><span>Email</span></th>
                        <th><span>Phone</span></th>
                        <th><span>Role</span></th>
                        <th><span>Joined</span></th>
                        <th><span>Actions</span></th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo e_dash($user['name']); ?></td>
                            <td><?php echo e_dash($user['email']); ?></td>
                            <td><?php echo e_dash($user['phone']); ?></td>
                            <td>
                                <form action="../core/admin_update_role.php" method="post" style="display: inline-flex; gap: 6px;">
                                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                    <select name="role">
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="tran3s color1_bg" style="padding: 4px 12px;">Update</button>
                                </form>
                            </td>
                            <td><?php echo e_dash(date('d M Y', strtotime($user['created_at']))); ?></td>
                            <td>
                                <?php if ((int) $user['id'] !== $currentUserId): ?>
                                    <form action="../core/admin_delete_user.php" method="post" onsubmit="return confirm('Delete this user permanently?');">
                                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                        <button type="submit" class="tran3s" style="padding: 4px 12px; background: #e53935; color: #fff; border: none;">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #999;">(you)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>