<?php
include("database.php");
session_start();
date_default_timezone_set("Asia/Kuala_Lumpur");
global $conn;

$selected_date = $_SESSION['selected_date'];  //selected date from mainPage.php
$week_offset = $_SESSION['week_offset'];

if (!isset($_SESSION["user_id"])) {
    echo "<script>parent.window.location.href = 'user_login.php';</script>";
    exit;
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT user_status FROM user WHERE BINARY user_id = $user_id";
$row = $conn->query($sql)->fetch_assoc();
$user_status = $row['user_status'];

if (isset($_GET['edit_task_id'])) {
    $task_id = (int)$_GET['edit_task_id']; 
    $sql_all = "SELECT * FROM task WHERE task_id = $task_id AND user_id = $user_id AND DATE(task_start_date) = '$selected_date'"; 
    $result = $conn->query($sql_all);
    $task = $result->fetch_assoc();
    // Fetching task details
    $task_type = $task['task_type'];
    $task_title = $task['task_title'];

    $start_date = date("H:i", strtotime($task['task_start_date']));
    $end_date = date("H:i", strtotime($task['task_end_date']));
    $task_reminder_time = $task['task_reminder_time'];
    $task_recurring_time = $task['task_recurring'];
    $task_description = $task['task_description'];
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Edit Task</title>
    <link rel="stylesheet" href="create_task.css">

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            //set default value to all attributes
            // Set task type
            var taskType = "<?php echo $task_type;?>";
            var taskTypeRadios = document.getElementsByName("task_type");
            for (var i = 0; i < taskTypeRadios.length; i++) {
                if (taskTypeRadios[i].value === taskType) {
                    taskTypeRadios[i].checked = true;
                }
            }

            // Set task title
            var taskTitle = "<?php echo $task_title; ?>";
            document.getElementById("task_title").value = taskTitle;

            // Set start and end date
            var startTime = document.getElementById("start_date");
            var endTime = document.getElementById("end_date");
  
            startTime.value = "<?php echo $start_date; ?>";
            endTime.value = "<?php echo $end_date; ?>";

            // Set reminder time
            var reminderTime = "<?php echo $task_reminder_time; ?>";
            var reminderSelect = document.getElementById("reminder_time");
            reminderSelect.value = reminderTime;

            // Set recurring time
            var recurringTime = "<?php echo $task_recurring_time; ?>";
            var recurringSelect = document.getElementById("recurring_time");
            // recurringSelect.value = recurringTime;
            var selectedDate = "<?php echo $selected_date; ?>";
            var todayDate = new Date().toISOString().split('T')[0];

            if (selectedDate !== todayDate) {
                recurringSelect.value = 0;
            } else {
                recurringSelect.value = recurringTime;
            }

            // Set note
            var note = "<?php echo $task_description; ?>";
            document.getElementById("note").value = note;

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
        });

        // Function to hide overlay and iframe
        function exit() {
            var iframe = parent.document.getElementById("edit_task_iframe");
            var overlay = parent.document.getElementById("overlay");
            iframe.style.visibility = "hidden";
            overlay.style.visibility = "hidden";
            parent.window.location.href = "mainPage.php?week_offset=<?php echo $week_offset; ?>&selected_date=<?php echo $selected_date; ?>";
        }
    </script>
</head>
<body>

<form action="edit_task.php?edit_task_id=<?= $task_id ?>" method="POST" class="container">

    <p id="category_title">Edit Task</p>
    <button id="exit_button" onclick="exit()">
        <img id="exit_logo" src="images/exit.png" alt="Exit">
    </button>
    <hr>
    <img id="category_logo" src="images/category_logo.png" alt="category logo">
    <div class="radio">
        <label><input type="radio" name="task_type" value="work"> Work</label>
        <label><input type="radio" name="task_type" value="personal"> Personal</label>
        <label><input type="radio" name="task_type" value="household"> Household</label>
        <label><input type="radio" name="task_type" value="fitness"> Fitness</label>
    </div>
    <input type="text" id="task_title" name="task_title" placeholder="Task Title" required autocomplete="off">

    <img id="calender_logo" src="images/calendar.png" alt="calender logo">
    <div class="date">
        <label for="start_date">Start Date:</label>
        <input type="time" id="start_date" name="start_date" required>

        <label for="end_date" id="end_label">End Date:</label>
        <input type="time" id="end_date" name="end_date" required>
    </div>

    <div id="reminder_recurring">
        <img id="reminder_logo" src="images/reminder.png" alt="reminder logo">
        <label for="reminder_time" id="reminder_label">Reminder:</label>
        <select name="reminder_time" id="reminder_time">
                <option value="0">None</option>
                <option value="15">15 min before</option>
                <option value="30">30 min before</option>
                <option value="60">60 min before</option>
        </select>

        <img id="recurring_logo" src="images/recurring.png" alt="recurring logo">
        <label for="recurring_time" id="recurring_label">Recurring:</label>
        <select name="recurring_time" id="recurring_time">
                <option value="0">None</option>
                <option value="1">Tomorrow</option>
                <option value="7">Weekly</option>
                <option value="30">Monthly</option>
        </select>
    </div>
    <img id="note_logo" src="images/note.png" alt="note logo">
    <textarea id="note" placeholder="Note..." name="note" style="max-height: <?= ($user_status == 'premium') ? '90px' : '200px'; ?>;" required autocomplete="off"></textarea>

    <input type="submit" value="Update" id="submit_task" name="submit_task">
