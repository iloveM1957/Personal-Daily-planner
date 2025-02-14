<?php
session_start();
require_once '../../database.php';

header('Content-Type: application/json');
$response = ['success' => false, 'error' => ''];

try {
    // Verify admin is logged in
    if (!isset($_SESSION['admin_id'])) {
        throw new Exception('Authentication required');
    }

    $admin_id = $_SESSION['admin_id'];

    // Prepare and execute deletion
    $stmt = $conn->prepare("DELETE FROM admin WHERE admin_id = ?");
    $stmt->bind_param("i", $admin_id);
    $stmt->execute();

    // Verify deletion
    if ($stmt->affected_rows === 0) {
        throw new Exception('Account not found');
    }

    // Clear session data
    session_unset();
    session_destroy();
    
    $response['success'] = true;

} catch (Exception $e) {
    $response['error'] = $e->getMessage();
} finally {
    echo json_encode($response);
    $conn->close();
}
?>