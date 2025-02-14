<?php
    session_start();
    include("database.php");
    date_default_timezone_set("Asia/Kuala_Lumpur");
    global $conn;
    if (!isset($_SESSION["user_id"])) {
        // If not set, redirect to the login page
        echo "<script>parent.window.location.href = 'user_login.php';</script>";
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $selected_date = $_SESSION['selected_date'];  //selected date from mainPage.php
    $week_offset = $_SESSION['week_offset'];

    $next_day_date = date('Y-m-d', strtotime($selected_date . ' +1 day'));
    $current_time = date("Y-m-d H:i:s");

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
        $task_id = $_POST['task_id'];
        $sql_update = "UPDATE task SET task_reminder_status = 'sent' WHERE task_id = $task_id";
        $conn->query($sql_update);
    }

    $sql_reminder = "SELECT * FROM task 
    WHERE user_id = $user_id 
    AND task_status = 'in progress' 
    AND task_reminder_status = 'pending' 
    AND DATE(task_end_date) = '$selected_date';";

    $result = $conn->query($sql_reminder);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="30">   <!-- refresh every 30 seconds -->
    <title>Reminder</title>
    <link rel="stylesheet" href="check_reminder.css">

    <script>
          // Function to hide overlay and iframe
          function exit() {
            var iframe = parent.document.getElementById("check_reminder_iframe");
            var overlay = parent.document.getElementById("overlay");
            iframe.style.visibility = "hidden";
            overlay.style.visibility = "hidden";
            parent.window.location.href = "mainPage.php?week_offset=<?php echo $week_offset; ?>&selected_date=<?php echo $selected_date; ?>";
        }

    </script>
</head>
<body>
   
<div class="container">
    <p id="title">Reminder</p>
    <button id="exit_button" onclick="exit();">
        <img id="exit_logo" src="images/exit.png" alt="Exit">
    </button>
<?php
 if ($result) {
    while ($row = $result->fetch_assoc()) {

      $trigger_time = date('Y-m-d H:i:s', strtotime($row['task_end_date'] . ' - ' . $row['task_reminder_time'] . ' minutes'));
      
        if (strtotime($current_time) > strtotime($trigger_time)) {
  
           echo '
           <div class="reminder_div">
                <form action="check_reminder.php" method="POST">
                    <input type="hidden" name="task_id" value="'.$row['task_id'].'">
                    <button id="close_button" type="submit">
                        <img id="close_logo" src="images/dustbin.png" alt="Close">
                    </button>
                </form>

                <p id="task_type">'.$row['task_type'].'</p>
                <p>Task Title: '.$row['task_title'].'</p>
                <p>Task End Date: '.$row['task_end_date'].'</p>
                <p>Task Description: '.$row['task_description'].'</p>

           </div>
           ';
        }
    }
}

?>

</div>
</body>
</html>