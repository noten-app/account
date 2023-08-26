<?php

// Load PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/Exception.php";
require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/PHPMailer.php";
require $_SERVER["DOCUMENT_ROOT"] . "/res/php/PHPMailer-master/src/SMTP.php";

// Get config
require($_SERVER["DOCUMENT_ROOT"] . "/config.php");

// Get password requirement checker
require($_SERVER["DOCUMENT_ROOT"] . "/res/php/password.php");

// Start session
session_start();

// Input
$newpass = $_POST["newpass"];
$newpass2 = $_POST["newpass2"];
$token = $_POST["token"];

// Check if passwords match
if ($newpass != $newpass2) exit("Passwords do not match!");

// Check if token matches session variable
if ($_SESSION["reset_password_token"] != $token) exit("Invalid verification code!");

// Check if password meets requirements
if (!password_meets_requirements($newpass)) exit("Password does not meet requirements!");

// Conect to database
$con = mysqli_connect($settings["beta_database"]["host"], $settings["beta_database"]["username"], $settings["beta_database"]["password"], $settings["beta_database"]["database"]);
if (mysqli_connect_errno()) exit("Error connecting to our database! Please try again later.");

// Hash password
$hashed_password = password_hash($newpass, PASSWORD_DEFAULT);

// Update password
$stmt = $con->prepare("UPDATE " . $settings["database_tables"]["accounts"] . " SET password = ? WHERE email = ?");
$stmt->bind_param('ss', $hashed_password, $_SESSION["reset_password_email"]);
$stmt->execute();
$stmt->close();

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
    $mail->addAddress($_SESSION["reset_password_email"], $displayname);                        //Add a recipient

    // Content
    $mail->isHTML(true);                                            //Set email format to HTML
    $mail->Subject = 'Noten-App | Password Reset';

    // Content from /mails/password_changed.html
    $mail->Body       = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/password_changed.html");
    $mail->AltBody    = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/password_changed.txt");

    // Disable debugging
    $mail->SMTPDebug = false;

    // Send mail
    $mail->send();

    // Destroy session
    session_destroy();

    // Redirect to login
    header("Location: /");
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
