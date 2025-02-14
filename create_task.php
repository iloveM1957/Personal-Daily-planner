<?php
    include("database.php");
    session_start();
    date_default_timezone_set("Asia/Kuala_Lumpur");
    global $conn;
    
    $currentTime = date("H:i");
    $endTime = date("H:i", strtotime("+1 hour"));

    if (!isset($_SESSION["user_id"])) {
        // If not set, redirect to the login page
        echo "<script>parent.window.location.href = 'user_login.php';</script>";
        exit;
    }
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT user_status FROM user WHERE BINARY user_id = '$user_id'";
    $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
    $user_status = $row['user_status'];
    $selected_date = $_SESSION['selected_date'];  //selected date from mainPage.php
    $week_offset = $_SESSION['week_offset'];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Create Task</title>
    <link rel="stylesheet" href="create_task.css">

    <script>
        window.onload = function() {
            var startTime = document.getElementById("start_date");
            var endTime = document.getElementById("end_date");
  
            startTime.value = "<?php echo $currentTime; ?>";
            endTime.value = "<?php echo $endTime; ?>";

            // Validate dates on change
            startTime.addEventListener("change", validateDates);
            endTime.addEventListener("change", validateDates);

            function validateDates() {
                var startTimeValue = document.getElementById("start_date").value;
                var endTimeValue = document.getElementById("end_date").value;

                if (endTimeValue <= startTimeValue) {
                    alert("End Date must be after Start Date");
                    document.getElementById("end_date").value = "";
                    document.getElementById("end_date").focus();
                }
            }
        };

        // Function to hide overlay and iframe
        function exit() {
            var iframe = parent.document.getElementById("task_iframe");
            var overlay = parent.document.getElementById("overlay");
            iframe.style.visibility = "hidden";
            overlay.style.visibility = "hidden";
            parent.window.location.href = "mainPage.php?week_offset=<?php echo $week_offset; ?>&selected_date=<?php echo $selected_date; ?>";
        }
    </script>
</head>
<body>

<form action="create_task.php" method="POST" class="container">
    <p id="category_title">Create New Task</p>
    <button id="exit_button" onclick="exit()">
        <img id="exit_logo" src="images/exit.png" alt="Exit">
    </button>
    <hr>
    <img id="category_logo" src="images/category_logo.png" alt="category logo">
    <div class="radio">
        <label><input type="radio" name="task_type" value="work" checked> Work</label>
        <label><input type="radio" name="task_type" value="personal"> Personal</label>
        <label><input type="radio" name="task_type" value="household"> Household</label>
        <label><input type="radio" name="task_type" value="fitness"> Fitness</label>
    </div>
    <input type="text" id="task_title" name="task_title" placeholder="Task Title" required autocomplete="off">

    <img id="calender_logo" src="images/calendar.png" alt="calender logo">
    <div class="date">
        <label for="start_date">Start Time:</label>
        <input type="time" id="start_date" name="start_date" required>

        <label for="end_date" id="end_label">End Time:</label>
        <input type="time" id="end_date" name="end_date" required>
    </div>

    <?php
        if($user_status == "premium"){
    ?>
        <img id="reminder_logo" src="images/reminder.png" alt="reminder logo">
        <label for="reminder_time" id="reminder_label">Reminder:</label>
        <select name="reminder_time" id="reminder_time">
                <option value="0" selected>None</option>
                <option value="15">15 min before</option>
                <option value="30">30 min before</option>
                <option value="60">60 min before</option>
        </select>

        <img id="recurring_logo" src="images/recurring.png" alt="recurring logo">
        <label for="recurring_time" id="recurring_label">Recurring:</label>
        <select name="recurring_time" id="recurring_time">
                <option value="0" selected>None</option>
                <option value="1">Tomorrow</option>
                <option value="7">Weekly</option>
                <option value="30">Monthly</option>
        </select>
    <?php
        }
        else{
            echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                 var noteElement = document.getElementById('note');
                 if (noteElement) {
                     noteElement.style.maxHeight = '200px';
                 }
             });
         </script>"; 
        }
    ?>
    <img id="note_logo" src="images/note.png" alt="note logo">
    <textarea id="note" placeholder="Note..." name="note" required autocomplete="off"></textarea>

    <input type="submit" value="Create" id="submit_task" name="submit_task">
</form>

</body>
</html>

<?php
    if(isset($_POST['submit_task'])){
        $task_type = $_POST['task_type'];   
        $task_title = $_POST['task_title'];
        $start_date = date("Y-m-d H:i:s", strtotime($selected_date . ' ' . $_POST['start_date']));
        $end_date = date("Y-m-d H:i:s", strtotime($selected_date . ' ' . $_POST['end_date']));
        $task_description = $_POST['note'];
   
        if ($user_status == "premium") {
            $reminder_time = $_POST['reminder_time'];
            $recurring_time = $_POST['recurring_time'];
            $sql_insert = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring)
                            VALUES ($user_id, '$task_type', '$start_date', '$end_date', '$task_title', '$task_description', '$reminder_time', '$recurring_time')";
        
            if($reminder_time != '0'){
                $sql_insert = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring, task_reminder_status)
                            VALUES ($user_id, '$task_type', '$start_date', '$end_date', '$task_title', '$task_description', '$reminder_time', '$recurring_time', 'pending')";
            }
        } 
        else {  //basic user
            $sql_insert = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description)
                            VALUES ($user_id, '$task_type', '$start_date', '$end_date', '$task_title', '$task_description')";
        }

        $result = $conn->query($sql_insert);
        if ($result) {
            // Insert additional task if recurring is set
            if ($user_status == "premium" && $recurring_time > 0) {
                $recurring_start_date = date("Y-m-d H:i:s", strtotime($start_date . " + $recurring_time days"));
                $recurring_end_date = date("Y-m-d H:i:s", strtotime($end_date . " + $recurring_time days"));
                $sql_insert_recurring = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring)
                                         VALUES ($user_id, '$task_type', '$recurring_start_date', '$recurring_end_date', '$task_title', '$task_description', '$reminder_time', '$recurring_time')";
                
                if($reminder_time != '0'){
                    $sql_insert_recurring = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring, task_reminder_status)
                                         VALUES ($user_id, '$task_type', '$recurring_start_date', '$recurring_end_date', '$task_title', '$task_description', '$reminder_time', '$recurring_time', 'pending')";
                }  
                $conn->query($sql_insert_recurring);
            }

            echo '<script>
                exit();
            </script>';
        } else {
            echo '<script>
                alert("Something went wrong."); 
                window.location.href = "user_login.php";
            </script>'; 
        }
        
    }
 
?>

