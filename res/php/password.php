<?php

function password_meets_requirements($password)
{
    $error = false;

    // Check if password is valid
    if (strlen($password) < 8) $error = true;
    if (strlen($password) > 72)  $error = true;
    if (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).*$/", $password))  $error = true;

    return !$error;
}
