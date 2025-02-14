<?php
require_once '../../database.php';


$specialDays = [];
$error = '';

// Get all special event
$sql = "SELECT * FROM special_day ORDER BY special_day_date DESC";
if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $specialDays = $result->fetch_all(MYSQLI_ASSOC);
    }
    $result->free();
} else {
    error_log("Special Day Query Error: " . $conn->error);
    $error = "The system is busy, please try again later";
}
$conn->close();
?>

<div>
    <h2><strong>Special Day</strong></h2>
    <div class="container_Feedback">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php elseif (!empty($specialDays)): ?>
            <?php foreach ($specialDays as $day): ?>
                <div class="boxFeedback form-boxFeedback">
                    <header><?= date('d/m/Y', strtotime($day['special_day_date'])) ?></header>
                    <div class="content-box">
                        <div class="text-left"><?= htmlspecialchars($day['special_day_content']) ?></div>
                        <div class="space">
                            <button class="btnFeedback" 
                                    onclick="deleteSpecial(<?= $day['special_day_id'] ?>)">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No Special Event</div>
        <?php endif; ?>
    </div>
    <div class="rightSpecial">
        <button id="postButton" class="btnAnnOpen">Post</button>
    </div>
</div>

<?php include 'popSpe.php'; ?>

<script src="./style/js/popmsg.js"></script>
<script>currentPage = ' ';</script>