<?php
session_start();  // Make sure to start session
require_once '../database.php';

$adminName = "Admin";
$avatarSrc = "style/image/logo.png"; // Default logo

$sql = "SELECT admin_name, avatar_data FROM Admin WHERE admin_id = ?";
$error = '';

if (isset($_SESSION['admin_id'])) {
    $adminId = $_SESSION['admin_id'];

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $adminId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            $adminName = htmlspecialchars($admin['admin_name']);

            if (!empty($admin['avatar_data'])) {
                $avatarSrc = 'data:image/png;base64,' . base64_encode($admin['avatar_data']);
            }
        }
    } else {
        error_log("Admin Query Failed: " . $conn->error);
        $error = "Unable to load administrator information, please try again later.";
    }
}
?>

<!-- Sidebar -->
<div class="sidebar" id="mySidebar">
    <div class="side-header">
        <img src="<?= $avatarSrc ?>" width="130" height="130" alt="Admin Avatar" class="side-header img">
        <h5 style="margin-top:10px;">Hello, <?= $adminName ?></h5>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <hr style="border:1px solid; background-color:#8a7b6d; border-color:#6D5B5B;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
    <a href="./index.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#viewUserAccount" onclick="viewUserAccount()"><i class="fa fa-users"></i> User Account</a>
    <a href="#editUserAccount" onclick="editUserAccount()"><i class="fa-solid fa-pencil"></i> Edit User Account</a>
    <a href="#manageSpecial" onclick="manageSpecial()"><i class="fa-solid fa-calendar-plus"></i> Special Event</a>
    <a href="#manageAnn" style="font-size: 21px;" onclick="manageAnn()"><i class="fa-solid fa-bullhorn"></i> Announcement</a>    
    <a href="#viewFeedback" onclick="viewFeedback()"><i class="fa-regular fa-comments"></i> View Feedback</a>
    <a href="#Profile" onclick="Profile()"><i class="fa-solid fa-address-card"></i> Profile</a>
</div>

<div id="main">
    <button class="openbtn" onclick="openNav()"><i class="fa fa-home"></i></button>
</div>
