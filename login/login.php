<?php

// Start the PHP_session
require($_SERVER["DOCUMENT_ROOT"] . "/res/php/session.php");
start_session();
// Variables
require($_SERVER["DOCUMENT_ROOT"] . '/config.php');

// Get input
if (!isset($_POST)) exit("No input");
$input = array();
if (!isset($_POST["email"])) exit("E-Mail or Username missing!");
$input["email_or_username"] = strtolower($_POST["email"]);
if (!isset($_POST["password"])) exit("Password missing!");
$input["password"] = $_POST["password"];

// Check input
$input_type = "";
if (str_contains($input["email_or_username"], "@")) $input_type = "email";
else $input_type = "username";

// Conect to database
$con = mysqli_connect($settings["beta_database"]["host"], $settings["beta_database"]["username"], $settings["beta_database"]["password"], $settings["beta_database"]["database"]);
if (mysqli_connect_errno()) exit("Error connecting to our database! Please try again later.");

// Check if account exists
if ($input_type == "email") $stmt = $con->prepare("SELECT id, displayname, password, email, account_version, rounding, sorting, gradesystem, school_year FROM " . $settings["database_tables"]["accounts"] . " WHERE email = ?");
else $stmt = $con->prepare("SELECT id, displayname, password, email, account_version, rounding, sorting, gradesystem, school_year FROM " . $settings["database_tables"]["accounts"] . " WHERE username = ?");
$stmt->bind_param('s', $input["email_or_username"]);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows == 0) exit("Account not found!");
$stmt->bind_result($id, $displayname, $password_hash, $email, $account_version, $setting_rounding, $setting_sorting, $setting_system, $school_year);
$stmt->fetch();

// Add salt and password and check if right
if ($account_version != 4) header("Location: https://onboarding.beta.noten-app.de/");

// Check if password is right
if (!password_verify($input["password"], $password_hash)) exit("Wrong password");

// Set session variables
$_SESSION["login_method"] = "login";
$_SESSION["user_name"] = $displayname;
$_SESSION["user_id"] = $id;
if (isset($input["email"])) $_SESSION["user_email"] = $input["email"];
else $_SESSION["user_email"] = $email;
$_SESSION["setting_rounding"] = $setting_rounding;
$_SESSION["setting_sorting"] = $setting_sorting;
$_SESSION["setting_system"] = $setting_system;
$_SESSION["setting_year"] = $school_year;

if (!isset($_POST["forward"])) {
    header("Location: " . $settings["urls"]["default"]);
    exit();
}

// Check if forward domain starts with "https://"
// If not -> prepend it
// (If domain starts with http prepend it too so domain is invalidated in next step)
if (!str_starts_with($_POST["forward"], "https://")) $_POST["forward"] = "https://" . $_POST["forward"];
// Check if forward domain is allowed (*.noten-app.de)
$forward_domain = parse_url($_POST["forward"], PHP_URL_HOST);
if ($forward_domain == null) exit("Invalid forward domain!");
if (!str_ends_with($forward_domain, $settings["urls"]["allowed_forwarding"])) exit("Invalid forward domain!");
header("Location: " . $_POST["forward"]);
