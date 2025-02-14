<?php
session_start();
require_once '../../database.php';

if (!isset($_SESSION['helpdesk_id'])) {
    header("Location: ../NoAccount.php"); // Jump to dedicated tips page
    exit();
}
// Get helpdesk user data
$helpdesk_id = $_SESSION['helpdesk_id'];
$helpdesk_id = $_SESSION['helpdesk_id'];
$helpdesk_name = '';
$helpdesk_email = '';
$avatarSrc = "./style/image/logo.png";

$sql = "SELECT helpdesk_name, helpdesk_email, helpdesk_image FROM helpdesk WHERE helpdesk_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $helpdesk_id);
$stmt->execute();
$stmt->bind_result($helpdesk_name, $helpdesk_email, $helpdesk_image);
$stmt->fetch();
$stmt->close();

if (!empty($helpdesk_image)) {
    $avatarSrc = 'data:image/png;base64,' . base64_encode($helpdesk_image);
}
?>

<div class="bodyPro">
    <div class="containerPro">
        <!-- Avatar Section -->
        <div class="avatarSectionPro">
            <div class="avatarWrapperPro">
                <img src="<?= $avatarSrc ?>" class="avatarPro" id="avatarPreview" alt="Helpdesk Avatar">
                <input type="file" name="avatar" id="avatarInput" class="inputFilePro" accept="image/*" onchange="previewAvatar(event)">
                <label for="avatarInput" class="btnUploadPro">Change Photo</label>
            </div>
        </div>

        <!-- Profile Details -->
        <div class="profileContentPro">
            <div class="tabPro">
                <button class="tabBtnPro active" onclick="switchTab('profile')">Profile</button>
                <button class="tabBtnPro" onclick="switchTab('password')">Change Password</button>
            </div>

            <!-- Profile Form -->
            <form id="profileForm" class="formPro activeForm" onsubmit="saveProfile(event)" enctype="multipart/form-data">
                <div class="formGroupPro">
                    <label class="labelPro">Helpdesk Name</label>
                    <input type="text" name="helpdesk_name" class="inputPro" value="<?php echo htmlspecialchars($helpdesk_name); ?>">
                </div>
                <div class="formGroupPro">
                    <label class="labelPro">Helpdesk Email</label>
                    <input type="email" name="email" class="inputPro" value="<?php echo htmlspecialchars($helpdesk_email); ?>">
                </div>
                <div class="buttonGroupPro">
                    <button type="submit" class="btnSavePro">Save</button>
                    <button type="button" class="btnDeletePro" id="deleteAccountBtn" onclick="deleteAccount()">Delete Account</button>
                </div>
            </form>

            <!-- Change Password Form -->
            <form id="passwordForm" class="formPro" onsubmit="changePassword(event)">
                <div class="formGroupPro">
                    <label class="labelPro">Old Password</label>
                    <input type="password" name="old_password" class="inputPro" required>
                </div>
                <div class="formGroupPro">
                    <label class="labelPro">New Password</label>
                    <input type="password" name="new_password" class="inputPro" required>
                </div>
                <div class="formGroupPro">
                    <label class="labelPro">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="inputPro" required>
                </div>
                <button type="submit" class="btnSavePro">Save Changes</button>
            </form>
        </div>
    </div>
    <script src="./style/js/command.js"></script>
</div>
