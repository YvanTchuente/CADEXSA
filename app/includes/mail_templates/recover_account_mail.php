<?php
extract($_POST);
$host = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'];
?>
<html lang="en">
    <body style="margin: 0;
             display: flex;
             line-height: 1.5;
             justify-content: center;
             font-family: Helvetica, Arial, sans-serif">
        <div style="width: 500px;
                margin: 1.5em 0;
                height: fit-content">
            <img src="<?= $host; ?>/static/images/logo/Logo.png" alt="logo" style="width: 170px">
            <p style="margin: 1rem 0 2em;"><?= $username; ?>, you have requested to change your password. Click on the link below to continue the process.</p><p style="margin: 1rem 0;"><a href="<?= $link; ?>" style="color: white; background: steelblue; text-decoration: none; border-radius: 2em; padding: 0.8em 1.5em;">Recover your account</a></p>
        </div>
    </body>
</html>