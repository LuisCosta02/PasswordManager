<?php
include "php\database_connection.h"; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';



// Function to generate a random security key
function generateVerificationCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateSecurityKey($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

$subject = "Password Manager Verification Code";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $_SESSION['email'] =  $_POST["email"];
    $email = $_SESSION['email'];
    
    $_SESSION['name'] = $_POST["name"];
    $name = $_SESSION['name'];
    
    $_SESSION['password'] = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $password = $_SESSION['password'];
    
    $_SESSION['terms'] = $_POST["terms"];
    $terms = $_SESSION['terms'];

    $_SESSION['security_key'] = generateSecurityKey();
    $security_key = $_SESSION['security_key'];

    $_SESSION['security_key_encrypted'] = password_hash($security_key, PASSWORD_DEFAULT);
    $security_key_encrypted = $_SESSION['security_key_encrypted'];
    

    $_SESSION['verification_code'] = generateVerificationCode();
    $verification_code = $_SESSION['verification_code'];

    $_SESSION['verification_code_encrypted'] = password_hash($verification_code, PASSWORD_DEFAULT);
    $verification_code_encrypted = $_SESSION['verification_code_encrypted'];

    $checkDuplicatedTempEmail = "SELECT 1 FROM TempUsersUnderVerification WHERE email_user = $1";
    $resultDuplicateTempEmail = pg_query_params($conn, $checkDuplicatedTempEmail, array($email));

    }

    // Check for errors before proceeding
    if (empty($email) || empty($name) || empty($password) || empty($terms)) {
        $errorMSG = "All fields are required.";
    } else {
        // Check if there is already a user with the email the user just typed
        $checkQuery = "SELECT 1 FROM usersTable WHERE email_user = $1";
        $checkResult = pg_query_params($conn, $checkQuery, array($email));

        if (pg_num_rows($checkResult) > 0) {
            echo " <script> alert ('A user with this email already exists!!') </script>";
            echo "<script>  setTimeout(function () { window.location.href = 'sign-up.html'; }, 0000)</script>";
        } else {
            
            if(pg_num_rows($resultDuplicateTempEmail) > 0){
                $new_verification_code = generateVerificationCode();
                $delete_duplicated_email = "DELETE FROM TempUsersUnderVerification WHERE email_user = $1";
                $result_duplicated_email = pg_query_params($conn,$delete_duplicated_email, array($email));
        
                $query = "INSERT INTO TempUsersUnderVerification (email_user, verification_code) VALUES ($1, $2)";
                $result = pg_query_params($conn, $query, array($email, $new_verification_code));
        
                $mail = new PHPMailer(true);
        
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'luisfgcosta222@gmail.com';
                $mail->Password = 'wicl kntn scti yzme';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;  
        
                $mail->setFrom('luisfgcosta222@gmail.com');
        
                
        
                $mail->addAddress($email);
        
                $mail->isHTML(true);
        
                $mail-> Subject = $subject;
                
                $mail->Body = $new_verification_code;
        
                $mail->send();
            }else{
                $query = "INSERT INTO TempUsersUnderVerification (email_user, verification_code) VALUES ($1, $2)";
                $result = pg_query_params($conn, $query, array($email, $verification_code));

                $mail = new PHPMailer(true);
        
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'luisfgcosta222@gmail.com';
                $mail->Password = 'wicl kntn scti yzme';
                $mail->SMTPSecure = 'ssl';
                $mail->Port = 465;  
        
                $mail->setFrom('luisfgcosta222@gmail.com');
        
                       
                $mail->addAddress($email);
        
                $mail->isHTML(true);
        
                $mail-> Subject = $subject;
                
                $mail->Body = $verification_code;
        
                $mail->send();
            }


        }
    }

    



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="Tivo is a HTML landing page template built with Bootstrap to help you create engaging presentations for SaaS apps and convert visitors into users.">
    <meta name="author" content="Inovatik">

    <!-- OG Meta Tags to improve the way the post looks when you share the page on LinkedIn, Facebook, Google+ -->
    <meta property="og:site_name" content="" /> <!-- website name -->
    <meta property="og:site" content="" /> <!-- website link -->
    <meta property="og:title" content=""/> <!-- title shown in the actual shared post -->
    <meta property="og:description" content="" /> <!-- description shown in the actual shared post -->
    <meta property="og:image" content="" /> <!-- image link, make sure it's jpg -->
    <meta property="og:url" content="" /> <!-- where do you want your post to link to -->
    <meta property="og:type" content="article" />

    <!-- Website Title -->
    <title>Tivo - Verify your Email</title>
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,400i,700&display=swap&subset=latin-ext" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/fontawesome-all.css" rel="stylesheet">
    <link href="css/swiper.css" rel="stylesheet">
    <link href="css/magnific-popup.css" rel="stylesheet">
    <link href="css/styles.css" rel="stylesheet">
    
    <!-- Favicon  -->
    <link rel="icon" href="images/favicon.png">
</head>
<body data-spy="scroll" data-target=".fixed-top">
    
    <!-- Preloader -->
    <div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <!-- end of preloader -->
    

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <div class="container">

            <!-- Text Logo - Use this if you don't have a graphic logo -->
            <!-- <a class="navbar-brand logo-text page-scroll" href="index.html">Tivo</a> -->

            <!-- Image Logo -->
            <a class="navbar-brand logo-image" href="index.html"><img src="images/logo.svg" alt="alternative"></a> 
            
            
        </div> <!-- end of container -->
    </nav> <!-- end of navbar -->
    <!-- end of navigation -->


    <!-- Header -->
    <header id="header" class="ex-2-header">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Email Verification</h1>
                   <p>We just send an Email with verification code, please confirm. Didn't receive any email? <a class="white" href="php/resend-email-verification.php">Resend Email</a></p> 
                    <!-- Sign Up Form -->
                    <div class="form-container">
                        <form data-toggle="validator" action="php/signupform-process.php" method="POST" data-focus="false">
                            <div class="form-group">
                                <input type="" class="form-control-input" id="code" name="code" required>
                                <label class="label-control" for="code">Code</label>
                                <div class="help-block with-errors"></div>
                            </div>
                            
                            
                            
                            
                            <div class="form-group">
                                <button type="submit" class="form-control-submit-button">SUBMIT</button>
                            </div>
                            <div class="form-message">
                                <div id="smsgSubmit" class="h3 text-center hidden"></div>
                            </div>
                        </form>
                    </div> <!-- end of form container -->
                    <!-- end of sign up form -->

                </div> <!-- end of col -->
            </div> <!-- end of row -->
        </div> <!-- end of container -->
    </header> <!-- end of ex-header -->
    <!-- end of header -->


    <!-- Scripts -->
    <script src="js/jquery.min.js"></script> <!-- jQuery for Bootstrap's JavaScript plugins -->
    <script src="js/popper.min.js"></script> <!-- Popper tooltip library for Bootstrap -->
    <script src="js/bootstrap.min.js"></script> <!-- Bootstrap framework -->
    <script src="js/jquery.easing.min.js"></script> <!-- jQuery Easing for smooth scrolling between anchors -->
    <script src="js/swiper.min.js"></script> <!-- Swiper for image and text sliders -->
    <script src="js/jquery.magnific-popup.js"></script> <!-- Magnific Popup for lightboxes -->
    <script src="js/validator.min.js"></script> <!-- Validator.js - Bootstrap plugin that validates forms -->
    <script src="js/scripts.js"></script> <!-- Custom scripts -->


    
</body>
</html>
