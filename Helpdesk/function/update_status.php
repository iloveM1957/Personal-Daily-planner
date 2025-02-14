<?php
include '../../database.php';  // Include database connection

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["question_id"])) {
    $question_id = intval($_POST["question_id"]);

    // Get current status
    $query = "SELECT status FROM question WHERE question_id = $question_id";
    $result = $conn->query($query);
    $row = $result->fetch_assoc();

    if ($row) {
        $new_status = ($row["status"] === "done") ? "pending" : "done"; // Toggle status
        $updateQuery = "UPDATE question SET status = '$new_status' WHERE question_id = $question_id";
        if ($conn->query($updateQuery) === TRUE) {
            echo json_encode(["success" => true, "new_status" => $new_status]);
        } else {
            echo json_encode(["success" => false, "error" => $conn->error]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Question not found"]);
    }
}

$conn->close();
?>
