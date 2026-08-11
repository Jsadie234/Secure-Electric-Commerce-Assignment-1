<?php
session_start();

if (!isset($_SESSION['mfa_code'], $_SESSION['mfa_expires'])) { //"user" must log in first
    header('Location: login.php');
    exit;
}

if (time() > $_SESSION['mfa_expires']) { //code has expired
    unset($_SESSION['mfa_code'], $_SESSION['mfa_expires']);
    $_SESSION['error_login'] = 'Verification code expired. Please log in again.';
    header('Location: login.php');
    exit;
}

$result = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');

    if ($code === $_SESSION['mfa_code']) {
        unset($_SESSION['mfa_code'], $_SESSION['mfa_expires']);
        $result = 'success';
    } else {
        unset($_SESSION['mfa_code'], $_SESSION['mfa_expires']);
        $result = 'failure';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Enter Verification Code</title>
</head>
<body>
    <!-- Checks for success or failure of verification and returns appropriate message on page -->
    <?php if ($result === 'success'): ?>
        <h1>Success</h1>
        <p>Login successful. Welcome to Alice's Shop!</p>
    <?php elseif ($result === 'failure'): ?>
        <h1>Failure</h1>
        <p>Invalid verification code. Please try again.</p>
        <p><a href="login.php">Back to login</a></p>
    <?php else: ?>
        <h1>Alice's Shop - Verification</h1>
        <p>Enter the 6-digit code sent to your email.</p>

        <form method="post">
            <p>
                <label>Verification Code<br>
                    <input type="text" name="code" required pattern="\d{6}" maxlength="6" inputmode="numeric"> <!-- must be exactly 6 digits -->
                </label>
            </p>

            <button type="submit">Verify</button>
        </form>
    <?php endif; ?>
</body>
</html>
