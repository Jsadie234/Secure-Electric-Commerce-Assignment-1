<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

// PHPMailer docs:
// https://github.com/PHPMailer/PHPMailer/wiki/Tutorial
// https://phpmailer.github.io/PHPMailer/

// Gmail SMTP settings
$smtpUser = 'jmsad234@gmail.com'; //Actual gmail address that has an app password set up
$smtpPass = 'lgbk arfq idgr dkdn'; //App password that Google generates for the gmail address, should be secret
$validEmail = 'sample@gmail.com'; //demo login email address, i.e what the login form will accept as an 'existing user'
$validPassword = '1234'; //demo login password                ↑↑↑↑↑↑↑↑↑↑↑↑↑↑

$error = $_SESSION['error_login'] ?? '';
unset($_SESSION['error_login']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) { //validate email format server-side
        $error = 'Please enter a valid email address.';
    } elseif ($email !== $validEmail || $password !== $validPassword) {
        $error = 'Invalid email or password.';
    } else {
        $code = (string) random_int(100000, 999999); //generate random 6-digit code

        //PHPMailer to send the email to Google SMTP server using details from login.php
        //Formatting is included
        //PHPMailer creates a $mail object and send() method is used to send the email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP(); //use SMTP to send instead of PHP mail()
            $mail->Host = 'smtp.gmail.com'; //gmail SMTP server address
            $mail->SMTPAuth = true; //enable SMTP username/password authentication
            $mail->Username = $smtpUser; //SMTP login email
            $mail->Password = $smtpPass; //SMTP login password (gmail app password)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; //enable TLS encryption
            $mail->Port = 587; //SMTP port for STARTTLS
            $mail->setFrom($smtpUser, 'Alice\'s Shop'); //set sender email and display name
            $mail->addAddress($email); //set recipient email address
            $mail->Subject = 'Your verification code'; //set email subject line
            $mail->Body = 'Your 6-digit verification code is: ' . $code; //set plain text email body
            $mail->send(); //send the email

            $_SESSION['mfa_code'] = $code;
            $_SESSION['mfa_expires'] = time() + 300; //code expires in 5 minutes
            header('Location: verify.php');
            exit;
        } catch (Exception $e) { //catch PHPMailer exception if send() fails
            $error = 'Could not send verification email. Check your Gmail SMTP settings.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Email MFA</title>
</head>
<body>
    <h1>Alice's Shop - Login</h1>

    <?php if ($error !== ''): ?>
        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p> <?php //escape output to prevent xss ?>
    <?php endif; ?>

    <form method="post">
        <p>
            <label>Email<br>
                <input type="email" name="email" required maxlength="254"> <!-- email field only accepts email format -->
            </label>
        </p>
        <p>
            <label>Password<br>
                <input type="password" name="password" required maxlength="128"> <!-- password field uses password format -->
            </label>
        </p>

        <button type="submit">Login</button>
    </form>
</body>
</html>
