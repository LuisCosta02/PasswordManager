<?php
include "database_connection.h"; // Use .php extension for the included file
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

session_start();

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

$errorMSG = "";

// Function to generate a random security key
function generateSecurityKey($length = 10) {
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}

$security_key = $_SESSION['security_key'];
$terms = $_SESSION['terms'];
$password = $_SESSION['password'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$verification_code = $_SESSION['verification_code'];
$verification_code_introduced = $_POST['code'];

$subject = "Password Manager Security Key";

$check_verf_code = "SELECT FROM TempUsersUnderVerification WHERE email_user = $1 AND verification_code = $2";
$result_verif_code = pg_query_params($conn, $check_verf_code, array($email,$verification_code_introduced));

if(pg_num_rows($result_verif_code)>0){
    // Prepare the SQL query
    $query = "INSERT INTO usersTable (nome_user, email_user, security_key, password_user) VALUES ($1, $2, $3, $4)";
    $result = pg_query_params($conn, $query, array($name, $email, $security_key, $password));

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
    
    $mail->Body = $security_key;

    $mail->send();

    if ($result) {
        $delete_query_fromTemp = "DELETE FROM TempUsersUnderVerification WHERE email_user = $1";
        $result_delete_query_fromTemp = pg_query_params($conn,$delete_query_fromTemp,array($email));
        
        echo "<script>  setTimeout(function () { window.location.href = '../success-registration.html'; }, 0000)</script>";
        
        exit();
    } else {
        echo "Error: " . pg_last_error($conn);
    }


}else{
    //Erro de Validacao do verification code
    echo '
    <div style="
        width: 100%;
        max-width: 400px;
        margin: 20px auto;
        padding: 15px;
        border: 1px solid #f44336;
        border-radius: 5px;
        background-color: #f8d7da;
        color: #721c24;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-family: Arial, sans-serif;
    ">
        <strong>Error:</strong> Invalid verification code. Please try again.
        <br><br>
        <button onclick="goBack()" style="
            margin-top: 10px;
            padding: 8px 16px;
            background-color: #f44336;
            color: #ffffff;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 16px;
        ">Go Back</button>
    </div>
    <script>
        function goBack() {
            window.location.href = "../email-verification.php"; // Replace with your specific page
        }
    </script>';

}

?>
