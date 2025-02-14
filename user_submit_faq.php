<?php
    // Include the database connection file
    include('database.php'); 
    session_start();
    global $conn;
    $user_id = $_SESSION['user_id'];

    // Default response
    $response = ["success" => false, "message" => ""];

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["status" => "error", "message" => "Invalid request method."]);
        exit;
    }

    // Get the FAQ question from the POST request
    $question = isset($_POST["faq_question"]) ? trim($_POST["faq_question"]) : "";

    if (empty($question) ) {
        echo json_encode(["status" => "error", "message" => "Please enter a valid question."]);
        exit;
    }

    $query = "INSERT INTO question (question_content, user_id) 
    VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $question, $user_id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Question submitted successfully!"]);
    } else {
        error_log("SQL Error: " . $stmt->error); 
        echo json_encode(["status" => "error", "message" => "Failed to submit question. Try again."]);
    }
    $stmt->close();
    $conn->close();
?>
