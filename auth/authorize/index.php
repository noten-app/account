<?php

// Start the PHP_session
require($_SERVER["DOCUMENT_ROOT"] . "/res/php/session.php");
start_session();
// Variables
require($_SERVER["DOCUMENT_ROOT"] . '/config.php');

// Check input
if (!isset($_GET["response_type"]) || !isset($_GET["client_id"]) || !isset($_GET["redirect_uri"]) || !isset($_GET["state"]))
    header("Location: /auth/error/?error=invalid_request&error_description=Missing%20parameters");

// Check login
if (
    !isset($_SESSION['login_method']) ||
    !isset($_SESSION['user_name']) ||
    !isset($_SESSION['user_id'])
) header("Location: " . $settings["urls"]["base_url"] . "/login/?forward=" . urlencode($settings["urls"]["base_url"] . $_SERVER["REQUEST_URI"]));

// Conect to database
$con = mysqli_connect($settings["beta_database"]["host"], $settings["beta_database"]["username"], $settings["beta_database"]["password"], $settings["beta_database"]["database"]);
if (mysqli_connect_errno()) exit("Error connecting to our database! Please try again later.");

// Check if application exists
if ($stmt = $con->prepare('SELECT appname, appurl, owner, usercount, creation FROM ' . $settings["database_tables"]["applications"] . ' WHERE appid = ?')) {
    $stmt->bind_param('s', $_GET["client_id"]);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows <= 0) header("Location: /auth/error/?error=invalid_request&error_description=Application%20does%20not%20exist");
    $stmt->bind_result($appname, $appurl, $owner_id, $usercount, $creation);
    $stmt->fetch();
    $stmt->close();
}
if ($stmt = $con->prepare('SELECT displayname FROM ' . $settings["database_tables"]["accounts"] . ' WHERE id = ?')) {
    $stmt->bind_param('s', $owner_id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows <= 0) header("Location: /auth/error/?error=invalid_request&error_description=Application%20does%20not%20exist");
    $stmt->bind_result($owner);
    $stmt->fetch();
    $stmt->close();
}

// Generate CSRF-Protection Token
$csrf_token = bin2hex(random_bytes(32));
$_SESSION["csrf_token"] = $csrf_token;

?>
<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Register a new Noten-App-Account." />
    <title>Authorize | Noten-App</title>
    <link rel="stylesheet" href="/res/css/fonts.css" />
    <link rel="stylesheet" href="/res/css/main.css" />
    <link rel="stylesheet" href="./style.css" />
    <link rel="stylesheet" href="/res/fontawesome/css/fontawesome.min.css">
    <link rel="stylesheet" href="/res/fontawesome/css/solid.min.css">
    <link rel="apple-touch-icon" sizes="180x180" href="https://assets.noten-app.de/images/logo/apple-touch-icon.png" />
    <link rel="icon" type="image/png" sizes="32x32" href="https://assets.noten-app.de/images/logo/favicon-32x32.png" />
    <link rel="icon" type="image/png" sizes="16x16" href="https://assets.noten-app.de/images/logo/favicon-16x16.png" />
    <link rel="mask-icon" href="https://assets.noten-app.de/images/logo/safari-pinned-tab.svg" color="#eb660e" />
    <link rel="shortcut icon" href="https://assets.noten-app.de/images/logo/favicon.ico" />
</head>

<body>
    <main id="main">
        <h1>Authorize</h1>
        <span id="logintext">Authorize an Application</span>
        <p>
            The Application <span class="accent"><?= htmlspecialchars($appname) ?></span> wants to access your Noten-App Data.
        </p>
        <p>
            It <b class="accent">will</b> get access to your homework, your grades, your subjects and your username.
        </p>
        <p>
            It <b class="accent">will not</b> get access to your E-Mail, Password or other personal data.
        </p>
        <div class="info-container">
            <div class="app_creator info-data">
                <span class="creator-name"><?= htmlspecialchars($owner) ?></span>
            </div>
            <div class="app_creator_icon info-icon">
                <i class="fa-solid fa-code"></i>
            </div>
            <div class="app_users info-data">
                <?= $usercount ?>
            </div>
            <div class="app_users_icon info-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <div class="app_creation info-data">
                <?= $creation ?>
            </div>
            <div class="app_creation_icon info-icon">
                <i class="fa-solid fa-calendar"></i>
            </div>
        </div>
        <div id="spacer"></div>
        <span id="reject" onclick="access.reject();">Reject Access</span>
        <span id="grant" onclick="access.grant();">Grant Access</span>
    </main>
    <script src="/res/js/jquery.min.js"></script>
    <script>
        const access = {
            grant: () => {
                $.ajax({
                    url: 'grant.php',
                    type: 'POST',
                    data: {
                        csrf_token: "<?= $csrf_token ?>",
                        app_id: "<?= $_GET["client_id"] ?>",
                    },
                    success: url => location.assign(url)
                });
            },
            deny: () => {
                return false;
            }
        }
    </script>
</body>

</html>