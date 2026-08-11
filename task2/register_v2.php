<?php
session_start(); //needed to show error messages on the form

$error = $_SESSION['error_v2'] ?? '';
unset($_SESSION['error_v2']); //clear error so it doesnt show again on refresh
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <!-- loads google recaptcha v2 widget -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Create Account - reCAPTCHA v2</title>
</head>
<body>
    <h1>Alice's Shop - Registry</h1>

    <?php if ($error !== ''): ?>
        <p><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p> <?php //escape output to prevent xss ?>
    <?php endif; ?>

    <form action="process_v2.php" method="post">
        <p>
            <label>Email<br>
                <input type="email" name="email" required maxlength="254"> <!-- email format checked by browser, max length limits input size -->
            </label>
        </p>
        <p>
            <label>Password<br>
                <input type="password" name="password" required minlength="10" maxlength="128"> <!-- copied input field parameters from figure-2.1 again to prevent XSS attacks -->
            </label>
        </p>
        <p>Password must be at least 10 characters and include uppercase, lowercase, and a number.</p>
        <p>
            <label>Confirm Password<br>
                <input type="password" name="confirm_password" required minlength="10" maxlength="128"> <!-- must match password field -->
            </label>
        </p>

        <!-- Copied from index.php in Week 3 Tutorial files -->
        <div class="g-recaptcha" data-sitekey="6LelDHYtAAAAAEStsVu5BRrWI5dD7hI3WGUCclY1"></div> <!-- shouldnt be pushed into the repo but for simplicity I will -->

        <button type="submit">Register</button>
    </form>
</body>
</html>
