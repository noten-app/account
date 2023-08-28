<?php

if (!isset($_POST)) header("Location: /register");
if (!isset($_POST["email"])) header("Location: /register");
if (!isset($_POST["password"])) header("Location: /register");
if (!isset($_POST["password_repeat"])) header("Location: /register");

if (filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) === false) exit("Invalid email address!");

$user_input = array();
$user_input["email"] = $_POST["email"];
$user_input["password"] = $_POST["password"];
$user_input["password_repeat"] = $_POST["password_repeat"];

session_start();
$_SESSION["register_email"] = $user_input["email"];
?>


<!DOCTYPE html>
<html lang="de">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Reset your Noten-App-Account password." />
  <title>Verify Registration | Noten-App</title>
  <link rel="icon" type="image/x-icon" href="https://assets.noten-app.de/images/logo/favicon.ico" />
  <link rel="apple-touch-icon" href="https://assets.noten-app.de/images/logo/favicon.ico" />
  <link rel="stylesheet" href="/res/css/fonts.css" />
  <link rel="stylesheet" href="/res/css/main.css" />
  <link rel="stylesheet" href="./style.css" />
  <link rel="stylesheet" href="./verifymail.css" />
</head>

<body>
  <form action="success.php" method="post">
    <h4>Enter verification code</h4>
    <div id="input-container">
      <input type="tel" name="input-1" maxlength="1" pattern="[0-9]" class="form-control" />
      <input type="tel" name="input-2" maxlength="1" pattern="[0-9]" class="form-control" />
      <input type="tel" name="input-3" maxlength="1" pattern="[0-9]" class="form-control" />
      <input type="tel" name="input-4" maxlength="1" pattern="[0-9]" class="form-control" />
      <input type="tel" name="input-5" maxlength="1" pattern="[0-9]" class="form-control" />
      <input type="tel" name="input-6" maxlength="1" pattern="[0-9]" class="form-control" />
    </div>
    <button type="submit">Finish registration</button>
  </form>
  <script src="verifymail.js"></script>
  <script src="https://assets.noten-app.de/js/jquery/jquery-3.6.1.min.js"></script>
  <script>
    $.ajax({
      url: "verifymail_send.php",
      type: "GET",
      success: function(data) {
        console.log(data);
      }
    });
  </script>
</body>

</html>