</form>

</body>
</html>

<?php

echo '<script>
    if("'.$user_status.'" == "basic"){
        document.getElementById("reminder_recurring").style.display = "none";
    }
</script>';

if(isset($_POST['submit_task'])){
    $task_type = $_POST['task_type'];   
    $task_title = $_POST['task_title'];
    $start_date = date("Y-m-d H:i:s", strtotime($selected_date . ' ' . $_POST['start_date']));
    $end_date = date("Y-m-d H:i:s", strtotime($selected_date . ' ' . $_POST['end_date']));
    $task_description = $_POST['note'];

    if ($user_status == "premium") {
        $reminder_time = $_POST['reminder_time'];
        $recurring_time = $_POST['recurring_time'];
        $sql_update = "UPDATE task SET task_type = '$task_type', task_start_date = '$start_date', task_end_date = '$end_date', task_title = '$task_title', 
                    task_description = '$task_description', task_reminder_time = '$reminder_time', task_recurring = '$recurring_time' WHERE task_id = $task_id";
   
        if($reminder_time != '0'){
            $sql_update = "UPDATE task SET task_type = '$task_type', task_start_date = '$start_date', task_end_date = '$end_date', task_title = '$task_title', 
                    task_description = '$task_description', task_reminder_time = '$reminder_time', task_recurring = '$recurring_time', task_reminder_status = 'pending' 
                    WHERE task_id = $task_id";
        }
        else{
            $sql_update = "UPDATE task SET task_type = '$task_type', task_start_date = '$start_date', task_end_date = '$end_date', task_title = '$task_title', 
                    task_description = '$task_description', task_reminder_time = '$reminder_time', task_recurring = '$recurring_time', task_reminder_status = 'none' 
                    WHERE task_id = $task_id";
        }
    } 
    else {  //basic user
        $sql_update = "UPDATE task SET task_type = '$task_type', task_start_date = '$start_date', task_end_date = '$end_date', task_title = '$task_title', 
                    task_description = '$task_description' WHERE task_id = $task_id";
    }
    $result = $conn->query($sql_update);    
    if ($result) {
        echo '<script>
            setReminder(' . $reminder_time . ', "' . $task_title . '");
        </script>';

        // Insert additional task if recurring is set and the user edited the recurring part
        if ($user_status == "premium" && $recurring_time > 0 && $recurring_time != $task['task_recurring']) {
            $recurring_start_date = date("Y-m-d H:i:s", strtotime($start_date . " + $recurring_time days"));
            $recurring_end_date = date("Y-m-d H:i:s", strtotime($end_date . " + $recurring_time days"));
            $sql_insert_recurring = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring)
                                     VALUES ($user_id, '$task_type', '$recurring_start_date', '$recurring_end_date', '$task_title', '$task_description', '$reminder_time', 
                                     '$recurring_time')";
            
            if($reminder_time != '0'){
                $sql_insert_recurring = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, 
                                        task_recurring, task_reminder_status)
                                     VALUES ($user_id, '$task_type', '$recurring_start_date', '$recurring_end_date', '$task_title', '$task_description', '$reminder_time', 
                                     '$recurring_time', 'pending')";
            }
            else{
                $sql_insert_recurring = "INSERT INTO task (user_id, task_type, task_start_date, task_end_date, task_title, task_description, task_reminder_time, task_recurring, 
                                        task_reminder_status)
                                     VALUES ($user_id, '$task_type', '$recurring_start_date', '$recurring_end_date', '$task_title', '$task_description', '$reminder_time', 
                                     '$recurring_time', 'none')";
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


