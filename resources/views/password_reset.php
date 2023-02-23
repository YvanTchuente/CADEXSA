<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Password reset - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body id="password_reset">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <div class="ws-container">
        <form action="password_reset" method="post">
            <h2>Password reset</h2>
            <?php
            switch ($step):
                case 3:
            ?>
                    <p>Enter a new password to replace the former, the password should be at least 10 characters long.</p>
                    <?= (isset($message)) ? "<p class='error'>" . $message . "</p>" : ''; ?>
                    <div class="form-element-container">
                        <div>
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" placeholder="Enter a password" name="new_password" required />
                            <button type="button" class="password-visibility-button"><i class="fas fa-eye"></i></button>
                        </div>
                    </div>
                    <?= (isset($exStudentId)) ? '<input type="hidden" name="exStudentId" value="' . $exStudentId . '">' : ''; ?>
                    <button type="submit" class="full-width">Submit</button>
                <?php
                    break;
                case 2:
                ?>
                    <p>We will send you an email to reset your password. Enter your email address.</p>
                    <?= (isset($message)) ? "<p class='error'>" . $message . "</p>" : ''; ?>
                    <div class="form-element-container">
                        <div><i class="fas fa-envelope"></i><input type="email" id="email" name="email" placeholder="Enter an email" required /></div>
                    </div>
                    <?= (isset($exStudentId)) ? '<input type="hidden" name="exStudentId" value="' . $exStudentId . '">' : ''; ?>
                    <button type="submit" class="full-width">Submit</button>
                <?php
                    break;
                case 1:
                ?>
                    <p style="margin-bottom: 1rem">Enter your current username and continue</p>
                    <?= (isset($message)) ? "<p class='error'>" . $message . "</p>" : ''; ?>
                    <div class="form-element-container">
                        <div>
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" placeholder="Enter your username" required />
                        </div>
                    </div>
                    <button type="submit" class="full-width">Continue</button>
                    <footer>
                        <p>Remember your password? <a href="login">Sign in</a></p>
                    </footer>
            <?php
                    break;
            endswitch;
            ?>
            <input type="hidden" name="current-step" value="<?= $step; ?>" />
            <input type="hidden" name="token" value="<?= $token; ?>" />
        </form>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>