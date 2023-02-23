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
            <img src="<?= $host; ?>/images/logo/Logo.png" alt="logo" style="width: 170px">
            <h3 style="font-size: 1.2em; font-weight: 600">Account activation</h3>
            <p>Hi <?= $recipientName; ?></p>
            <p>Welcome to the La Cadenelle Ex-Students Association (CADEXSA). Click this <a href="<?= $link; ?>">link</a> to activate you're account.</p>
        </div>
        <?php require views_path("/commons/newsletter_footer.php"); ?>
    </div>
</body>

</html>