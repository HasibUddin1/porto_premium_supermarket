<?php
require_once __DIR__ . '/../core/db_connection.php';
require_once __DIR__ . '/../core/login_check.php';

$userStmt = $conn->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$userStmt->bind_param('i', $currentUserId);
$userStmt->execute();
$account = $userStmt->get_result()->fetch_assoc();
$userStmt->close();

$profileErrors  = $_SESSION['profile_errors'] ?? [];
$profileSuccess = $_SESSION['profile_success'] ?? '';
unset($_SESSION['profile_errors'], $_SESSION['profile_success']);

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

        <div style="flex: 1; max-width: 600px;">
            <h2>My Account</h2>

            <?php if (!empty($profileErrors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($profileErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($profileSuccess): ?>
                <div class="alert alert-success"><?php echo e_dash($profileSuccess); ?></div>
            <?php endif; ?>

            <div style="margin: 20px 0;">
                <img
                    src="<?php echo $account['image'] ? '../users/' . e_dash($account['image']) : 'https://via.placeholder.com/120?text=No+Photo'; ?>"
                    alt="Profile photo"
                    style="width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 1px solid #eee;">
            </div>

            <form action="../core/update_profile.php" method="post" enctype="multipart/form-data">
                <div class="form_group">
                    <label>Profile Photo</label>
                    <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp">
                    <p style="font-size: 12px; color: #888; margin-top: 5px;">
                        For best results, use a <strong>1:1 (square)</strong> image — for example <strong>1000&times;1000px</strong>.
                    </p>
                </div>

                <div class="form_group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo e_dash($account['name']); ?>" required>
                </div>

                <div class="form_group">
                    <label>Email</label>
                    <input type="email" value="<?php echo e_dash($account['email']); ?>" disabled>
                    <p style="font-size: 12px; color: #888; margin-top: 5px;">Email can't be changed here.</p>
                </div>

                <div class="form_group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo e_dash($account['phone']); ?>" required>
                </div>

                <div class="form_group">
                    <label>Location</label>
                    <input type="text" name="location" value="<?php echo e_dash($account['location']); ?>">
                </div>

                <button type="submit" class="tran3s color1_bg">Save Changes</button>
            </form>
        </div>
    </div>
</div>