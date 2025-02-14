<?php
require_once '../../database.php';


$feedbacks = [];
$error = '';

// Get all feedback (associated user table)
$sql = "
    SELECT 
        f.feedback_id,
        u.user_name,
        f.feedback_content,
        DATE_FORMAT(f.feedback_date, '%d/%m/%Y') AS formatted_date
    FROM feedback f
    JOIN User u ON f.user_id = u.user_id
    ORDER BY f.feedback_date DESC
";

if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $feedbacks = $result->fetch_all(MYSQLI_ASSOC);
    }
    $result->free();
} else {
    error_log("Feedback Query Error: " . $conn->error);
    $error = "The system is busy, please try again later";
}
$conn->close();
?>

<div> 
    <h2><strong>Feedback</strong></h2>
    <div class="container_Feedback">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($feedbacks)): ?>
            <?php foreach ($feedbacks as $feedback): ?>
                <div class="boxFeedback form-boxFeedback">
                    <header>
                        <?= htmlspecialchars($feedback['user_name']) ?>
                        <span class="feedback-date">
                            <?= $feedback['formatted_date'] ?>
                        </span>
                    </header>
                    <div class="content-box">
                        <div class="feedback-content">
                            <?= htmlspecialchars($feedback['feedback_content']) ?>
                        </div>
                        <div class="space">
                            <button class="btnFeedback" 
                                    onclick="deleteFeedback(<?= $feedback['feedback_id'] ?>)">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No user feedback</div>
        <?php endif; ?>
    </div>
</div>

<script>currentPage = ' ';</script>