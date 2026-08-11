<?php
session_start(); //needed to pass error messages back to the form

//Below are the validation checks for the form
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { //only accept form submissions, block direct access
    header('Location: register_v3.php');
    exit;
}

function validatePassword(string $password): bool //checks min 10 chars, uppercase, lowercase and number
{
    return strlen($password) >= 10
        && preg_match('/[a-z]/', $password) //must contain lowercase letter
        && preg_match('/[A-Z]/', $password) //must contain uppercase letter
        && preg_match('/\d/', $password); //must contain number
}

$username = trim($_POST['username'] ?? ''); //remove whitespace from username input
$password = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if ($username === '') { //username is required
    $_SESSION['error_v3'] = 'Please enter a username.';
    header('Location: register_v3.php');
    exit;
}

if (!validatePassword($password)) { //reject weak passwords even if browser validation is bypassed
    $_SESSION['error_v3'] = 'Password must be at least 10 characters and include uppercase, lowercase, and a number.';
    header('Location: register_v3.php');
    exit;
}

if ($password !== $confirmPassword) { //make sure both password fields match
    $_SESSION['error_v3'] = 'Passwords do not match.';
    header('Location: register_v3.php');
    exit;
}

// This is copied directly from the Week 3 Tutorial files
$recaptchaSecret = '6LfTC4AtAAAAANifl6PabXL14pH9_rpLR_7cGXlh'; //shouldnt be pushed into the repo but for simplicity I will
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? ''; //token sent back by recaptcha v3 execute()

if ($recaptchaResponse === '') { //user must pass recaptcha v3 before submitting
    $_SESSION['error_v3'] = 'Please complete the reCAPTCHA verification.';
    header('Location: register_v3.php');
    exit;
}

$response = file_get_contents(
    'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($recaptchaSecret) //urlencode prevents injection in the request url
    . '&response=' . urlencode($recaptchaResponse)
);
$response = json_decode($response, true);

if (($response['success'] ?? false) === true && ($response['score'] ?? 0) >= 0.5 && ($response['action'] ?? '') === 'register') { //v3 uses a score instead of a checkbox
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Registration Successful</title></head><body><h1>User Registered, thanks for using our shop!</h1></body></html>';
} else {
    echo 'You are a robot';
}
