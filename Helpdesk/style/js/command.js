function UserFAQs(){
    $.ajax({
        url:"./front/UserFAQs.php",
        method:"post",
        data:{record:1},
        success:function(data){
            $('.allContent-section').html(data);
        }
    });
}

function QuestionBank(){
    $.ajax({
        url:"./front/QuestionBank.php",
        method:"post",
        data:{record:1},
        success:function(data){
            $('.allContent-section').html(data);
        }
    });
}

function Profile(){
    $.ajax({
        url:"./front/Profile.php",
        method:"post",
        data:{record:1},
        success:function(data){
            $('.allContent-section').html(data);
        }
    });
}

//************************ FAQ ************************//
// 在command.js中添加
// 在command.js中添加FAQ相关功能调用
function handleFAQ(action, data = {}) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "../function/faqDB.php",
            method: "POST",
            data: { action, ...data },
            success: resolve,
            error: reject
        });
    });
}

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
        // In order to ensure that the judgment in editProfileDB.php is true (isset($_POST['helpdesk_name']) || isset($_POST['email'])),
        // You can retrieve other existing information from the form (you can also pass other data as needed)
        formData.append('helpesk_name', document.querySelector('input[name="helpdesk_name"]').value);
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



