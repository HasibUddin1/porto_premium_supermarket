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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/x-icon" href="../assets/images/favicon.png" />
    <title>My Account - Dashboard</title>

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
                <h1>My Account</h1>
            </div>

            <div class="action-card" style="max-width: 620px;">
                <?php if (!empty($profileErrors)): ?>
                    <div class="dash-alert dash-alert-danger">
                        <ul><?php foreach ($profileErrors as $err): ?><li><?php echo e_dash($err); ?></li><?php endforeach; ?></ul>
                    </div>
                <?php endif; ?>

                <?php if ($profileSuccess): ?>
                    <div class="dash-alert dash-alert-success"><?php echo e_dash($profileSuccess); ?></div>
                <?php endif; ?>

                <?php
                $defaultAvatar = 'data:image/svg+xml;utf8,' . rawurlencode('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120"><circle cx="60" cy="60" r="60" fill="#eef7de"/><circle cx="60" cy="46" r="22" fill="#7fb401"/><path d="M60 74c-24 0-40 14-40 34v12h80v-12c0-20-16-34-40-34z" fill="#7fb401"/></svg>');
                $photoSrc = $account['image'] ? '../users/' . e_dash($account['image']) : $defaultAvatar;
                ?>
                <img src="<?php echo $photoSrc; ?>" alt="Profile photo" class="profile-photo">

                <form action="../core/update_profile.php" method="post" enctype="multipart/form-data" class="dash-form">
                    <div class="form_group">
                        <label>Profile Photo</label>
                        <input type="file" name="profile_image" accept="image/jpeg,image/png,image/gif,image/webp">
                        <p class="helper-text">For best results, use a <strong>1:1 (square)</strong> image — for example <strong>1000&times;1000px</strong>.</p>
                    </div>

                    <div class="form_group">
                        <label>Full Name</label>
                        <input type="text" name="name" value="<?php echo e_dash($account['name']); ?>" required>
                    </div>

                    <div class="form_group">
                        <label>Email</label>
                        <input type="email" value="<?php echo e_dash($account['email']); ?>" disabled>
                        <p class="helper-text">Email can't be changed here.</p>
                    </div>

                    <div class="form_group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" value="<?php echo e_dash($account['phone']); ?>" required>
                    </div>

                    <div class="form_group">
                        <label>Location</label>
                        <input type="text" name="location" value="<?php echo e_dash($account['location']); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </main>
    </div>
</body>

</html>