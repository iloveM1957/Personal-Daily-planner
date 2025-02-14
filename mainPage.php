<?php
    include("database.php");
    session_start();
    date_default_timezone_set("Asia/Kuala_Lumpur");
    global $conn;

    if (!isset($_SESSION["user_id"])) {
        echo "<script>parent.window.location.href = 'user_login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];

    $sql = "SELECT user_name, user_email, user_status, user_image FROM user WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $user_name = htmlspecialchars($row['user_name']);
        $user_email = htmlspecialchars($row['user_email']);
        $user_status = htmlspecialchars($row['user_status']);
        $user_image = $row['user_image'];
    } else {
        die("Error: No user data found.");
    }

    //print_r($row);

    // Handle the week offset from the URL query parameter
    $week_offset = isset($_GET['week_offset']) ? (int)$_GET['week_offset'] : 0;
    $_SESSION['week_offset'] = $week_offset;

    //$selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : null;

    // Get the current date
    $current_date = new DateTime();
    $current_date->modify("+$week_offset week");

    // Get today's date
    $today = new DateTime();
    $today_formatted = $today->format('Y-m-d');

    // Handle the selected date
    $selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : $today_formatted;
    $_SESSION['selected_date'] = $selected_date;
    // Get the start of the current week (Monday)
    $start_of_week = clone $current_date;
    $start_of_week->modify('this week');

    // Generate dates for the 7-day list
    $dates = [];
    for ($i = 0; $i < 7; $i++) {
        $day = clone $start_of_week;
        $day->modify("+$i day");
        $dates[] = $day;
    }

    // Get next and previous week offsets
    $next_week_offset = $week_offset + 1;
    $prev_week_offset = $week_offset - 1;
    $prev_month_offset = $week_offset - 4;
    $next_month_offset = $week_offset + 4;

    //Fetch tasks for the selected date range (if a date is clicked)
    $tasks = [];

    if ($selected_date) {
        // Convert selected_date to DateTime if needed for comparison
        $selected_date = date('Y-m-d H:i:s', strtotime($selected_date));

        // SQL query to check tasks within a date range
        //DATE() : exact the date only, ? : selected date, this sql only compate the date part instead of date&time
        //So the task like 28/1 to 30/1 can be appear before it end

        // $sql = "SELECT * FROM task WHERE user_id = ? AND DATE(task_start_date) <= ? AND DATE(task_end_date) >= ? ORDER BY TIME(task_start_date) ASC";
        $sql = "SELECT * FROM task 
            WHERE user_id = ? 
            AND DATE(task_start_date) <= ? 
            AND DATE(task_end_date) >= ? 
            AND (task_type = ? OR ? = 'all')
            ORDER BY TIME(task_start_date) ASC";

        $stmt = $conn->prepare($sql);
        $task_type = $_GET['task_type'] ?? 'all';
        //$stmt->bind_param("iss", $user_id, $selected_date, $selected_date);  // Binding selected_date as parameter ,the  ? in sql query will be replaced by selected
        $stmt->bind_param("issss", $user_id, $selected_date, $selected_date, $task_type, $task_type);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;  // Storing task details in the tasks array
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="mainPage.css">
    <title>Personal Daily Planner</title>
</head>

<body>
    <div class="planner">
        <!-- Header & Icon -->
        <div class="header">
            <!-- Month Navigation -->
            <div class="month-switch">
                <button id="prevMonth" onclick="window.location.href='?week_offset=<?= $prev_month_offset ?>'"> &lt; </button>
                <span id="currentMonth"><?= $current_date->format('F Y') ?></span>
                <button id="nextMonth" onclick="window.location.href='?week_offset=<?= $next_month_offset ?>'"> &gt; </button>
            </div>
            <!-- Icon -->
            <div class="icon-list">
                <ul class="menu">
                    <!-- Premium feature: Reminder -->
                    <?php if ($user_status == 'premium'): ?>
                        <li><a href="#" id="notification"  onclick="display('check_reminder_iframe');"><i class="fa-regular fa-bell"></i></a></li>
                    <?php endif; ?>
                   
                    <li><a href="#" id="filter-task"><i class="fa-solid fa-arrow-up-wide-short"></i></a></li>

                    <form action="mainPage.php" method="GET" id="task_type_form">
                        <!-- Hidden fields to preserve other query parameters -->
                        <input type="hidden" name="week_offset" value="<?php echo htmlspecialchars($week_offset); ?>">
                        <input type="hidden" name="selected_date" value="<?php echo htmlspecialchars($selected_date); ?>">

                        <!-- Dropdown to filter task types -->
                        <select id="task_type" name="task_type" onchange="this.form.submit()">
                            <option value="all" <?php echo ($task_type === 'all') ? 'selected' : ''; ?>>All</option>
                            <option value="work" <?php echo ($task_type === 'work') ? 'selected' : ''; ?>>Work</option>
                            <option value="personal" <?php echo ($task_type === 'personal') ? 'selected' : ''; ?>>Personal</option>
                            <option value="household" <?php echo ($task_type === 'household') ? 'selected' : ''; ?>>Household</option>
                            <option value="fitness" <?php echo ($task_type === 'fitness') ? 'selected' : ''; ?>>Fitness</option>
                        </select>
                    </form>

                    <!-- Premium feature: View statistics diagram -->
                    <?php if ($user_status == 'premium'): ?>
                        <li><a href="user_statistic.php" target="_blank" id="view-statistics"><i class="fa-solid fa-chart-column"></i></a></li>
                    <?php endif; ?>

                    <li><a href="#" id="setting"><i class="fa-solid fa-gear"></i></a></li>
                    <li><a><i class="fa-solid fa-arrow-right-from-bracket" onclick='logout();'></i></a></li>
                </ul>
            </div>
        </div>

        <!-- Settings Modal -->
        <div id="settings-modal" class="modal">
            <div class="modal-content">
                <span class="close-setting-btn" id="close-setting-btn"><i class="fa-solid fa-xmark"></i></span>

                <!-- Navigation Tabs -->
                <div class="modal-tabs">
                    <button class="tab-link active" data-tab="profile">Profile Management</button>
                    <!-- Premium user dont show upgrade section  -->
                    <?php if ($user_status == 'basic'): ?>
                        <button class="tab-link" data-tab="premium">Upgrade to Premium</button>
                    <?php endif; ?>

                    <button class="tab-link" data-tab="feedback">Write Feedback</button>
                    <button class="tab-link" data-tab="faq">FAQs</button>
                </div>

                <!-- Modal Sections -->
                <div id="profile" class="tab-content active">
                    <!-- Profile Image Upload -->
                    <div class="profile-container">
                        <div class="profile-image">
                            <img id="user-img" src="<?php echo !empty($user_image) ? $user_image : 'images/default_user.png'; ?>" alt="Profile Image">
                        </div>

                        <div class="upload-section">
                            <form action="upload_profile_photo.php" method="post" enctype="multipart/form-data">
                                <label class="custom-file-upload">
                                    Choose Photo
                                    <input type="file" name="image" id="image-upload">
                                </label>
                                <button type="submit" name="submit_photo" id="submit_photo" class="upload-btn">Upload</button>
                            </form>
                            <p class="info-text" id="uploadPhotoResponseMsg">Allowed JPG or PNG.</p>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <div class="settings-links">
                        <button class="settings-link active" data-tab="account-general"><i class="fa-solid fa-user"></i></button>
                        <button class="settings-link" data-tab="account-change-password"><i class="fa-solid fa-lock"></i></button>
                    </div>

                    <!-- General Settings -->
                    <div id="account-general" class="settings-content active">
                        <form action="user_update_profile.php" method="POST">
                            <label for="username">Username:</label>
                            <input type="text" class="user_name" id="user_name" name="user_name"
                                value="<?php echo $user_name; ?>" autocomplete="off">

                            <label for="email">Email:</label>
                            <input type="email" id="user_email" class="user_email" name="user_email" value="<?php echo $user_email; ?>" autocomplete="off">
                            <!-- display update msg -->
                            <div class="generalResponseMessage" id="generalResponseMessage"></div>
                            <button type="button" id="saveProfileButton" class="save-btn">Save Changes</button>
                        </form>


                    </div>

                    <!-- Change Password Section -->
                    <div id="account-change-password" class="settings-content">
                        <form action="user_change_password.php" method="POST">
                            <label for="old-password">Old Password:</label>
                            <input type="password" id="old-password" name="old_password">
                            <span class="eye1" onclick="password_visibility('old-password','eyeIcon_1')">
                                <img id="eyeIcon_1" src="images/close_eye.png" alt="Toggle Password Visibility" width="20px" height="20px" style="margin-left: 10px;">
                            </span>

                            <label for="new-password">New Password:</label>
                            <input type="password" id="new-password" name="new_password">
                            <span class="eye2" onclick="password_visibility('new-password','eyeIcon_2')">
                                <img id="eyeIcon_2" src="images/close_eye.png" alt="Toggle Password Visibility" width="20px" height="20px" style="margin-left: 10px;">
                            </span>

                            <label for="confirm-password">Confirm New Password:</label>
                            <input type="password" id="confirm-password" name="confirm_password">
                            <span class="eye3" onclick="password_visibility('confirm-password','eyeIcon_3')">
                                <img id="eyeIcon_3" src="images/close_eye.png" alt="Toggle Password Visibility" width="20px" height="20px" style="margin-left: 10px;">
                            </span>

                            <div class="passwordResponseMsg" id="passwordResponseMsg"></div>
                            <button type="submit" id="savePasswordButton" class="save-btn">Update Password</button>
                        </form>
                    </div>
                </div>

                <div id="premium" class="tab-content">
                    <div class="lifetime-upgrade">
                        <h2>Lifetime Subscription</h2>
                        <h3>RM 50.00 once</h2>
                            <h4>Buy Once Enjoy All</h4>
                            <button onclick="window.location.href='user_upgrade_premium.php'">Upgrade Now</button>
                    </div>
                    <div class="features">
                        <h3>Unlock below features:</h3>
                        <i class="fa-solid fa-repeat"></i>
                        <p>Create Recurring Task</p><br>
                        <i class="fa-regular fa-bell"></i>
                        <p>Create Reminder</p><br>
                        <i class="fa-solid fa-chart-column"></i>
                        <p>View Time-Spent Statistics Diagram</p>
                    </div>
                </div>

                <!-- Write feedback -->
                <div id="feedback" class="tab-content">
                    <textarea id="userFeedback" placeholder="Write your feedback here..."></textarea>
                    <button id="submitFeedback" onclick="submitFeedback()">Submit</button>
                    <div id="responseMessage"></div>
                </div>
                
                <!-- faq sql -->
                <?php 
                    $retrive_faq_sql = "SELECT * FROM FAQ";
                    $result = $conn->query($retrive_faq_sql);

                    // Store the fetched data in an array
                    $faqs = [];
                    if ($result->num_rows > 0) {
                        while ($faq = $result->fetch_assoc()) {
                            $faqs[] = $faq;
                        }
                    }
                ?>

                <!-- faq -->
                <div id="faq" class="tab-content">
                    <?php
                    // Display each FAQ question and answer dynamically
                        foreach ($faqs as $faq) {
                            echo '<div class="faq-question">';
                            echo '<button class="faq-toggle" onclick="toggleAnswer(' . htmlspecialchars($faq['faq_id']) . ')">';
                            echo '<span class="faq-question-text">Question: ' . htmlspecialchars($faq['faq_question']) . '</span>';
                            echo '</button>';
                            echo '<div id="answer' . htmlspecialchars($faq['faq_id']) . '" class="faq-answer" style="display:none;">';
                            echo '<p style="text-align:left;">Answer: ' . nl2br(htmlspecialchars($faq['faq_content'])) . '</p>';                            
                            echo '</div>';
                            echo '</div>';
                        }
                    ?>
                    <!-- New Question -->
                    <form action="user_submit_faq.php" method="POST">
                        <div class="new-question">
                            <input type="text" id="newQuesInput" autocomplete="off" placeholder="Ask a new question..." />
                            <button type="button" id="submitNewQuestionButton">Send</button>
                        </div>
                    </form>
                    
                    <!-- Send FAQ question response msg -->
                    <div class="sendFaqResponseMsg" id="sendFaqResponseMsg"></div>
                    
                </div>

            </div>
        </div>

        <!-- 7-Day List -->
        <div class="days-list">
            <div class="controls">
                <a href="?week_offset=<?= $prev_week_offset ?>">Previous Week</a>
                <a href="?week_offset=0">This Week</a>
                <a href="?week_offset=<?= $next_week_offset ?>">Next Week</a>
            </div>

            <?php foreach ($dates as $date): ?>
                <?php
                $date_formatted = $date->format('Y-m-d');

                $selected_date = isset($_GET['selected_date']) ? $_GET['selected_date'] : null;
                ?>
                <div class="day <?= ($date_formatted === $selected_date) ? 'current-day' : '' ?>" >
                    <a href="?week_offset=<?= $week_offset ?>&selected_date=<?= $date_formatted ?>">
                        <strong><?= $date->format('j') ?></strong> <!-- Day of the month -->
                        <strong><?= $date->format('D') ?></strong> <!-- Abbreviated day name -->
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Display special day -->
        <?php
        // Prepare the SQL query
        $sql = "SELECT * FROM special_day WHERE special_day_date = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $selected_date); // "s" means string

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) { ?>
            <div class="special-day">
                <p><?= htmlspecialchars($row['special_day_content']) ?></p>
            </div>
        <?php }
        ?>


        <!-- Display Tasks -->
        <div class="tasks">
            <?php if ($tasks): ?>
                <?php foreach ($tasks as $task): ?>

                    <!-- Display each task list -->
                    <!--if task_status not 'done' then append the onclick function -->
                    <div class="task-list <?= ($task['task_status'] === 'done') ? 'completed' : '' ?>">
                        <div class="task-list-container"
                            <?php if ($task['task_status'] !== 'done'): ?>
                            onclick="editTask(<?= $task['task_id'] ?>)"
                            <?php endif; ?>>

                            <div class="task-content">
                                <!-- Display Start-End -->
                                <p class="task-time">
                                    <?php
                                    // Format the task start time
                                    $start_time = date('h:i A', strtotime($task['task_start_date']));
                                    $end_time = date('h:i A', strtotime($task['task_end_date']));
                                    ?>
                                    <span class="time"><?= $start_time ?> - <?= $end_time ?></span>
                                </p>
                                <!-- Display Task Title -->
                                <h4 class="task-title"><?= ucfirst(strtolower(htmlspecialchars($task['task_type']))) . ": " . htmlspecialchars($task['task_title']) ?></h4>
                                <!-- Display Task Description -->
                                <p class="task-description"><?= htmlspecialchars($task['task_description']) ?></p>
                                <!-- Optionally display other task details -->
                            </div>
                        </div>
                        <div class="task-operation">
                            <!--  Delete Form -->
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="delete-btn"><i class="fa-regular fa-trash-can"></i></button>
                            </form>

                            <!--  Complete Form -->
                            <form method="POST" action="" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['task_id'] ?>">
                                <input type="hidden" name="action" value="complete">
                                <button type="submit" class="complete-btn">
                                    <!-- Toggle between complete and in progress -->
                                    <?php if ($task['task_status'] === 'done'): ?>
                                        <i class="fa-solid fa-check-circle"></i> <!-- Filled check for completed -->
                                    <?php else: ?>
                                        <i class="fa-regular fa-circle"></i> <!-- Empty circle for in-progress -->
                                    <?php endif; ?>
                                </button>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-task">No tasks available for this date.</div>
            <?php endif; ?>
        </div>

        <!-- Create task -->
        <iframe name="edit_task_iframe" id="edit_task_iframe" src="edit_task.php"></iframe>
        <div class="overlay" id="overlay"></div>
        <iframe name="task_iframe" id="task_iframe" src="create_task.php"></iframe>
        <iframe name="check_reminder_iframe" id="check_reminder_iframe" src="check_reminder.php"></iframe>
        <div class="create-task-container">
            <input type="submit" value="" id="create-task" onclick="display('task_iframe')">
            <i class="fa-solid fa-plus submit-icon"></i>
        </div>

        <div class="view-annoucement-container">
            <button class="view-annoucement" onclick="window.open('user_view_annoucement.php', '_blank')">
                <i class="fa-solid fa-bullhorn annoucement-icon"></i>
            </button>
        </div>

    </div>

