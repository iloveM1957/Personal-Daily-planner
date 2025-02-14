//********************* User account related *********************//
function editUserAccount() {  
    $.ajax({
        url: "./frount/editUserAccount.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function viewUserAccount() {
    $.ajax({
        url: "./frount/viewUserAccount.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function uploadSpecial() {
    $.ajax({
        url: "./frount/uploadSpecial.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function viewFeedback() {
    $.ajax({
        url: "./frount/viewFeedback.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function manageSpecial() {
    $.ajax({
        url: "./frount/manageSpecial.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function manageAnn() {
    $.ajax({
        url: "./frount/manageAnn.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

function Profile() {
    $.ajax({
        url: "./frount/Profile.php",
        method: "POST",
        data: { record: 1 },
        success: function(data) {
            $('.allContent-section').html(data);
        }
    });
}

//********************* Login related *********************//
function login(email, password) {
    $.ajax({
        url: "./function/loginDB.php",
        method: "POST",
        data: {
            email: email,
            password: password
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                window.location.href = "index.php"; // Jump to administrator panel
            } else {
                alert(response.error || "Login failed"); // Show specific error information
            }
        },
        error: function(xhr) {
            try {
                const error = JSON.parse(xhr.responseText);
                alert(error.error || "Login error");
            } catch {
                alert("Connection error");
            }
        }
    });
}

//********************* 注册相关 *********************//
function register(username, email, password) {
    console.log("Registration attempt:", { username, email });
    
    $.ajax({
        url: "./function/registerDB.php",
        method: "POST",
        data: {
            username: username,
            email: email,
            password: password
        },
        dataType: "json",
        success: function(response) {
    console.log("Server response:", response);
    if (response.success) {
        console.log("Registration successful, redirecting...");
        window.location.href = "index.php"; // After successful registration, jump to the login page
    } else {
        console.error("Registration error:", response.error);
        alert("Error: " + (response.error || "Unknown error"));
            }
        },
        error: function(xhr, status, error) {
            console.error("AJAX error:", status, error);
            alert("An error occurred: " + error);
        }
    });
}

//********************* Verification code related *********************//
function verifyCode(verification_code) {
    $.ajax({
        url: "./function/verificationDB.php",
        method: "POST",
        data: {
            verification_code: verification_code
        },
        success: function(response) {
            if (response === "valid") {
                window.location.href = "register.php"; // Jump to the login page after successful verification
            } else {
                alert("Invalid verification code!"); // Verification failure prompt
            }
        },
        error: function() {
            alert("An error occurred. Please try again.");
        }
    });
}

//************************ Block/Unblock  ******************************//
function handleBlockAction(userId, actionType) {
    const confirmMsg = actionType === 'block' 
        ? 'Are you sure you want to block this user?' 
        : 'Are you sure you want to unblock this user?';
    
    if (confirm(confirmMsg)) {
        $.ajax({
            url: "./function/blockUserDB.php",
            method: "POST",
            data: {
                user_id: userId,
                action: actionType
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    editUserAccount(); // Refresh interface
                } else {
                    alert(response.error || 'Operation failed');
                }
            },
            error: function() {
                alert('Request failed, please try again');
            }
        });
    }
}

// Ban user
function blockUser(userId) {
    handleBlockAction(userId, 'block');
}

// Unblock user
function unblockUser(userId) {
    handleBlockAction(userId, 'unblock');
}

//********************* Special Day  *********************//
function deleteSpecial(specialId) {
    if (confirm('Are you sure you want to delete this special day?')) {
        $.ajax({
            url: "./function/specialDayDB.php",
            method: "POST",
            data: {
                action: 'delete',
                special_id: specialId
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    manageSpecial(); // Refresh list
                } else {
                    alert(response.error || 'Delete failed');
                }
            },
            error: function() {
                alert('Request failed, please try again');
            }
        });
    }
}

// Form submission processing
function submitSpecialDay() {
    const date = document.getElementById('annDate').value;
    const eventName = document.getElementById('eventName').value;

    $.ajax({
        url: "./function/specialDayDB.php",
        method: "POST",
        data: {
            action: 'add',
            date: date,
            content: eventName
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                manageSpecial(); // Refresh list
            } else {
                alert(response.error || 'Add failed');
            }
        },
        error: function() {
            alert('Request failed, please try again');
        }
    });
}

//********************* Feedback  *********************//
function deleteFeedback(feedbackId) {
    if (confirm('Are you sure you want to delete this feedback?')) {
        $.ajax({
            url: "./function/feedbackDB.php",
            method: "POST",
            data: {
                action: 'delete',
                feedback_id: feedbackId
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    viewFeedback(); // Refresh list
                } else {
                    alert(response.error || 'Delete failed');
                }
            },
            error: function() {
                alert('Request failed, please try again');
            }
        });
    }
}

// Accordion functionality
const accordions = document.getElementsByClassName("annAccordion");
for (let i = 0; i < accordions.length; i++) {
    accordions[i].addEventListener("click", function() {
        this.classList.toggle("active");
        const panel = this.nextElementSibling;
        if (panel.style.display === "block") {
            panel.style.display = "none";
        } else {
            panel.style.display = "block";
        }
    });
}


//********************* Announcement  *********************//


function deleteAnnouncement(announcementId) {
    if (confirm('Are you sure you want to delete this announcement?')) {
        $.ajax({
            url: "./function/deleteAnnDB.php",
            method: "POST",
            data: { announcement_id: announcementId },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    manageAnn(); // Refresh announcement list
                } else {
                    alert(response.error || 'Failed to delete announcement');
                }
            },
            error: function() {
                alert('Request failed. Please try again.');
            }
        });
    }
}

// Initialize the accordion effect
function initAccordion() {
    $('.annAccordion').click(function() {
        $(this).toggleClass('active').next().slideToggle(200);
    });
}

// Initialized when page loads
$(document).ready(function() {
    initAccordion();
});



//********************* Edit Profile 管理 *********************//



/// Save information
// command.js
function saveProfile(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('profileForm'));

    $.ajax({
        url: './function/editProfileDB.php',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                alert('Data updated successfully!');
                Profile(); // refresh page
            } else {
                alert('ERROR: ' + (response.error || 'Unknown ERROR'));
            }
        },
        error: function(xhr) {
            try {
                const error = JSON.parse(xhr.responseText);
                alert('ERROR: ' + (error.error || 'Request failed'));
            } catch {
                alert('ERROR: Unable to parse server response');
            }
        }
    });
}

// Change password
function changePassword(e) {
    e.preventDefault();
    const formData = {
        old_password: $('input[name="old_password"]').val(),
        new_password: $('input[name="new_password"]').val(),
        confirm_password: $('input[name="confirm_password"]').val()
    };

    $.ajax({
        url: './function/editProfileDB.php',
        method: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                alert('Password changed successfully');
                $('#passwordForm')[0].reset();
            } else {
                alert(response.error || 'Password change failed');
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });
}

// Function to switch tabs
function switchTab(tabName) {
    if (tabName === 'profile') {
        document.getElementById('profileForm').classList.add('activeForm');
        document.getElementById('passwordForm').classList.remove('activeForm');
    } else if (tabName === 'password') {
        document.getElementById('passwordForm').classList.add('activeForm');
        document.getElementById('profileForm').classList.remove('activeForm');
    }
}

// Function to preview avatar immediately after selection
function previewAvatar(event) {
    const file = event.target.files[0];
    if (!file) return; // Return directly when no file is selected

    // Preview picture
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('avatarPreview');
        output.src = reader.result;
        
        // Construct a FormData object and automatically submit avatar updates
        const formData = new FormData();
        formData.append('avatar', file);
        // In order to ensure that the judgment in editProfileDB.php is true (isset($_POST['admin_name']) || isset($_POST['email'])),
        // You can retrieve other existing information from the form (you can also pass other data as needed)
        formData.append('admin_name', document.querySelector('input[name="admin_name"]').value);
        formData.append('email', document.querySelector('input[name="email"]').value);

        $.ajax({
            url: './function/editProfileDB.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Avatar updated successfully!');
                    // If necessary, refresh the page or other operations can be called here
                } else {
                    alert('Avatar update failed:' + (response.error || 'Unknown ERROR'));
                }
            },
            error: function(xhr) {
                try {
                    const error = JSON.parse(xhr.responseText);
                    alert('Avatar update request error:' + (error.error || 'Unknown ERROR'));
                } catch {
                    alert('Avatar update request failed');
                }
            }
        });
    };
    reader.readAsDataURL(file);
}

//********************* Delete Account *********************//
function deleteAccount() {
    if (confirm('Are you sure you want to permanently delete your account? This action cannot be undone!')) {
        $.ajax({
            url: './function/deleteAccountDB.php',
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    window.location.href = '../../user_login.php'; // Redirect to login page
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                try {
                    const error = JSON.parse(xhr.responseText);
                    alert('Error: ' + (error.error || 'Request failed'));
                } catch {
                    alert('Error: Unable to process request');
                }
            }
        });
    }
}

// Bind delete button using event delegation
$(document).on('click', '#deleteAccountBtn', deleteAccount);



