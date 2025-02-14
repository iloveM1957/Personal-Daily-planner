<?php
session_start();
require_once '../../database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

try {

    $admin_id = $_SESSION['admin_id'];

    // Process information updates (including avatar updates)
    if (isset($_POST['admin_name']) || isset($_POST['email'])) {
        // Get the current avatar data (used to keep the original value when not updated)
        $stmt = $conn->prepare("SELECT avatar_data FROM Admin WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->bind_result($current_avatar_data);
        $stmt->fetch();
        $stmt->close();

        // Keep the original avatar data by default
        $avatar_data = $current_avatar_data;

        // If a new avatar is uploaded, read the file content (note: this is not saved to the file system,
        // but the binary data is saved directly to the database)
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $avatar_data = file_get_contents($_FILES['avatar']['tmp_name']);
        }

        // Get other basic information
        $admin_name  = $_POST['admin_name'] ?? '';
        $admin_email = $_POST['email'] ?? '';

        // Update the database. Note that avatar_data is binary data. Use the "s" type of bind_param.
        $stmt = $conn->prepare("UPDATE Admin SET admin_name = ?, admin_email = ?, avatar_data = ? WHERE admin_id = ?");
        $stmt->bind_param("sssi", $admin_name, $admin_email, $avatar_data, $admin_id);
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['admin_name']  = $admin_name;
            $_SESSION['admin_email'] = $admin_email;
            $_SESSION['avatar_data'] = $avatar_data;
            $response['success'] = true;
        } else {
            throw new Exception('Failed to update profile');
            $stmt->close();
        }
        

    // Handle password changes
    } elseif (isset($_POST['old_password'])) {
        $old_password     = $_POST['old_password'];
        $new_password     = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Basic verification
        if ($new_password !== $confirm_password) {
            throw new Exception('New passwords do not match');
        }

        // Get current password hash
        $stmt = $conn->prepare("SELECT admin_password FROM Admin WHERE admin_id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $stmt->bind_result($current_hash);
        $stmt->fetch();
        $stmt->close();

        // Verify old password
        if (!password_verify($old_password, $current_hash)) {
            throw new Exception('Old password is incorrect');
        }

        // Update password
        $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE Admin SET admin_password = ? WHERE admin_id = ?");
        $stmt->bind_param("si", $new_hash, $admin_id);
        
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            throw new Exception('Password update failed');
        }
        $stmt->close();
    }

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
} finally {
    echo json_encode($response);
    $conn->close();
}
