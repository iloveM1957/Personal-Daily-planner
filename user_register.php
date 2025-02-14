<?php
    include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel = "stylesheet" href = "user_register.css?=v2"> <!--?v=2-->
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

    function display_message(msg_id) {
        var msg_id = document.getElementById(msg_id);
        var overlay = document.getElementById('overlay');
        msg_id.classList.add("open");
        overlay.classList.add("visible");
    }

    function close_message(msg_id) {
        var msg_id_element = document.getElementById(msg_id);
        var overlay = document.getElementById('overlay');
        msg_id_element.classList.remove("open");
        overlay.classList.remove("visible");
        
        if(msg_id == 'successful_msg')
            window.location.href = 'user_login.php';

        else
            window.location.href = 'user_register.php';
    }

    document.addEventListener("DOMContentLoaded", function() 
    {
        var nameInput = document.getElementById("name");
        var myInput = document.getElementById("password");
        var space = document.getElementById("space");
        var letter = document.getElementById("letter");
        var capital = document.getElementById("capital");
        var number = document.getElementById("number");
        var length = document.getElementById("length");

        myInput.onfocus = function() {
            document.getElementById("message").style.display = "block";
            var spaces = /\s/g.test(myInput.value);
            if (!spaces) {
                space.classList.remove("invalid");
                space.classList.add("valid");
            } else {
                space.classList.remove("valid");
                space.classList.add("invalid");
            }
        }

        myInput.onblur = function() {
            document.getElementById("message").style.display = "none";
        }

        myInput.onkeyup = function() 
        {
            var lowerCaseLetters = /[a-z]/g;
            if(myInput.value.match(lowerCaseLetters)) {  
                letter.classList.remove("invalid");
                letter.classList.add("valid");
            } else {
                letter.classList.remove("valid");
                letter.classList.add("invalid");
            }
            
            var upperCaseLetters = /[A-Z]/g;
            if(myInput.value.match(upperCaseLetters)) {  
                capital.classList.remove("invalid");
                capital.classList.add("valid");
            } else {
                capital.classList.remove("valid");
                capital.classList.add("invalid");
            }

            var numbers = /[0-9]/g;
            if(myInput.value.match(numbers)) {  
                number.classList.remove("invalid");
                number.classList.add("valid");
            } else {
                number.classList.remove("valid");
                number.classList.add("invalid");
            }
            
            if(myInput.value.length >= 6) {
                length.classList.remove("invalid");
                length.classList.add("valid");
            } else {
                length.classList.remove("valid");
                length.classList.add("invalid");
            }

            var spaces = /\s/g.test(myInput.value);
            if (!spaces) {
                space.classList.remove("invalid");
                space.classList.add("valid");
            } else {
                space.classList.remove("valid");
                space.classList.add("invalid");
            }     
        }

        nameInput.addEventListener("input", function() {
            var value = nameInput.value;
            
            value = value.replace(/\s/g, "");
            nameInput.value = value;
        });
    });

</script>

</head>
<body>
    <div class="overlay" id="overlay"></div>

    <div class="successful_msg" id="successful_msg">
        <i class="fa-solid fa-xmark" onclick="close_message('successful_msg')"></i>
        <h2>Registration Successful!</h2>
        <img src="images/tick.png"><br>
        <p>You are now a member.<br>Please log in to access your account.</p> 
    </div>

    <div class="email_error_msg" id="email_error_msg">
        <i class="fa-solid fa-xmark" onclick="close_message('email_error_msg')"></i>
        <h2>Invalid Email Format</h2>
        <img src="images/error.png"><br>
        <p>Please use a valid email. Try agian later.</p> 
    </div>

    <div class="pass_error_msg" id="pass_error_msg">
        <i class="fa-solid fa-xmark" onclick="close_message('pass_error_msg')"></i>
        <h2>Password Do Not Match</h2>
        <img src="images/error.png"><br>
        <p>The password you entered does not match. Please try again.</p> 
    </div>

    <div class="pass_error_msg" id="error_msg">
        <i class="fa-solid fa-xmark" onclick="close_message('pass_error_msg')"></i>
        <h2 id="error_h2"></h2>
        <img src="images/error.png"><br>
        <p id="error_p"></p> 
    </div>

    <div class="container">
        <p id="header">Sign Up</p>
        
        <div id="form-top">
            <form method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]) ?>" name="signupform" autocomplete="off">
                
                <div class="input-box">
                    <input type="text" id="name" maxlength="20"  placeholder="Username" name="name" pattern="^[a-zA-Z][a-zA-Z0-9_-]{4,18}[a-zA-Z0-9]$" required><br><br>
                
                    <input type="password" id="password" maxlength="32" placeholder="Password" name="password" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{6,}$" required><br><br>

                    <span onclick="password_visibility('password','eyeIcon_1')">
                        <img id="eyeIcon_1" src="images/close_eye.png" alt="Toggle Password Visibility">
                    </span>

                    <div id="message">
                        <h6>Password must meet the following requirement:</h6>
                        <p id="space" class="invalid"><span>No spaces allowed</span></p>
                        <p id="capital" class="invalid"><span>At least one uppercase letter</span></p>
                        <p id="letter" class="invalid"><span>At least one lowercase letter</span></p>
                        <p id="number" class="invalid"><span>At least one number</span></p>
                        <p id="length" class="invalid"><span>Mininum 6 characters</span></p>
                    </div>

                    <input type="password" id="confirm_password" maxlength="32" placeholder="Confirm Password" name="confirm_password" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{6,}$" required><br><br>
                    <span onclick="password_visibility('confirm_password','eyeIcon_2')">
                        <img id="eyeIcon_2" src="images/close_eye.png" alt="Toggle Password Visibility">
                    </span>

                    <input type="email" id="email" maxlength="32" placeholder="Email" name="email" required>
                </div>
                
                <label style="font-size:14px;">Select User Type</label><br>
                <select id="choice" name="choice">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                    <option value="helpdesk">Helpdesk</option>
                </select><br><br>
                
                <input type="submit" value="Submit" id="register" name="register">
            </form>

        <div id="form-bottom">
            <span>Already have an account?</span>
            <a href="user_login.php" id="login">Login Here</a>
        </div>
           
        </div>

    </div>

