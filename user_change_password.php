<?php
    include('database.php'); 
    session_start();
    global $conn;
    $user_id = $_SESSION['user_id'];

    
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(["status" => "error", "message" => "Invalid request method."]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get the inputs from the request
        $oldPassword = $_POST['old_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        // Initialize response array
        $response = ['success' => false, 'message' => ''];

        //check if any fields are empty
        if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
            $response['message'] = 'Please fill in all the fields.';
            echo json_encode($response);
            exit; 
        }

        // Check if the new password and confirm password match
        if ($newPassword !== $confirmPassword) {
            $response['message'] = 'New password and confirm password do not match.';
            echo json_encode($response);
            exit; 
        }

        //Password regex
        $passwordPattern = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{6,}$/';

        // Validate new password against the regex pattern
        if (!preg_match($passwordPattern, $newPassword)) {
            $response['message'] = 'Password must be at least 6 characters long, contain at least one uppercase letter, one lowercase letter, and one digit. No spaces allowed.';
            echo json_encode($response);
            exit; 
        }

        // Check the current password from the database
        $sql_check_password = "SELECT user_password FROM user WHERE user_id = $user_id";
        $result_check_password = mysqli_query($conn, $sql_check_password);

        if ($result_check_password) {
            $row = mysqli_fetch_assoc($result_check_password);
            $db_password_hash = $row['user_password'];

            // Verify if the old password is correct
            if (!password_verify($oldPassword, $db_password_hash)) {
                $response['message'] = 'Current password is incorrect.';
                echo json_encode($response);
                exit; // End execution if password verification fails
            }

            // Check if the new password is the same as the old password
            if (password_verify($newPassword, $db_password_hash)) {
                $response['message'] = 'New password cannot be the same as the current password.';
                echo json_encode($response);
                exit; // End execution if passwords are the same
            }

            // Proceed to update the password in the database
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $sql_update_password = "UPDATE user SET user_password = '$newPasswordHash' WHERE user_id = $user_id";
            
            if (mysqli_query($conn, $sql_update_password)) {
                $response['success'] = true;
                $response['message'] = 'Password updated successfully!';
            } else {
                $response['message'] = 'Error updating password. Please try again later.';
            }

            // Return the response as JSON
            echo json_encode($response);
        } else {
            $response['message'] = 'Error: Could not fetch current password from database.';
            echo json_encode($response);
        }

        // Close database connection
        mysqli_close($conn);
    }
?>
