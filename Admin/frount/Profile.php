<?php
session_start();
require_once '../../database.php';

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../NoAccount.php"); // Jump to dedicated tips page
    exit();
}

// Get the administrator's complete information (including image binary data)
$admin_id = $_SESSION['admin_id'];
$admin_name = '';
$admin_email = '';
$avatarSrc = "./style/image/logo.png";

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'];
}
if ($admin_id) {
    $sql = "SELECT admin_name, admin_email, avatar_data FROM Admin WHERE admin_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();
    $stmt->bind_result($admin_name, $admin_email, $avatar_data);
    $stmt->fetch();
    $stmt->close();

    if (!empty($avatar_data)) {
        $avatarSrc = 'data:image/png;base64,' . base64_encode($avatar_data);
}
}

?>



<div class="bodyPro">
    <div class="containerPro">
        <!-- Left avatar area -->
        <div class="avatarSectionPro">
            <div class="avatarWrapperPro">

            <img src="<?= $avatarSrc ?>" class="avatarPro" id="avatarPreview" alt="Admin Avatar">

                <input type="file" name="avatar" id="avatarInput" class="inputFilePro" accept="image/*" onchange="previewAvatar(event)">
                <label for="avatarInput" class="btnUploadPro">Change Photo</label>
            
            </div>
        </div>

        <!-- Data editing area on the right -->
        <div class="profileContentPro">
            <div class="tabPro">
                <button class="tabBtnPro active" onclick="switchTab('profile')">Profile</button>
                <button class="tabBtnPro" onclick="switchTab('password')">Change Password</button>
            </div>

            <!-- profile form -->
            <form id="profileForm" class="formPro activeForm" onsubmit="saveProfile(event)" enctype="multipart/form-data">
                <div class="formGroupPro">
                    <label class="labelPro">Admin Name</label>
                    <input type="text" name="admin_name" class="inputPro" value="<?php echo htmlspecialchars($admin_name); ?>">
                </div>
                <div class="formGroupPro">
                    <label class="labelPro">Admin Email</label>
                    <input type="email" name="email" class="inputPro" value="<?php echo htmlspecialchars($admin_email); ?>">
                </div>
                <div class="buttonGroupPro">
                    <button type="submit" class="btnSavePro">Save</button>
                    <button type="button" class="btnDeletePro" id="deleteAccountBtn" onclick="deleteAccount()">Delete Account</button>
                </div>
            </form>

            <!-- Change password form -->
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