</body>
</html>

<?php
 
    global $conn;

    if(isset($_POST["register"]))
    {
        $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, "password", FILTER_SANITIZE_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, "confirm_password", FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);

        $email = strtolower($email);
        $choice = $_POST["choice"];

        if(preg_match('/^[a-zA-Z][a-zA-Z0-9_-]{4,18}[a-zA-Z0-9]$/', $name))
        {
            if($password === $confirm_password)
            {
                function check_existence($conn, $table, $field_type, $field)
                {
                    $sql = "SELECT COUNT(*) AS count FROM $table WHERE BINARY $field_type = '$field'";
                    $result = mysqli_query($conn, $sql);
                    if($result)
                    {
                        $row = mysqli_fetch_assoc($result);

                        if($row["count"] >0){
                            return true;
                        }
                        else{
                            return false;
                        }
                    }
                    else{
                        echo "<script>alert('Something went wrong, plese try agian later.');</script>";
                        echo "<script>window.location.href = 'user_register.php';</script>";
                    }
                }

                function check($conn, $choice, $name, $password, $email)
                {
                    $test = true;

                    if($choice == "user")
                    {
                        $name_type = "user_name";
                        $email_type = "user_email";
                        $pass_type = "user_password";
                    } 
                    else if($choice == "admin")
                    {
                        $name_type = "admin_name";
                        $email_type = "admin_email";
                        $pass_type = "admin_password";

                        if($password != "Admin123") {
                            $test = false;
                        }
                    }
                    else
                    {
                        $name_type = "helpdesk_name";
                        $email_type = "helpdesk_email";
                        $pass_type = "helpdesk_password";
                        if($password != "Helpdesk123") {
                            $test = false;
                        }
                    }

                    if($test)
                    {
                        $hash = password_hash($password, PASSWORD_DEFAULT); 
                        $sql = "INSERT INTO $choice ($name_type, $pass_type, $email_type)
                                VALUES ('$name', '$hash', '$email')";
                        mysqli_query($conn, $sql);
                                            
                        echo "<script>display_message('successful_msg');</script>";
                    }
                    else
                    {
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var msg_h2 = document.getElementById("error_h2");
                                var error_p = document.getElementById("error_p");
                
                                msg_h2.innerHTML = "Wrong Default Password";
                                error_p.innerHTML = "Please use a valid admin default password. Please try again.";
                                display_message("error_msg");
                            });
                        </script>';
                    }                    
                }

                if(substr($email, -4) === '.com'|| strlen($email) < 4)
                {
                    if(!check_existence($conn, 'user', 'user_name', $name) && !check_existence($conn, 'admin', 'admin_name', $name) && !check_existence($conn, 'helpdesk', 'helpdesk_name', $name))
                    {
                        if(!check_existence($conn, 'user', 'user_email', $email) && !check_existence($conn, 'admin', 'admin_name', $email) && !check_existence($conn, 'helpdesk', 'helpdesk_name', $email)){
                            check($conn, $choice, $name, $password, $email);
                        }
                        else{
                            echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    var msg_h2 = document.getElementById("error_h2");
                                    var error_p = document.getElementById("error_p");
                    
                                    msg_h2.innerHTML = "Email Has Been Used";
                                    error_p.innerHTML = "The email you entered is already in use. Please try again.";
                                    display_message("error_msg");
                                });
                            </script>';
                        }
                    }
                    else
                    {
                        echo '<script>
                            document.addEventListener("DOMContentLoaded", function() {
                                var msg_h2 = document.getElementById("error_h2");
                                var error_p = document.getElementById("error_p");
                
                                msg_h2.innerHTML = "Username Has Been Used";
                                error_p.innerHTML = "The username you entered is already in use. Please try again.";
                                display_message("error_msg");
                            });
                        </script>';
                    }
                }
                else{
                    echo "<script>display_message('email_error_msg');</script>";
                }
            }
            else{
                echo "<script>display_message('pass_error_msg');</script>";
            }
        }
        else
        {
            echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        var msg_h2 = document.getElementById("error_h2");
                        var error_p = document.getElementById("error_p");
        
                        msg_h2.innerHTML = "Invalid Username Format";
                        error_p.innerHTML = "Please enter a valid username format. <br>Try agian later.";
                        display_message("error_msg");
                    });
                </script>';
        }
    }

    mysqli_close($conn);

?>