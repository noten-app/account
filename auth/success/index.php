<?php

// Variables
require($_SERVER["DOCUMENT_ROOT"] . '/config.php');

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
        <h1>Success!</h1>
        <span id="logintext">Successfully authorized the application.</span>
        <p>You can now close this browser tab.</p>
        <div id="spacer"></div>
        <span id="home" onclick='location.assign("<?= $settings["urls"]["autherror_default"] ?>");'><i class="fas fa-house"></i></span>
    </main>
    <script>
        window.close();
    </script>
</body>

</html>