<?php
session_start();
require_once '../../database.php';

header('Content-Type: application/json');

$response = ['success' => false, 'error' => ''];

try {
    $helpdesk_id = $_SESSION['helpdesk_id'];

    // Process information updates (including avatar updates)
    if (isset($_POST['helpdesk_name']) || isset($_POST['email'])) {
        // Get the current avatar data (used to keep the original value when not updated)
        $stmt = $conn->prepare("SELECT helpdesk_image FROM helpdesk WHERE helpdesk_id = ?");
        $stmt->bind_param("i", $helpdesk_id);
        $stmt->execute();
        $stmt->bind_result($current_avatar_data);
        $stmt->fetch();
        $stmt->close();

        // Keep the original avatar data by default
        $avatar_data = $current_avatar_data;

        // If a new avatar is uploaded, read the file content
        if (!empty($_FILES['avatar']['tmp_name'])) {
            $avatar_data = file_get_contents($_FILES['avatar']['tmp_name']);
        }

        // Get other basic information
        $helpdesk_name  = $_POST['helpdesk_name'] ?? '';
        $helpdesk_email = $_POST['email'] ?? '';

        // Update the database
        $stmt = $conn->prepare("UPDATE helpdesk SET helpdesk_name = ?, helpdesk_email = ?, helpdesk_image = ? WHERE helpdesk_id = ?");
        $stmt->bind_param("sssi", $helpdesk_name, $helpdesk_email, $avatar_data, $helpdesk_id);
        
        if ($stmt->execute()) {
            // Update session data
            $_SESSION['helpdesk_name']  = $helpdesk_name;
            $_SESSION['helpdesk_email'] = $helpdesk_email;
            $_SESSION['helpdesk_image'] = $avatar_data;
            $response['success'] = true;
        } else {
            throw new Exception('Failed to update profile');
        }
        $stmt->close();
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
        $stmt = $conn->prepare("SELECT helpdesk_password FROM helpdesk WHERE helpdesk_id = ?");
        $stmt->bind_param("i", $helpdesk_id);
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
        $stmt = $conn->prepare("UPDATE helpdesk SET helpdesk_password = ? WHERE helpdesk_id = ?");
        $stmt->bind_param("si", $new_hash, $helpdesk_id);
        
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
