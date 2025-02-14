<?php
    include("database.php");
    session_start();
    global $conn;

    if (!isset($_SESSION["user_id"])) {
        echo "<script>parent.window.location.href = 'user_login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Fetch announcements
    $sql = "SELECT announcement_tittle, announcement_content, announcement_date FROM announcement ORDER BY announcement_date DESC";
    $result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
    <link rel="stylesheet" href="user_view_annoucement.css"> <!-- Link to external CSS -->
</head>
<body>

    <div class="container">
        <h2>Latest Announcements</h2>
        
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $fullContent = htmlspecialchars($row['announcement_content']);
                $shortContent = substr($fullContent, 0, 150); // Show only first 150 characters

                echo "<div class='announcement'>";
                echo "<div class='announcement-header'>";
                echo "<h3>" . htmlspecialchars($row['announcement_tittle']) . "</h3>";
                echo "<p class='announcement-date'>" . htmlspecialchars($row['announcement_date']) . "</p>";
                echo "</div>";
                
                // Show preview + full content
                echo "<p class='announcement-content'>";
                echo "<span class='short-text'>$shortContent...</span>"; // Shortened text
                echo "<span class='full-text'>$fullContent</span>"; // Full text hidden
                echo "</p>";

                echo "<button class='read-more-btn'>Read More</button>"; // Read More Button
                echo "</div>";
            }
        } else {
            echo "<div class='announcement-content'>No announcement available.</div>";
        }

        $conn->close();
        ?>
    </div>

    <script src="read_more_annoucement.js"></script> <!-- Link to external JS file -->
</body>
</html>
