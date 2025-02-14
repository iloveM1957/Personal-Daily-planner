<?php
    include("database.php");
    session_start();
    global $conn;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel = "stylesheet" href = "user_login.css"> <!--?v=2-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/cd7067c6ef.js" crossorigin="anonymous"></script>

<script>

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

    function show_wrong_input() { // Renamed function
        var error_div = document.getElementById('error');
        if (error_div.style.visibility === 'visible') {
            error_div.style.visibility = 'hidden'; 
        } 
        else {
            error_div.style.visibility = 'visible'; 
        }
    }

    function display_message(msg_id) {
        var msgElement  = document.getElementById(msg_id);
        msgElement .classList.add("open");
        document.getElementById("email").value = "";
    }

    function close_message(msg_id) {
        var msgElement  = document.getElementById(msg_id);
        var overlay = document.getElementById("overlay");
        msgElement .classList.remove("open");
        //window.location.href = "user_login.php";
        overlay.classList.remove("visible");

    }

</script>


</head>
<body>
<div class="overlay" id="overlay"></div>
    <div class="error_msg" id="error_msg">
        <i class="fa-solid fa-xmark" onclick="close_message('error_msg')"></i>
        <h2>Access Denied!</h2>
        <img src="images/error.png"><br>
        <p>Your account has been blocked.<br>Please contact customer support for further assistance.</p> 
    </div>

    <div class="forgot_password" id="forgot_password">
        <i class="fa-solid fa-xmark" onclick="close_message('forgot_password')"></i>
        <h2>Forget Password</h2>
        <form action="user_login.php" method="post">   
            <label for="email">Email: </label>
            <input type="email" name="email" id="email" required autocomplete="off"><br><br>

            <select name="reset_choice" id="reset_choice">
                <option value="user" selected>User</option>
                <option value="admin">Admin</option>
                <option value="helpdesk">Helpdesk</option>
            </select><br><br><br>

            <button type="submit" name="password_reset_link" id="password_reset_link">Send</button>       
        </form>
    </div>

    <div class="container">
        <p id="header">Personal Daily Planner</p>
        <div id="form-top">
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" name="signinform" autocomplete="off"> 

                <div id="error">
                    <p id="error-message"><span style="font-weight: bold;">Wrong credentials</span> <br>username or password</p>
                </div>

                <div class="input-box">
                    <input type="text" id="name" maxlength="30"  placeholder="Username" name="name" required>
                </div>

                <div class="input-box">
                    <input type="password" id="password" maxlength="30" placeholder="Password" name="password" required>
                    
                    <span onclick="password_visibility('password','eyeIcon')">
                        <img id="eyeIcon" src="images/close_eye.png" alt="Toggle Password Visibility">
                    </span>
                </div>
                
                <label style="font-size:14px;">Select User Type</label><br>
                <select id="choice" name="choice">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                    <option value="helpdesk">Helpdesk</option>
                </select>
                
                <input type="submit" value="Login" id="login" name="login">
                
                <div id="forgot_link">
                    <p id="forgot">Forgot Password?</p>

                    <script>
                        var forgot = document.getElementById("forgot");
                        var overlay = document.getElementById("overlay");
                        forgot.addEventListener("click", function() {
                            display_message('forgot_password');                      
                            overlay.classList.add("visible");
                        });
                    </script>
                    
                </div>
            </form>
        </div>

        <hr>
        <div id="form-bottom">
            <div id="register-button">
                <button type="button" id="register">Create New Account</button>
            </div>
        </div>
    </div>

<script>
        document.getElementById("register").addEventListener("click", function() {
        window.location.href = "user_register.php"; 
    });
</script>

</body>
</html>


<?php
function authenticateUser($username, $password, $table, $user, $passwordColumn, $user_id, $redirect_page)
{
    global $conn;

    $sql = "SELECT * FROM $table WHERE BINARY $user = '$username'";  
    $result = mysqli_query($conn, $sql);

    if(mysqli_num_rows($result) > 0)
    {
        $row = mysqli_fetch_assoc($result);
        if(password_verify($password, $row[$passwordColumn]) && $row[$user] == $username)
        {
            $_SESSION[$user_id] = $row[$user_id];
            $_SESSION[$user] = $username;
            echo '<script>window.location.href = "'.$redirect_page.'";</script>';
            exit();
        }
        else{
            echo '<script> show_wrong_input(); </script>';
        }
    }
    else{
        echo '<script> show_wrong_input(); </script>'; 
    }
}

