<?php
require_once '../../database.php';

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        // Delete operation
        if ($action === 'delete' && isset($_POST['special_id'])) {
            $specialId = intval($_POST['special_id']);
            $stmt = $conn->prepare("DELETE FROM special_day WHERE special_day_id = ?");
            $stmt->bind_param('i', $specialId);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Delete failed');
            }
        }
        // Add action
        elseif ($action === 'add' && isset($_POST['date'], $_POST['content'])) {
            $date = $_POST['date'];
            $content = htmlspecialchars($_POST['content']);
            
            $stmt = $conn->prepare("INSERT INTO special_day (special_day_content, special_day_date) VALUES (?, ?)");
            $stmt->bind_param('ss', $content, $date);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Add failed');
            }
        } else {
            throw new Exception('Invalid request');
        }
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    } finally {
        if (isset($stmt)) $stmt->close();
        $conn->close();
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
?>