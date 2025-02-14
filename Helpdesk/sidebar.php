<?php
session_start();  // Make sure to start session
require_once '../database.php';

$helpdeskName = "Helpdesk";
$avatarSrc = "style/image/logo.png"; // Default logo

$sql = "SELECT helpdesk_name, helpdesk_image FROM Helpdesk WHERE helpdesk_id = ?";
$error = '';

if (isset($_SESSION['helpdesk_id'])) {
    $helpdeskId = $_SESSION['helpdesk_id'];

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $helpdeskId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $helpdesk = $result->fetch_assoc();
            $helpdeskName = htmlspecialchars($helpdesk['helpdesk_name']);

            if (!empty($helpdesk['helpdesk_image'])) {
                $avatarSrc = 'data:image/png;base64,' . base64_encode($helpdesk['helpdesk_image']);
            }
        }
    } else {
        error_log("Helpdesk Query Failed: " . $conn->error);
        $error = "Unable to load helpdesk information, please try again later.";
    }
}
?>


<!-- Sidebar -->
<div class="sidebar" id="mySidebar">
<div class="side-header">
        <img src="<?= $avatarSrc ?>" width="130" height="130" alt="Helpdesk Avatar" class="side-header img">
        <h5 style="margin-top:10px;">Hello, <?= $helpdeskName ?></h5>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

<hr style="border:1px solid; background-color:#8a7b6d; border-color:#3B3131;">
    <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">×</a>
    <a href="./home.php" ><i class="fa fa-home"></i> Dashboard</a>
    <a href="#UserFAQs"  onclick="UserFAQs()" ><i class="fa fa-users"></i> User FAQs</a>  
    <a href="#QuestionBank"   onclick="QuestionBank()" ><i class="fa fa-university"></i> QuestionBank</a>
    <a href="#Profile" onclick="Profile()"><i class="fa fa-user"></i> Profile</a>
</div>
 
<div id="main">
    <button class="openbtn" onclick="openNav()"><i class="fa fa-home"></i></button>
</div>


