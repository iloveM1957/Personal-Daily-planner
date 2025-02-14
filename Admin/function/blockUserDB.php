<?php
session_start();
require '../../database.php';

header('Content-Type: application/json');



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['action'])) {
    $user_id = intval($_POST['user_id']);
    $action = $_POST['action'];

    // Validation operation type
    if (!in_array($action, ['block', 'unblock'])) {
        echo json_encode(['success' => false, 'error' => 'Invalid action']);
        exit;
    }

    // Set user status
    $status = ($action === 'block') ? 'Block' : 'Unblock';
    
    try {
        $sql = "UPDATE User SET user_block_status = ? WHERE user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('si', $status, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            throw new Exception('Database update failed');
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => 'Database error']);
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
}
?>