<?php
  include("database.php");
  session_start();
  global $conn;

  $user_id = $_SESSION['user_id'];
  $final_price = 50;

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment Page</title>
  <link rel="stylesheet" href="user_upgrade_premium.css"> 
  <script src="https://kit.fontawesome.com/e3141a5f25.js" crossorigin="anonymous"></script>

  <script>
  function display_message(payment_success_msg) {
    var msg_id = document.getElementById(payment_success_msg);
    msg_id.classList.add("open");
    var overlay = document.getElementById("overlay");
    overlay.classList.add("visible");
  } 

  function close_message(payment_success_msg) {
    var msg_id = document.getElementById(payment_success_msg);
    msg_id.classList.remove("open");
    var overlay = document.getElementById("overlay");
    overlay.classList.remove("visible");
    window.location.href = "mainPage.php";
  }

  document.addEventListener("DOMContentLoaded", function() {
    // Function to detect the selected radio button
    function detectSelectedOption() {
        const credit_card = document.getElementById("credit-card");
        const e_wallet = document.getElementById("e-wallet");
        const e_wallet_div = document.getElementById("ewallet-details");
        const credit_div = document.getElementById("credit-card-details");
        const e_wallet_phone = document.getElementById("ewallet-phone");
        const e_wallet_holder = document.getElementById("ewallet-holder");
        const credit_number = document.getElementById("card-number");
        const credit_holder = document.getElementById("card-holder");
        const credit_date = document.getElementById("expiration-date");
        const credit_cvv = document.getElementById("cvv");

        if (credit_card.checked) {
          e_wallet_div.style.display="none";
          credit_div.style.display="block";
          e_wallet_phone.required = false;
          e_wallet_holder.required = false;

          credit_number.required = true;      
          credit_holder.required = true; 
          credit_date.required = true; 
          credit_cvv.required = true; 

        }
        else{
          credit_div.style.display="none";
          e_wallet_div.style.display="block";   
          credit_number.required = false;      
          credit_holder.required = false; 
          credit_date.required = false; 
          credit_cvv.required = false; 

          e_wallet_phone.required = true;
          e_wallet_holder.required = true;
        } 
    }

    // Add event listeners to the radio buttons
    document.getElementById("credit-card").addEventListener("change", detectSelectedOption);
    document.getElementById("e-wallet").addEventListener("change", detectSelectedOption);

    // Initial call to set the initial state
    detectSelectedOption();
});

  </script>
</head>
<body>
  <?php
    $sql = mysqli_query($conn, "SELECT user_name, user_email FROM user 
                        WHERE user_id = $user_id");
    $user_info = mysqli_fetch_assoc($sql);
  ?>

  <section class="payment-section">
    <div class="payment-container">
    
      <div class="title">
        <h2>Payment</h2>
      </div>

      <div class="secure_payment">
        <hr>
        <h2><span class="icon"><i class="fa-solid fa-lock"></i></span>Secure Payment</h2>
        <hr>
      </div>
    
      <div class="payment-method">
        <h3>Select Payment Method</h3>
        <div class="payment-options">
          <label for="credit-card" class="radio-label-c">
            <input type="radio" id="credit-card" name="payment-method" value="credit-card" checked>
            <div class="outer-border"></div>
            <img src="images/card.jpg" alt="Credit Card">
            <p class="card">Credit / Debit Card</p>
          </label>

          <label for="e-wallet" class="radio-label-e">
            <input type="radio" id="e-wallet" name="payment-method" value="e-wallet">
            <div class="outer-border"></div>
            <img src="images/ewallet.jpg" alt="E-wallet">
            <p class="wallet">E-wallet</p>
          </label>
        </div>
        
        <form action="user_upgrade_premium.php" method="post">
          <div class="credit-card-details" id="credit-card-details">
            <label for="card-number">Card Number<span class="required">*</span></label>
            <input type="text" id="card-number" name="card-number" placeholder="XXXX-XXXX-XXXX-XXXX" 
            pattern="[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{4}" title="Please match the format XXXX-XXXX-XXXX-XXXX"
            autocomplete="off" required>
            
            <label for="card-holder">Name of the Card Holder<span class="required">*</span></label>
            <input type="text" id="card-holder" name="card-holder" autocomplete="off" 
            pattern="^(?!\s*$)[a-zA-Z\s]+$"  title="Please enter a valid name" required>
            
            <div class="expiration-cvv">
              <label for="expiration-date">Expiration Date<span class="required">*</span></label>
              <input type="text" style="width: 50px;" id="expiration-date" name="expiration-date" placeholder="MM/YY" pattern="(0[1-9]|1[0-2])/2[4-9]"
                title="Expiration Date (MM/YY) - Valid from July 2024 to 2029" autocomplete="off" required>
              
              <label for="cvv">CVV<span class="required">*</span></label>
              <input type="text" style="width: 50px;" id="cvv" name="cvv" pattern="[0-9]{3}" title="Please enter a 3-digit CVV number" autocomplete="off" required>
            </div>
          </div>

          <div id="ewallet-details" class="ewallet-details">
            <label>Enter your E-wallet phone number<span class="required">*</span></label>
            <input type="text" class="form-control custom-width" id="ewallet-phone" name="user_contact"
            pattern="^01\d{8,9}$" title="Please enter a valid 10 or 11 digit number starting with '01'" autocomplete="off" required> 
            
            <label for="ewallet-holder">E-wallet Holder Name<span class="required">*</span></label>
            <input type="text" id="ewallet-holder" name="ewallet-holder" autocomplete="off" 
            pattern="^(?!\s*$)[a-zA-Z\s]+$"  title="Please enter a valid name" required>
          </div>
          
          <div class="total-price">
            <h4>TOTAL: RM <span id="total-price"><?php echo number_format($final_price, 2); ?></span></h4> 
          </div>
          <div class="payment-buttons">
            <button type="submit" class="pay"  name="pay">Pay</button>
            <input type="hidden" name="final_price" value="<?php echo $final_price; ?>">
          </div>
        </form>
      </div>

      <div>
      <form action="user_upgrade_premium.php" method="post">
        <button type="submit" name="cancel" class="cancel">Cancel</button>
      </form>
      </div>

    </div>

    <div class="overlay" id="overlay"></div>
    <div class="max_qty_msg" id="payment_success_msg">
      <i class="fa-solid fa-xmark close-icon" onclick="close_message('payment_success_msg')"></i>
      <h2> Congratulations! </h2>
      <p>You have successfully upgraded to <strong>Premium</strong>! 🎖️</p>
  </div>

  </section>

</body>
</html>

<?php 
    if(isset($_POST['cancel'])) {
        echo '<script>window.location.href="mainPage.php";</script>';
    }

    if(isset($_POST["pay"])) {

        $sql_update_user_status = "UPDATE user SET 
                                    user_status = 'premium' 
                                    WHERE user_id = $user_id";  // Removed extra comma before WHERE

        if(mysqli_query($conn, $sql_update_user_status)){
            echo '<script> display_message("payment_success_msg"); </script>';
        } else {
            echo '<script>alert("Error updating user status");</script>';
        }
    }
?>