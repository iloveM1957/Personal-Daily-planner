<?php
session_start();
require_once '../../database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['announcement_tittle']);
    $content = mysqli_real_escape_string($conn, $_POST['announcement_content']);
    $date = mysqli_real_escape_string($conn, $_POST['announcement_date']);

    $sql = "INSERT INTO Announcement (announcement_tittle, announcement_content, announcement_date) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $title, $content, $date);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    }
    mysqli_stmt_close($stmt);
    exit();
}

echo json_encode(['success' => false, 'error' => 'Invalid request method']);
exit();
?>