<?php
session_start();
require_once '../../database.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $announcementId = (int)$_POST['announcement_id'];
    
    $sql = "DELETE FROM Announcement WHERE announcement_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $announcementId);
    
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid request']);
?>