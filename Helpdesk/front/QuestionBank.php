<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Bank</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>

<?php include '../../database.php'; ?> <!-- Include database connection -->

<div class="container_QuestionBank">
    <h2 class="title">Question Bank</h2>

    <ul id="questionList" class="list-group">
    <?php
    // Include database connection
    include '../../database.php';

    // Fetch questions from the database
    $sql = "SELECT q.question_id, q.question_content, q.status, q.question_date, u.user_id, u.user_name, u.user_email 
        FROM question q
        JOIN user u ON q.user_id = u.user_id 
        ORDER BY FIELD(q.status, 'pending', 'done'), q.question_date DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $statusClass = ($row["status"] === "done") ? "settled" : "";
        $buttonText = ($row["status"] === "done") ? "Undo" : "Done";
        $buttonClass = ($row["status"] === "done") ? "undo-btn" : "done-btn";

        // Display user details and question content
        echo '<li class="list-group-item question-box ' . $statusClass . '" data-id="' . $row["question_id"] . '">
                <div>
                    <p>User ID: ' . htmlspecialchars($row["user_id"]) . '</p>  <!-- Display user_id -->
                    <p>User Name: ' . htmlspecialchars($row["user_name"]) . '</p>  <!-- Display user_name -->
                    <p>User Email: ' . htmlspecialchars($row["user_email"]) . '</p>  <!-- Display user_email -->
                    <p>' . htmlspecialchars($row["question_content"]) . '</p>  <!-- Display question content -->
                </div>
                <button class="' . $buttonClass . '" onclick="toggleStatus(this, ' . $row["question_id"] . ')">' . $buttonText . '</button>
              </li>';
    }
} else {
    echo "<li class='list-group-item'>No questions available.</li>";
}

$conn->close();
    ?>
</ul>


</div>

<script>
function toggleStatus(button, questionId) {
    let parentBox = button.closest(".question-box");
    let questionList = document.getElementById("questionList");

    // Send AJAX request to update the status
    fetch("./function/update_status.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `question_id=${questionId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (data.new_status === "done") {
                parentBox.classList.add("settled");
                button.innerText = "Undo";
                button.classList.remove("done-btn");
                button.classList.add("undo-btn");

                // Move to the bottom
                questionList.appendChild(parentBox);
            } else {
                parentBox.classList.remove("settled");
                button.innerText = "Done";
                button.classList.remove("undo-btn");
                button.classList.add("done-btn");

                // Move to the top
                questionList.insertBefore(parentBox, questionList.firstChild);
            }
        } else {
            alert("Failed to update status: " + data.error);
        }
    })
    .catch(error => console.error("Error:", error));
}
</script>

</body>
</html>
