<?php 
    session_start(); 
    include("database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="password_change.css?=v2">
<script>

document.addEventListener("DOMContentLoaded", function() 
{

    var myInput = document.getElementById("pw");
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

});

</script>
</head>
<body>
    
        <div id="container">
            <h2>Reset Password</h2>
        
            <?php
            if(isset($_SESSION['status']))
            {
                echo '<h4 style="margin-bottom: 30px;">' . $_SESSION["status"] . '</h4>';
            }
        ?>
            
            <br>
            <form method="post" action="password_change.php" name="signinform">  
                <input type="hidden" name="password_token" value="<?php if(isset($_GET["token"])) { echo $_GET['token']; } ?>">

                <div class="input-box">
                    <input type="password" id="pw" maxlength="32" placeholder="Enter New Password" name="NewPassword" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{6,}$" required><br><br>
                </div>

                <div id="message">
                    <h5>Password must meet the following requirement:</h5>
                    <p id="space" class="invalid"><span>No spaces allowed</span></p>
                    <p id="capital" class="invalid"><span>At least one uppercase letter</span></p>
                    <p id="letter" class="invalid"><span>At least one lowercase letter</span></p>
                    <p id="number" class="invalid"><span>At least one number</span></p>
                    <p id="length" class="invalid"><span>Mininum 6 characters</span></p>
                </div>

                <div class="input-box">
                <input type="password" id="pwConfirm" maxlength="32" placeholder="Enter Confirm Password" name="ConfirmPassword" pattern="^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])\S{6,}$" required><br><br>
                </div>
                
                <input type="submit" value="submit" id="submit" name="password_update"><br>
   
            </form>
        
        </div>

</body>
</html>
<?php
    global $conn;

    if(isset($_POST["password_update"]))
    {
        $NewPassword = mysqli_real_escape_string($conn, $_POST["NewPassword"]);
        $ConfirmPassword = mysqli_real_escape_string($conn, $_POST["ConfirmPassword"]);
        $password_token = mysqli_real_escape_string($conn, $_POST["password_token"]);

        if(!empty($password_token))
        {
            if(!empty($NewPassword) &&!empty($ConfirmPassword))
            {
                $check_token ="SELECT user_verify_token FROM user WHERE user_verify_token ='$password_token' LIMIT 1";
                $check_token_run = mysqli_query($conn, $check_token);
                if(mysqli_num_rows($check_token_run) > 0){
                    $reset_choice = "user";
                }
                else
                {
                    $check_token ="SELECT admin_verify_token FROM admin WHERE admin_verify_token ='$password_token' LIMIT 1";
                    $check_token_run = mysqli_query($conn, $check_token);
                    if(mysqli_num_rows($check_token_run) > 0){
                        $reset_choice = "admin";
                    }
                    else{
                        $check_token ="SELECT helpdesk_verify_token FROM helpdesk WHERE helpdesk_verify_token ='$password_token' LIMIT 1";
                        $check_token_run = mysqli_query($conn, $check_token);
                        if(mysqli_num_rows($check_token_run) > 0){
                            $reset_choice = "helpdesk";
                        }
                        else{
                            echo '<script>
                                    alert("invalid token!");
                                    window.location.href ="user_login.php";    
                                </script>';        
                        
                            exit(0);
                        }
                    }
                }

                if($NewPassword == $ConfirmPassword)
                {
                    $new_token = md5(rand());
                    $hash = password_hash($NewPassword, PASSWORD_DEFAULT); 
                    if($reset_choice == "user"){
                        $update_query = "UPDATE user SET user_password = '$hash', user_verify_token = '$new_token' WHERE user_verify_token = '$password_token' LIMIT 1";
                    }
                    else if($reset_choice == "admin"){
                        $update_query = "UPDATE admin SET admin_password = '$hash', admin_verify_token = '$new_token' WHERE admin_verify_token = '$password_token' LIMIT 1";
                    }
                    else{
                        $update_query = "UPDATE helpdesk SET helpdesk_password = '$hash', helpdesk_verify_token = '$new_token' WHERE helpdesk_verify_token = '$password_token' LIMIT 1";
                       
                    }

                    $update_db = mysqli_query($conn, $update_query);

                    if($update_db)
                    {
                        $_SESSION["status"] = "Only can update once per request!";   
                        echo '<script>
                            alert("New password successfully updated!");
                            window.location.href = "user_login.php";
                        </script>';
                    }
                    else
                    {
                        echo '<script>
                            alert("Did not update password,something went wrong");                               
                            window.location.href = "user_login.php";                                
                        </script>';
                    }
                }
                else
                {
                    $_SESSION["status"] = "Password and Confirm Password does not match!";     
                    echo '<script>window.location.href = "password_change.php?token=' . $password_token . '&reset_choice=' . $reset_choice . '";</script>';

                }
            }
        }
        else
        {
            $_SESSION["status"] = "No token available!";

            echo '<script>
                 alert("No token available");
                window.location.href ="user_login.php"
            </script>';
        }

    }

?>