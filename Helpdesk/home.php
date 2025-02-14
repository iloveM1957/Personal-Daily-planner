<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/c9514bfa2f.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

    <?php
        include "./HelpdeskHeader.php";
        include "./sidebar.php";
    ?>
    
    <main style="display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; text-align: center; margin-top: -50px;">
    
        <!-- Outer Box -->
        <div class="transparent-box">
        <div id="main-content" class="container_main allContent-section py-4" style="display: flex; justify-content: center; align-items: center; width: 100%;">
            <h1 class="title">Home Page----HELPDESK</h1>
                <div class="row" style="display: flex; justify-content: center; align-items: center; width: 100%; gap: 20px;">

                
                    <!-- Question Bank -->
                    <div class="col-sm-3 d-flex justify-content-center">
                        <div class="card text-center dashboard-btn" onclick="QuestionBank()">
                            <i class="fa fa-university" style="font-size: 70px;"></i>
                            <h4 style="color:white;">Question Bank</h4>
                        </div>
                    </div>

                    <!-- User FAQs -->
                    <div class="col-sm-3 d-flex justify-content-center">
                        <div class="card text-center dashboard-btn" onclick="UserFAQs()">
                            <i class="fa fa-question-circle mb-2" style="font-size: 70px;"></i>
                            <h4 style="color:white;">User FAQs</h4>
                        </div>
                    </div>

                    <!-- Profile -->
                    <div class="col-sm-3 d-flex justify-content-center">
                        <div class="card text-center dashboard-btn" onclick="Profile()">
                            <i class="fa fa-user mb-2" style="font-size: 70px;"></i>
                            <h4 style="color:white;">Profile</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
    
    <script type="text/javascript" src="./style/js/command.js"></script>    
    <script type="text/javascript" src="./style/js/sidebar.js"></script>
    <script src="https://code.jquery.com/jquery-3.1.1.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" ></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

</body>
</html>
