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

// Start session
session_start();

// Get Email
$email = $_POST["mail"];

// Conect to database
$con = mysqli_connect($settings["beta_database"]["host"], $settings["beta_database"]["username"], $settings["beta_database"]["password"], $settings["beta_database"]["database"]);
if (mysqli_connect_errno()) exit("Error connecting to our database! Please try again later.");

// Check if account exists
$stmt = $con->prepare("SELECT COUNT(*) FROM " . $settings["database_tables"]["accounts"] . " WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows != 1) exit("Account not found! | Could also be that there are multiple accounts with the same email address!");
$stmt->bind_result($id, $displayname);
$stmt->fetch();
$stmt->close();

// Create 6 digit verification code using Chars 0-9
$verification_token = "";
for ($i = 0; $i < 6; $i++) {
    $verification_token .= substr("0123456789", mt_rand(0, 9), 1);
}

$_SESSION["reset_password_token"] = $verification_token;

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
    $mail->addAddress($email, $displayname);                        //Add a recipient

    // Content
    $mail->isHTML(true);                                            //Set email format to HTML
    $mail->Subject = 'Noten-App | Password Reset';

    // Content from /mails/apply.html
    $mail->Body       = str_replace("RESETCODE", $verification_token, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/verification_code.html"));
    $mail->AltBody    = str_replace("RESETCODE", $verification_token, file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/mails/verification_code.txt"));

    // Disable debugging
    $mail->SMTPDebug = false;

    // Send mail
    $mail->send();

    // Redirect to login
    header("Location: confirm.html");
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
