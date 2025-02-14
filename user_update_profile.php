<?php
    session_start(); 

    header("Content-Type: application/json");
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    include('database.php'); 
    global $conn;
    $user_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["status" => "error", "message" => "Invalid request method."]);
        exit;
    }

    $username = isset($_POST["user_name"]) ? trim($_POST["user_name"]) : "";
    $email = isset($_POST["user_email"]) ? trim($_POST["user_email"]) : "";

    // Log received values for debugging
    error_log("Received Username: $username, Email: $email");

    $errors = [];

    //Check if username and email are provided
    if (empty($username) || empty($email)) {
        $errors[] = "Username and Email is required.";
    }

    // Validate username
    if (!empty($username) && !preg_match("/^[a-zA-Z][a-zA-Z0-9_-]{4,18}[a-zA-Z0-9]$/", $username)) {
        $errors[] = "Invalid username format.";
    }

    // Validate email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (!empty($errors)) {
        echo json_encode(["status" => "error", "messages" => $errors]);
        exit;
    }

    //check if username and email exist
    $query = "SELECT user_id FROM user WHERE (user_name = ? OR user_email = ?) AND user_id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $email, $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "Username or Email is already taken."]);
        exit;
    }

    $stmt->close();

    //update new entry data
    $query = "UPDATE user SET user_name = ?, user_email = ? WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $username, $email, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Profile updated successfully."]);
    } else {
        error_log("SQL Error: " . $stmt->error); 
        echo json_encode(["status" => "error", "message" => "Failed to update profile. Try again."]);
    }
    $stmt->close();
    $conn->close();
?>
