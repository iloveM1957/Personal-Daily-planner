<?php
    include("database.php");
    session_start();
    date_default_timezone_set("Asia/Kuala_Lumpur");
    if (!isset($_SESSION["user_id"])) {
        echo "<script>parent.window.location.href = 'user_login.php';</script>";
        exit;
    }
    global $conn;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Type Pie Chart</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.7.0"></script> <!-- Include the plugin -->
    <link rel = "stylesheet" href = "user_statistic.css">
</head>
<body>


    <div class="chart-container">
        <!-- Date Range Picker -->
        <form method="POST" action="user_statistic.php" id="date">
            <label for="start_date">From:</label>
            <input type="date" id="start_date" name="start_date" onchange="updateEndDateMin()" required>
            <label for="end_date">To:</label>
            <input type="date" id="end_date" name="end_date" onchange="updateEndDateMin()" required>
            <input type="submit" value="Show Tasks" id="show_tasks">
        </form>

        <!-- display error message when no data -->
        <div id="noDataMessage">
            <img src="images/error.png" alt="No Data" id="noDataImage">
            <label id="noDataMessageText">No task data available for the selected date range.</label>

        </div>

        <canvas id="taskChart"></canvas>
    </div>

    <?php
    // Default date range: current month
    $start_date = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
    $end_date = isset($_POST['end_date']) ? $_POST['end_date'] : date('Y-m-t');


    // If the start and end date are the same, use the same day in the query
    if ($start_date === $end_date) {
        $sql = "SELECT task_type, COUNT(*) as task_count 
                FROM task 
                WHERE task_start_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                GROUP BY task_type";
    } else {
        $sql = "SELECT task_type, COUNT(*) as task_count 
                FROM task 
                WHERE task_start_date BETWEEN '$start_date 00:00:00' AND '$end_date 23:59:59'
                GROUP BY task_type";
    }
    
    $result = $conn->query($sql);

    // Prepare data for the chart
    $taskTypes = [];
    $taskCounts = [];
    $totalTasks = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $taskTypes[] = ucfirst($row['task_type']); // Capitalize task types
            $taskCounts[] = $row['task_count'];
            $totalTasks += $row['task_count'];  // Calculate the total tasks
        }
    }

    $conn->close();
    ?>

    <script>
        // Task types and counts from PHP
        var taskTypes = <?php echo json_encode($taskTypes); ?>;
        var taskCounts = <?php echo json_encode($taskCounts); ?>;
        var totalTasks = <?php echo $totalTasks; ?>; // Total number of tasks

        if(totalTasks == 0){
            document.getElementById("taskChart").style.display = "none"; // Hide the canvas if no data
            document.getElementById("noDataMessage").style.display = "block"; // Show the message
        }
        else{
            document.getElementById("noDataMessage").style.display = "none"; // Hide the message
            document.getElementById("taskChart").style.display = "block"; // Show the canvas
        }


        // Calculate the percentage for each task type
        var taskPercentages = taskCounts.map(function(count) {
            return ((count / totalTasks) * 100).toFixed(2); // Round to nearest integer
        });


        // Display the pie chart
        new Chart("taskChart", {
            type: "pie",
            data: {
                labels: taskTypes,
                datasets: [{
                    backgroundColor: ["#FF6384", "#36A2EB", "#FFCE56", "#4CAF50"],
                    data: taskCounts
                }]
            },
            options: {
                title: {
                    display: true,
                    text: "My Time Spent Report",
                    fontSize: 20
                },
                plugins: {
                    datalabels: {
                        formatter: function(value, ctx) {
                            var index = ctx.dataIndex;
                            var percentage = taskPercentages[index];
                            return taskTypes[index] + ': ' + percentage + '%'; // Show percentage with task type
                        },
                        color: '#fff',
                        font: {
                            weight: 'bold',
                            size: 12
                        }
                    }
                }
            }
        });


    function updateEndDateMin() {
        var endDate = document.getElementById("end_date");
        var startDate = document.getElementById("start_date");

        endDate.min = startDate.value;
    }

    window.onload = function() {
        var startTime = document.getElementById("start_date");
        var endTime = document.getElementById("end_date");

        startTime.value = "<?php echo $start_date; ?>";
        endTime.value = "<?php echo $end_date; ?>";


        startTime.max = endTime.value;
        endTime.min = startTime.value;


        // Validate dates on change
        startTime.addEventListener("change", validateDates);
        endTime.addEventListener("change", validateDates);

        function validateDates() {
            var startTimeValue = document.getElementById("start_date").value;
            var endTimeValue = document.getElementById("end_date").value;

            if (endTimeValue < startTimeValue) {
                alert("End Date must be after Start Date");
                document.getElementById("end_date").value = "";
                document.getElementById("end_date").focus();
            }
        }
    };

    </script>

</body>
</html>
