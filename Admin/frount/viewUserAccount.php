<?php
// Keep the original PHP logic unchanged
require_once '../../database.php';

$users = [];
$error = '';

$sql = "
    SELECT 
        `user_id`,
        `user_name`,
        `user_email`,
        CASE 
            WHEN `user_block_status` = 'Block' THEN 'Block' 
            ELSE `user_status` 
        END AS status,
        `user_reg_date`
    FROM `User`
";

if ($result = $conn->query($sql)) {
    if ($result->num_rows > 0) {
        $users = $result->fetch_all(MYSQLI_ASSOC);
    }
    $result->free();
} else {
    error_log("User Query Failed: " . $conn->error);
    $error = "The system is busy, please try again later";
}

$conn->close();
?>

<div class="container_main allContent-section py-4">
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger mb-4"><?= $error ?></div>
    <?php elseif (!empty($users)): ?>
      <div>
        <h2><strong>User Account</strong></h2>  <!-- The title is placed directly above the table -->
        <table class="table">  <!-- Keep only the base table class -->
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Email</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Joining Date</th>  <!-- Fix column headers -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars($user['user_id']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($user['user_name']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($user['user_email']) ?></td>
                    <td class="text-center">
                        <span class="<?= 
                            ($user['status'] == 'Blocked') ? 'danger' : 
                            (($user['status'] == 'Premium') ? 'success' : 'primary') 
                        ?>">
                            <?= htmlspecialchars($user['status']) ?>
                        </span>
                    </td>
                    <td class="text-center"><?= htmlspecialchars(date('Y-m-d', strtotime($user['user_reg_date']))) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No user data</div>
    </div>
    <?php endif; ?>
</div>

<script>currentPage = ' ';</script>