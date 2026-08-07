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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.png" />
    <title>Manage Users - Dashboard</title>


    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="dashboard-wrapper">
        <?php require __DIR__ . '/includes/sidebar.php'; ?>

        <main class="dashboard-main">
            <div class="dashboard-header">
                <h1>Manage Users</h1>
            </div>

            <?php if (!empty($adminErrors)): ?>
                <div class="dash-alert dash-alert-danger">
                    <ul><?php foreach ($adminErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>
            <?php if ($adminSuccess): ?>
                <div class="dash-alert dash-alert-success"><?php echo e_dash($adminSuccess); ?></div>
            <?php endif; ?>

            <div class="dash-table-wrap">
                <table class="dash-table">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo e_dash($user['name']); ?></td>
                            <td><?php echo e_dash($user['email']); ?></td>
                            <td><?php echo e_dash($user['phone']); ?></td>
                            <td>
                                <form action="../core/admin_update_role.php" method="post" class="inline-form">
                                    <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                    <select name="role">
                                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                </form>
                            </td>
                            <td><?php echo e_dash(date('d M Y', strtotime($user['created_at']))); ?></td>
                            <td>
                                <?php if ((int) $user['id'] !== $currentUserId): ?>
                                    <form action="../core/admin_delete_user.php" method="post" class="js-confirm-delete" data-message="Delete this user permanently? This can't be undone.">
                                        <input type="hidden" name="user_id" value="<?php echo (int) $user['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">(you)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </main>
    </div>

    <?php require __DIR__ . '/includes/confirm_modal.php'; ?>
</body>

</html>