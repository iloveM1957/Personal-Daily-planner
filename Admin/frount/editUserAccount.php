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
    FROM `user`
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
        <h2><strong>User Account</strong></h2>
        <table class="table">
            <thead>
                <tr>
                    <th class="text-center">ID</th>
                    <th class="text-center">Username</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Block</th>
                    <th class="text-center">Unblock</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): 
                $isBlocked = $user['status'] === 'Block';
            ?>
                <tr>
                    <td class="text-center"><?= htmlspecialchars($user['user_id']) ?></td>
                    <td class="text-center"><?= htmlspecialchars($user['user_name']) ?></td>
                    <td class="text-center">
                        <span class="<?= 
                            $isBlocked ? 'text-danger' : 'text-center' 
                        ?>">
                            <?= htmlspecialchars($user['status']) ?>
                        </span>
                    </td>
                    <td class="text-center">
                        <button class="btnEUseraccount btn-danger" 
                                <?= $isBlocked ? 'disabled' : '' ?>
                                onclick="<?= $isBlocked ? '' : 'blockUser('.$user['user_id'].')' ?>">
                            Block
                        </button>
                    </td>
                    <td class="text-center">
                        <button class="btnEUseraccount btn-success" 
                                <?= !$isBlocked ? 'disabled' : '' ?>
                                onclick="<?= !$isBlocked ? '' : 'unblockUser('.$user['user_id'].')' ?>">
                            Unblock
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info mt-4">No user data available</div>
    </div>
    <?php endif; ?>
</div>

<script>currentPage = ' ';</script>