if(isset($_POST["login"]))
{
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
    $choice = $_POST["choice"];

    if($choice == "user")
    {
        $sql = "SELECT user_block_status FROM user WHERE user_name = '$name'";
        $result = mysqli_query($conn, $sql);

        if(mysqli_num_rows($result) > 0)
        {
            if($row = mysqli_fetch_assoc($result))
            {
                if($row["user_block_status"] == "block"){
                    echo "<script>display_message('error_msg');</script>"; 
                }
                else{
                    $today = new DateTime();
                    $today_formatted = $today->format('Y-m-d');
                    authenticateUser($name, $password, "user", "user_name", "user_password", "user_id", "mainPage.php?week_offset=0&selected_date=$today_formatted");
                }
            }
        }
        else{
            echo '<script> show_wrong_input(); </script>'; // Updated function call
        }
    }
    else if($choice == "admin"){
        authenticateUser($name, $password, "admin", "admin_name", "admin_password", "admin_id", "Admin/index.php");
    }

    else{
        authenticateUser($name, $password, "helpdesk", "helpdesk_name", "helpdesk_password", "helpdesk_id", "Helpdesk/home.php");
    }
}



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

    //Load Composer's autoloader
    require 'vendor/autoload.php';

    function send_password_reset($get_name, $get_email, $token, $reset_choice)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();     
            $mail->SMTPAuth   = true;   

            $mail->Host       = 'smtp.gmail.com';                                                  
            $mail->Username   = 'keanping528@gmail.com';                     
            $mail->Password   = 'cwbg eiti icly fjyf';   
                                        
            $mail->SMTPSecure = "tls";            
            $mail->Port       = 587;                                    

            //Recipients
            $mail->setFrom('keanping528@gmail.com', 'Personal Daily Planner Admin');
            $mail->addAddress($get_email);               
            
            $mail->isHTML(true);
            $mail->Subject = "Reset Password Notification";

            // Dynamically generate the base URL
            $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);

            $email_template = "
            <h2>Hello</h2>
            <h3>You are receiving this email because we received a password reset request for your account.</h3>
            <br/><br/>
            <a href='$base_url/password_change.php?token=$token&reset_choice=$reset_choice'>Reset Password Link</a>
            ";

            $mail->Body = $email_template;
            $mail->send();
            echo "<script>window.location.href = 'user_login.php';</script>";
        } catch (Exception $e) {
            echo '<script>
                alert("Message could not be sent. Mailer Error: {$mail->ErrorInfo}"); 
                window.location.href = "user_login.php";
            </script>';
        }
    }

    if(isset($_POST["password_reset_link"]))
    {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);
        $reset_choice = mysqli_real_escape_string($conn, $_POST["reset_choice"]);
        $token = md5(rand());

        if($reset_choice == "user"){
            $check_email = "SELECT * FROM user WHERE user_email = '$email' LIMIT 1";
        }
        else if($reset_choice == "admin"){   
            $check_email = "SELECT * FROM admin WHERE admin_email = '$email' LIMIT 1";
        }
        else{
            $check_email = "SELECT * FROM helpdesk WHERE helpdesk_email = '$email' LIMIT 1";
        }
        
        $check_email_run = mysqli_query($conn, $check_email);
    
        if(mysqli_num_rows($check_email_run) > 0)
        {
            $row = mysqli_fetch_array($check_email_run);
            if($reset_choice == "user"){
                $get_name = $row['user_name'];
                $get_email = $row['user_email'];
                $update_token = "UPDATE user SET user_verify_token = '$token' WHERE user_email ='$get_email' LIMIT 1";
            }
            else if($reset_choice == "admin"){
                $get_name = $row['admin_name'];
                $get_email = $row['admin_email'];
                $update_token = "UPDATE admin SET admin_verify_token = '$token' WHERE admin_email ='$get_email' LIMIT 1";
            }
            else{
                $get_name = $row['helpdesk_name'];
                $get_email = $row['helpdesk_email'];
                $update_token = "UPDATE helpdesk SET helpdesk_verify_token = '$token' WHERE helpdesk_email ='$get_email' LIMIT 1"; // Corrected table name
            }
            
            $update_token_run = mysqli_query($conn, $update_token);

            if($update_token_run){
                send_password_reset($get_name, $get_email, $token, $reset_choice);
                $_SESSION["status"] = "We e-mailed you a password reset link";
                exit(0);
            }
            else{
                echo '<script>
                    alert("Something went wrong."); 
                    window.location.href = "user_login.php";
                </script>';
            }
        }
        else{
            echo '<script>
                alert("No Email Found! Please enter a valid email."); 
                window.location.href = "user_login.php";
            </script>';
        }
        unset($_POST["email"]);
    }   
?>