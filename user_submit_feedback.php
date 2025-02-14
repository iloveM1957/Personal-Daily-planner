<?php
    include('database.php'); 
    session_start();
    global $conn;
    $user_id = $_SESSION['user_id'];
   
    if (empty($user_id)) {
        echo "User ID is missing. Please log in again.";
        exit; 
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
        $feedback = mysqli_real_escape_string($conn, $_POST['feedback']);
        
        $sql = "INSERT INTO feedback (user_id, feedback_content, feedback_date) VALUES (?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $user_id, $feedback); 

        if ($stmt->execute()) {
            echo "Feedback submitted successfully!";
        } else {
            echo "Error submitting feedback. Please try again.";
        }
    } else {
        echo "Error submitting feedback. Please try again.";
    }
?>