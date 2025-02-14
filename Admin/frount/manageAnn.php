<?php
require '../../database.php';



?>
<div>
    <h2><strong>Announcement Management</strong></h2>
    <div class="annContainer">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php else: ?>
            <?php
            $sql = "SELECT * FROM Announcement ORDER BY announcement_date DESC";
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0):
                while ($row = mysqli_fetch_assoc($result)):
            ?>
                <button class="annAccordion"><?= htmlspecialchars($row['announcement_tittle']) ?></button>
                <div class="annPanel">
                    <p><strong> <?= htmlspecialchars($row['announcement_date']) ?></strong></p>
                    <p><?= nl2br(htmlspecialchars($row['announcement_content'])) ?></p>
                    <div class="spaceAnn">
                        <button class="btnFeedback" 
                                onclick="deleteAnnouncement(<?= $row['announcement_id'] ?>)">
                                Delete
                        </button>
                    </div>
                </div>
            <?php
                endwhile;
            else:
            ?>
                <div class="alert alert-info">No announcements found</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="rightSpecial">
        <button id="postButton" class="btnAnnOpen" onclick="openAnnPopup()">Post</button>
    </div>
</div>

<?php include 'popAnn.php'; ?>

<script src="./style/js/popmsg.js"></script>
<script src="./style/js/ann.js"></script>
<script>
    function openAnnPopup() {
        document.getElementById('annPopup').style.display = 'block';
    }
</script>