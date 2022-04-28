<?php
$recipientName = $_GET['name'];
$host = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
?>
<html lang="en">

<body style="margin: 0;
             display: flex;
             line-height: 1.5;
             /* align-items: center; */
             justify-content: center;
             font-family: Helvetica, Arial, sans-serif">
    <style>
        p {
            margin: 1em 0;
        }
    </style>
    <div style="width: 400px;
                margin: 1.5em 0;
                height: fit-content">
        <div style="text-align: center;
                    padding-bottom: 1rem; 
                    border-bottom: 1px solid #d3d3d3">
            <img src="<?= $host; ?>/static/images/logo/Logo.png" alt="logo" style="width: 170px">
            <h3 style="font-size: 1.2em; font-weight: 600">Thank you</h3>
            <p>Hi <?= $recipientName; ?></p>
            <p>Welcome and congratulations on becoming a member of the La Cadenelle Ex-Students Association (CADEXSA).</p>
            <p>We're excited to have you! Cheers!</p>
        </div>
        <?php require_once dirname(__DIR__, 2) . '/includes/newsletter_footer.php'; ?>
    </div>
</body>

</html>