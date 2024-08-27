<?php
include "database_connection.h"; 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

function generateVerificationCode($length = 6) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

$subject = "Password Manager Verification Code";
$email = $_SESSION['email'];

$new_verification_code = generateVerificationCode();

$update_table_by_resend = "UPDATE TempUsersUnderVerification SET verification_code = $1 WHERE email_user = $2";
$result_update_by_resend = pg_query_params($conn,$update_table_by_resend, array($new_verification_code,$email));
        

        
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


header('Location: ../email-verification.php');

?>