</body>

</html>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const settingsIcon = document.getElementById("setting");
        const modal = document.getElementById("settings-modal");
        const closeBtn = document.getElementById("close-setting-btn");

        //for add event listener
        const saveProfileButton = document.getElementById("saveProfileButton");
        const savePasswordButton = document.getElementById("savePasswordButton");
        const submitPhotoButton = document.getElementById("submit_photo");
        const submitNewQuestionButton = document.getElementById("submitNewQuestionButton");
                
        const tabLinks = document.querySelectorAll(".tab-link");
        const tabContents = document.querySelectorAll(".tab-content");

        // Profile Management
        const profileTabLinks = document.querySelectorAll(".settings-link");
        const profileTabContents = document.querySelectorAll(".settings-content");

        // Open Modal when clicking the settings icon
        settingsIcon.addEventListener("click", function(event) {
            event.preventDefault();
            modal.style.display = "flex";
            document.body.classList.add("modal-active");

            // Store original values when modal opens
            const usernameInput = document.getElementById("user_name");
            const emailInput = document.getElementById("user_email");

            originalUsername = usernameInput.value.trim();
            originalEmail = emailInput.value.trim();
            clearMessages(); //clear msg
       
        });

        // Close Modal when clicking the close button
        closeBtn.addEventListener("click", function() {
            clearMessages(); //clear msg
            modal.style.display = "none";
            
            document.getElementById('userFeedback').value = ''; // Clear feedback textarea
            document.body.classList.remove("modal-active");
        });

        // Switch between main modal tabs
        tabLinks.forEach(button => {
            button.addEventListener("click", function() {
                clearMessages(); //clear msg

                let selectedTab = this.getAttribute("data-tab");

                tabLinks.forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");

                tabContents.forEach(content => content.classList.remove("active"));
                document.getElementById(selectedTab).classList.add("active");
            });
        });

        // Switch between General and Change Password in Profile Management
        profileTabLinks.forEach(button => {
            button.addEventListener("click", function() {
                clearMessages(); //clear msg

                let selectedProfileTab = this.getAttribute("data-tab");

                profileTabLinks.forEach(btn => btn.classList.remove("active"));
                this.classList.add("active");

                profileTabContents.forEach(content => content.classList.remove("active"));
                document.getElementById(selectedProfileTab).classList.add("active");
            });
        });

        // Close modal if user clicks outside the modal box
        window.addEventListener("click", function(event) {
            if (event.target === modal) {
                modal.style.display = "none";
                clearMessages(); //clear msg
                document.body.classList.remove("modal-active");
            }
        });

        document.getElementById('task_type').style.display = 'none';
        document.getElementById('filter-task').addEventListener('click', function() {
            let taskType = document.getElementById('task_type');

            taskType.style.display = taskType.style.display === 'none' ? 'block' : 'none';
        });

        // Attach function
        saveProfileButton.addEventListener("click", submitUpdateProfile);
        savePasswordButton.addEventListener("click", submitUpdatePassword);
        submitPhotoButton.addEventListener("click", updatePhoto);
        submitNewQuestionButton.addEventListener("click", submitNewFaq);

    });

    function logout() {
        window.location.href = "logout.php";
    }
    
    // update photo
    function updatePhoto(event) {
        event.preventDefault();
        console.log(" updatePhoto function triggered!");

        const uploadPhotoResponseMsg = document.getElementById("uploadPhotoResponseMsg");
        let fileInput = document.getElementById("image-upload");


        let formData = new FormData();
        formData.append("image", fileInput.files[0]);

        fetch("upload_profile_photo.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log(" Server Response:", data);

            if (data.success) {
                let imgElement = document.getElementById("user-img");
                if (imgElement) {
                    imgElement.src = data.image + "?t=" + new Date().getTime();
                }

                uploadPhotoResponseMsg.innerHTML = " Profile photo updated successfully!";
                uploadPhotoResponseMsg.style.color = "green";
            } else {
                uploadPhotoResponseMsg.innerHTML = " Error: " + data.message;
                uploadPhotoResponseMsg.style.color = "red";
            }
        })
        .catch(error => {
            console.error(" Error:", error);
            uploadPhotoResponseMsg.innerHTML = " An unexpected error occurred.";
            uploadPhotoResponseMsg.style.color = "red";
        });
    }

    let originalUsername = "";
    let originalEmail = "";

    // Function to handle profile update
    function submitUpdateProfile(event) {
        event.preventDefault();

        const usernameInput = document.getElementById("user_name");
        const emailInput = document.getElementById("user_email");
        const generalResponseMessage = document.getElementById("generalResponseMessage");
        const saveProfileButton = document.getElementById("saveProfileButton");

        let newUsername = usernameInput.value.trim();
        let newEmail = emailInput.value.trim();

        generalResponseMessage.innerHTML = "";

        // Detect if no changes were made
        if (newUsername === originalUsername && newEmail === originalEmail) {
            generalResponseMessage.innerHTML = "<p style='color: orange;'>No changes detected.</p>";
            return;
        }

        // Disable button to prevent multiple submissions
        saveProfileButton.disabled = true;

        // Prepare form data for AJAX request
        let formData = new FormData();
        formData.append("user_name", newUsername);
        formData.append("user_email", newEmail);

        fetch("user_update_profile.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text()) // Fetch response as text
            .then(text => {
                try {
                    let data = JSON.parse(text); // Try to parse JSON
                    if (data.status === "success") {
                        generalResponseMessage.innerHTML = "<p style='color: green;'>" + data.message + "</p>";
                        originalUsername = newUsername; // Update stored values
                        originalEmail = newEmail;
                    } else {
                        // Display error messages
                        generalResponseMessage.innerHTML = "<p style='color: red;'>" +
                            (data.messages ? data.messages.join("<br>") : data.message) +
                            "</p>";

                        // Restore previous values
                        usernameInput.value = originalUsername;
                        emailInput.value = originalEmail;
                    }
                } catch (error) {
                    generalResponseMessage.innerHTML = "<p style='color: red;'>Unexpected server response.</p>";
                    console.error("Response Error:", text);

                    // Restore previous values
                    usernameInput.value = originalUsername;
                    emailInput.value = originalEmail;
                }
            })
            .catch(error => {
                generalResponseMessage.innerHTML = "<p style='color: red;'>An error occurred while updating profile.</p>";
                console.error("Fetch Error:", error);
            })
            .finally(() => {
                saveProfileButton.disabled = false; // Re-enable button after request
            });
    }

    
    //password visibility
    function password_visibility(input_id, eye_icon) {
        var input_id = document.getElementById(input_id);
        var eye_icon = document.getElementById(eye_icon);

        if (input_id.type === 'password') {
            input_id.type = 'text';
            eye_icon.src = 'images/open_eye.png'; 
        } 
        else {
            input_id.type = 'password';
            eye_icon.src = 'images/close_eye.png'; 
        }
      }
      


    // change password
    function submitUpdatePassword(){
        event.preventDefault();

        const oldPass = document.getElementById("old-password").value;
        const newPass = document.getElementById("new-password").value;
        const confirmPass = document.getElementById("confirm-password").value;
        const passwordResponseMsg = document.getElementById("passwordResponseMsg");
        const savePasswordButton = document.getElementById("saveProfileButton");

        passwordResponseMsg.innerHTML="";

        saveProfileButton.disabled = true;

        // Prepare form data for AJAX request
        let formData = new FormData();
        formData.append("old_password", oldPass);
        formData.append("new_password", newPass);
        formData.append("confirm_password", confirmPass);

        // Send the form data via fetch to the server
        fetch("user_change_password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())  // Assuming the server returns JSON
        .then(data => {
            if (data.success) {
                passwordResponseMsg.innerHTML = "Password updated successfully!";
                passwordResponseMsg.style.color = "green";
            } else {
                passwordResponseMsg.innerHTML = "Error: " + data.message;
                passwordResponseMsg.style.color = "red";
            }
            savePasswordButton.disabled = false;  // Re-enable button
        })
        .catch(error => {
            console.error("Error updating password:", error);
            passwordResponseMsg.innerHTML = "An error occurred. Please try again later.";
            passwordResponseMsg.style.color = "red";
            savePasswordButton.disabled = false;  // Re-enable button
        });
    }


    // submit feedback
    function submitFeedback() {
        var feedbackText = document.getElementById('userFeedback').value;

        // If feedback is empty, show an alert
        if (feedbackText.trim() === "") {
            alert("Please provide your feedback.");
            return;
        }

        // Create a new FormData object to send the data
        var formData = new FormData();
        formData.append('feedback', feedbackText);

        // Create a new XMLHttpRequest object
        var xhr = new XMLHttpRequest();

        // Open the request (POST method to 'user_submit_feedback.php')
        xhr.open("POST", "user_submit_feedback.php", true);

        // Set the callback function for when the request completes
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('responseMessage').innerHTML = xhr.responseText;
                document.getElementById('userFeedback').value = ""; // Clear the textarea
            } else {
                document.getElementById('responseMessage').innerHTML = "Error submitting feedback. Please try again.";
            }
        };

        // Send the data to the server
        xhr.send(formData);
    }

    //Clear response msg when input field is clicked
    const fields = [
        // Profile
        { id: 'user_name', messageId: 'generalResponseMessage' },
        { id: 'user_email', messageId: 'generalResponseMessage' },

        // Password
        { id: 'old-password', messageId: 'passwordResponseMsg' },
        { id: 'new-password', messageId: 'passwordResponseMsg' },
        { id: 'confirm-password', messageId: 'passwordResponseMsg' },

        // Feedback
        { id: 'userFeedback', messageId: 'responseMessage' },

        // FAQ
        { id: 'newQuesInput', messageId: 'sendFaqResponseMsg' }
    ];

    fields.forEach(field => {
        const inputElement = document.getElementById(field.id);
        if (inputElement) {
            inputElement.addEventListener('focus', function () {
                document.getElementById(field.messageId).innerHTML = '';
            });
        }
    });

    // Clear msg called in domcontentloaded, switching tab or close setting window will clear the reponse msg
    function clearMessages() {
        document.getElementById("generalResponseMessage").innerHTML = "";
        document.getElementById("passwordResponseMsg").innerHTML = "";
        document.getElementById("responseMessage").innerHTML = "";
        document.getElementById("sendFaqResponseMsg").innerHTML = "";
        document.getElementById("uploadPhotoResponseMsg").innerHTML = "";

        document.getElementById('userFeedback').value = ''; // Clear feedback textarea
        document.getElementById('newQuesInput').value = ''; // Clear FAQ text

        // Clear password fields
        document.getElementById('old-password').value = '';
        document.getElementById('new-password').value = '';
        document.getElementById('confirm-password').value = '';

        //let the username, and email become original one 
        document.getElementById('user_name').value = originalUsername;
        document.getElementById('user_email').value = originalEmail;
    }


    // question answer visible
    function toggleAnswer(faqId) {
        const answer = document.getElementById(`answer${faqId}`);
        const isVisible = answer.style.display === 'block';

        // Hide all answers first (optional, if you want only one answer to be visible at a time)
        const allAnswers = document.querySelectorAll('.faq-answer');
        allAnswers.forEach(ans => ans.style.display = 'none');

        // Toggle the selected answer
        if (!isVisible) {
            answer.style.display = 'block';
        }
    }

    // user submit question
    function submitNewFaq(event) {
        event.preventDefault(); // Prevent page reload

        const newQuesInput = document.getElementById("newQuesInput");
        const sendFaqResponseMsg = document.getElementById("sendFaqResponseMsg");
        const submitNewQuestionButton = document.getElementById("submitNewQuestionButton");

        let newQues = newQuesInput.value.trim();
        sendFaqResponseMsg.innerHTML = "";

        // Disable button to prevent multiple submissions
        submitNewQuestionButton.disabled = true;

        // Validate input
        if (newQues === "") {
            sendFaqResponseMsg.innerHTML = "<p style='color: red;'>Please enter a question before submitting.</p>";
            submitNewQuestionButton.disabled = false;
            return;
        }

        // Prepare form data for AJAX request
        let formData = new FormData();
        formData.append("faq_question", newQues);

        // Send data to PHP script via fetch
        fetch("user_submit_faq.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // ✅ Ensure response is JSON
        .then(data => {
            console.log("Parsed JSON:", data); // Debugging: Log parsed response
            if (data.status === "success") {  // ✅ Check for "status" instead of "success"
                sendFaqResponseMsg.innerHTML = "<p style='color: green;'>" + data.message + "</p>";
                newQuesInput.value = ""; // Clear input field
            } else {
                sendFaqResponseMsg.innerHTML = "<p style='color: red;'>" + data.message + "</p>";
            }
        })
        .catch(error => {
            sendFaqResponseMsg.innerHTML = "<p style='color: red;'>An error occurred. Please try again later.</p>";
            console.error("Fetch Error:", error);
        })
        .finally(() => {
            submitNewQuestionButton.disabled = false;
        });
    }

    function display(iframeName) {
        var iframe = parent.document.getElementById(iframeName);
        var overlay = parent.document.getElementById("overlay");
        iframe.style.visibility = "visible";
        overlay.style.visibility = "visible";
    }

    function editTask(taskId) {
        var iframe = parent.document.getElementById("edit_task_iframe");
        var overlay = parent.document.getElementById("overlay");
        iframe.src = "edit_task.php?edit_task_id=" + taskId;
        iframe.style.visibility = "visible";
        overlay.style.visibility = "visible";
    }
</script>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']); // Ensure task_id is an integer

    if ($_POST['action'] === 'delete') {
        // DELETE Task Query
        $sql = "DELETE FROM task WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);

        if ($stmt->execute()) {
            echo "<script>
                window.location.href='mainPage.php?week_offset=$week_offset&selected_date=$_SESSION[selected_date]';
            </script>";
        } else {
            echo "Error deleting task.";
        }
    }

    if ($_POST['action'] === 'complete') {
        // Get the current task status
        $sql = "SELECT task_status FROM task WHERE task_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $task_id);
        //Retrive the task status from given task id
        $stmt->execute(); //Runs the prepared SQL query
        $result = $stmt->get_result(); //Gets the result set (the query's output)
        $task = $result->fetch_assoc();

        if ($task) {
            // Toggle between 'done' and 'in progress'
            //if cliked a done task, change it into in progress
            //if clicked a progressing task, turn it into done
            $new_status = ($task['task_status'] === 'done') ? 'in progress' : 'done';

            // Update the task status
            $sql = "UPDATE task SET task_status = ? WHERE task_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $new_status, $task_id); //string and integer

            if ($stmt->execute()) {
                echo "<script>
                    window.location.href='mainPage.php?week_offset=$week_offset&selected_date=$selected_date';
                </script>";
            } else {
                echo "Error updating task.";
            }
        }
    }
}
?>