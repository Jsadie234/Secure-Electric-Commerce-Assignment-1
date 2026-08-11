<?php
session_start(); //needed to pass error messages back to the form

//Below are the validation checks for the form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { //only accept form submissions, block direct access
    header('Location: register_v2.php');
    exit;
}

function validatePassword(string $password): bool //checks min 10 chars, uppercase, lowercase and number
{
    return strlen($password) >= 10
        && preg_match('/[a-z]/', $password) //must contain lowercase letter
        && preg_match('/[A-Z]/', $password) //must contain uppercase letter
        && preg_match('/\d/', $password); //must contain number
}

$email = trim($_POST['email'] ?? ''); //remove whitespace from email input
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { //validate email format server-side
    $_SESSION['error_v2'] = 'Please enter a valid email address.';
    header('Location: register_v2.php');
    exit;
}

if (!validatePassword($password)) { //reject weak passwords even if browser validation is bypassed
    $_SESSION['error_v2'] = 'Password must be at least 10 characters and include uppercase, lowercase, and a number.';
    header('Location: register_v2.php');
    exit;
}

if ($password !== $confirmPassword) { //make sure both password fields match
    $_SESSION['error_v2'] = 'Passwords do not match.';
    header('Location: register_v2.php');
    exit;
}

// This is copied directly from the Week 3 Tutorial files
$recaptchaSecret = '6LelDHYtAAAAAPszB_6IenX7krSDXOVPp4RDQiBF'; //shouldnt be pushed into the repo but for simplicity I will
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? ''; //token sent back by recaptcha widget

if ($recaptchaResponse === '') { //user must complete recaptcha before submitting
    $_SESSION['error_v2'] = 'Please complete the reCAPTCHA verification.';
    header('Location: register_v2.php');
    exit;
}

$response = file_get_contents(
    'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($recaptchaSecret) //urlencode prevents injection in the request url
    . '&response=' . urlencode($recaptchaResponse)
);
$response = json_decode($response, true);

if (($response['success'] ?? false) === true) {
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Registration Successful</title></head><body><h1>User Registered, thanks for using our shop!</h1></body></html>';
} else {
    echo 'You are a robot';
}
