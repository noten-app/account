<?php

// Connect all "input-[1-6]" to the same variable
$userinput = "";
for ($i = 1; $i <= 6; $i++) {
    $userinput .= $_POST["input-" . $i];
}

// Check if the user input is the same as the session variable
session_start();
if ($_SESSION["reset_password_token"] != $userinput) {
    exit("Invalid verification code!");
}

?>

<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Reset your Noten-App-Account password.">
    <title>Reset Password | Noten-App</title>
    <link rel="icon" type="image/x-icon" href="https://assets.noten-app.de/images/logo/favicon.ico">
    <link rel="apple-touch-icon" href="https://assets.noten-app.de/images/logo/favicon.ico">
    <link rel="stylesheet" href="/res/css/fonts.css">
    <link rel="stylesheet" href="/res/css/main.css">
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <main>
        <h1><em>New</em> Password</h1>
        <form action="./setpassword.php" method="post">
            <input type="password" name="newpass" id="password-input" placeholder="Password" required>
            <input type="password" name="newpass2" id="password-input2" placeholder="Repeat Password" required>
            <input type="hidden" name="token" value="<?php echo htmlentities($userinput); ?>">
            <input id="submit" type="submit" value="Set New Passsword">
        </form>
    </main>
</body>

</html>