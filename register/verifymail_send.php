<?php

session_start();

if (!isset($_SESSION["register_email"])) exit("No email address found!");
if (filter_var($_SESSION["register_email"], FILTER_VALIDATE_EMAIL) === false) exit("Invalid email address!");

$email = $_SESSION["register_email"];

// Get PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/Exception.php";
require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/PHPMailer.php";
require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/SMTP.php";

// Get config
require($_SERVER["DOCUMENT_ROOT"] . "/config.php");


// Create 6 digit verification code using Chars 0-9
$verification_token = "";
for ($i = 0; $i < 6; $i++) {
    $verification_token .= substr("0123456789", mt_rand(0, 9), 1);
}

// Create an instance of PHPMailer
$mail = new PHPMailer();

try {
    // Server settings
    $mail->SMTPDebug  = SMTP::DEBUG_SERVER;                         //Enable verbose debug output
    $mail->isSMTP();                                                //Send using SMTP
    $mail->Host       = $settings["mail"]["host"];                  //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                       //Enable SMTP authentication
    $mail->Username   = $settings["mail"]["username"];              //SMTP username
    $mail->Password   = $settings["mail"]["password"];              //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;                //Enable implicit TLS encryption
    $mail->Port       = 465;                                        //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    // Recipients
    $mail->setFrom($settings["mail"]["sender_mail"], $settings["mail"]["sender_name"]);
    $mail->addAddress($email);            //Add a recipient

    // Content
    $mail->isHTML(true);                                            //Set email format to HTML
    $mail->Subject = 'Noten-App | Registration';

    // Content from /mails/password_reset_verification_code.html
    $mail->Body       = str_replace("VERIFICATIONCODE", $verification_token, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/register_verification_code.html"));
    $mail->AltBody    = str_replace("VERIFICATIONCODE", $verification_token, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/register_verification_code.txt"));

    // Disable debugging
    $mail->SMTPDebug = false;

    // Send mail
    $mail->send();

    // Mail sent 
    session_destroy();
    exit("Mail sent!");
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
