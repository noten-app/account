<?php

// Start the PHP_session
require($_SERVER["DOCUMENT_ROOT"] . "/res/php/session.php");
start_session();
// Variables
require($_SERVER["DOCUMENT_ROOT"] . '/config.php');

// CSRF-Protection-Check
if (!isset($_SESSION["csrf_token"]) || !isset($_POST["csrf_token"]) || $_SESSION["csrf_token"] != $_POST["csrf_token"]) die("/auth/error/?error=invalid_request&error_description=CSRF%20token%20invalid");

// Check login
if (
    !isset($_SESSION['login_method']) ||
    !isset($_SESSION['user_name']) ||
    !isset($_SESSION['user_id'])
) die($settings["urls"]["base_url"] . "/login/?forward=" . urlencode($settings["urls"]["base_url"] . $_SERVER["REQUEST_URI"]));

// Conect to database
$con = mysqli_connect($settings["beta_database"]["host"], $settings["beta_database"]["username"], $settings["beta_database"]["password"], $settings["beta_database"]["database"]);
if (mysqli_connect_errno()) exit("/auth/error/?error=internal_error&error_description=Error%20connecting%20to%20our%20database!%20Please%20try%20again%20later.");

// Check if application exists
if ($stmt = $con->prepare('SELECT COUNT(*) FROM ' . $settings["database_tables"]["applications"] . ' WHERE appid = ?')) {
    $stmt->bind_param('s', $_POST["app_id"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
}
if ($count <= 0) die("/auth/error/?error=invalid_request&error_description=Application%20does%20not%20exist");

// Check if user already authorized this application
if ($stmt = $con->prepare('SELECT COUNT(*) FROM ' . $settings["database_tables"]["authorizations"] . ' WHERE application_id = ? AND user_id = ?')) {
    $stmt->bind_param('ss', $_POST["app_id"], $_SESSION["user_id"]);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
}
if ($count > 0) die("/auth/error/?error=invalid_request&error_description=You%20already%20authorized%20this%20application");

// Generate values
$auth_code = bin2hex(random_bytes(64));
$access_token = bin2hex(random_bytes(64));
$refresh_token = bin2hex(random_bytes(64));
$token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

// Insert authorization into database
if ($stmt = $con->prepare('INSERT INTO ' . $settings["database_tables"]["authorizations"] . ' (application_id, user_id, auth_type, auth_code, access_token, refresh_token, token_expiry) VALUES (?, ?, 1, ?, ?, ?, ?)')) {
    $stmt->bind_param('ssssss', $_POST["app_id"], $_SESSION["user_id"], $auth_code, $access_token, $refresh_token, $token_expiry);
    $stmt->execute();
    $stmt->close();
    exit("/auth/success/");
}
