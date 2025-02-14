<?php
require_once '../database.php';

// Query the total number of users
$totalUsers = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM User");
if ($result && $row = $result->fetch_assoc()) {
    $totalUsers = $row['total'];
}

// Query the total number of announcements
$totalAnnouncements = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM Announcement");
if ($result && $row = $result->fetch_assoc()) {
    $totalAnnouncements = $row['total'];
}

// Query the total number of feedback
$totalFeedbacks = 0;
$result = $conn->query("SELECT COUNT(*) AS total FROM Feedback");
if ($result && $row = $result->fetch_assoc()) {
    $totalFeedbacks = $row['total'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/c9514bfa2f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style/style.css">
    <title>Home</title>
</head>

<body>
    <?php include "./adminHeader.php"; ?>
    <?php include "sidebar.php"; ?>
    
    <main>
        <div style="margin-top: 30px;" id="main-content" class="container_main allContent-section py-4">
            <div class="row justify-content-center">
                <!-- User statistics card -->
                <div class="col-sm-3">
                    <div class="card">
                        <i class="fa fa-users  mb-2" style="font-size: 70px;"></i>
                        <h4 style="color:black;font-weight: bold;font-size: 35px;">Total Users</h4>
                        <h5 style="color:black;font-weight: bold;font-size: 35px;">
                            <?php echo $totalUsers; ?>
                        </h5>
                    </div>
                </div>

                <!-- Announcement Statistics Card -->
                <div class="col-sm-3">
                    <div class="card">
                        <i class="fa-solid fa-bullhorn" style="font-size: 70px;"></i>
                        <h4 style="color:black;font-weight: bold;font-size: 25px;">Total Announcement</h4>
                        <h5 style="color:black;font-weight: bold;font-size: 35px;">
                            <?php echo $totalAnnouncements; ?>
                        </h5>
                    </div>
                </div>

                <!-- Feedback stat card -->
                <div class="col-sm-3">
                    <div class="card">
                        <i class="fa-regular fa-comments" style="font-size: 70px;"></i>
                        <h4 style="color:black;font-weight: bold;font-size: 30px;">Total Feedback</h4>
                        <h5 style="color:black;font-weight: bold;font-size: 35px;">
                            <?php echo $totalFeedbacks; ?>
                        </h5>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript" src="./style/js/command.js"></script>    
        <script type="text/javascript" src="./style/js/sidebar.js"></script>
        <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
        <script>currentPage = 'home';</script>
    </main>
</body>
</html>