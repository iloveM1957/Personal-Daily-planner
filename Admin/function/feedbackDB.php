<?php
require_once '../../database.php';
header('Content-Type: application/json');



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'delete' && isset($_POST['feedback_id'])) {
            $feedbackId = intval($_POST['feedback_id']);
            $stmt = $conn->prepare("DELETE FROM Feedback WHERE feedback_id = ?");
            $stmt->bind_param('i', $feedbackId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Delete failed
');
            }
        } else {
            throw new Exception('Invalid request
');
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method
']);
}
?>