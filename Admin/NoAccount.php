<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Account reminder</title>
    <style>
    .warningBoxPro {
        background: #fff3cd;
        border: 1px solid #ffeeba;
        padding: 20px;
        margin: 50px auto;
        width: 80%;
        max-width: 600px;
        border-radius: 8px;
        text-align: center;
    }

    </style>
</head>
<body>
    <?php if (!isset($_SESSION['admin_id'])): ?>
        <div class="warningBoxPro">
            <h2>⚠️ restricted access</h2>
            <p>You need to log in to view complete information</p>
            <a href="../user_login.php" class="loginLinkPro">Log in now</a>
        </div>
    <?php else: ?>
        <script>
            // If logged in, automatically return to the original page.
            window.history.back();
        </script>
    <?php endif; ?>
</body>
</html>