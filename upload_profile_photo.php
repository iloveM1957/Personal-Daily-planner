<?php
session_start();
include("database.php");
global $conn;

$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["image"])) {
    $uploadDir = "images/";
    $allowedTypes = ["image/jpeg", "image/png", "image/jpg"];
    $file = $_FILES["image"];
    $fileName = basename($file["name"]);
    $targetFilePath = $uploadDir . time() . "_" . $fileName; // Unique filename

    // Check if file is selected
    if ($file["size"] == 0) {
        echo json_encode(["success" => false, "message" => "No file selected."]);
        exit();
    }

    // Check file type
    if (!in_array($file["type"], $allowedTypes)) {
        echo json_encode(["success" => false, "message" => "Invalid file type. Only JPG, JPEG & PNG allowed."]);
        exit();
    }

    // Upload file
    if (move_uploaded_file($file["tmp_name"], $targetFilePath)) {
        // Update database
        $sql = "UPDATE user SET user_image = ? WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $targetFilePath, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(["success" => true, "image" => $targetFilePath]);
        } else {
            echo json_encode(["success" => false, "message" => "Database update failed."]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(["success" => false, "message" => "File upload failed."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "No file uploaded."]);
}
?>
