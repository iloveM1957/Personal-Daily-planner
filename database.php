<?php 
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "SE_database";  // Ensure the database 'test' exists
    
    // Establish database connection
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

    // Check for connection error
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
?>