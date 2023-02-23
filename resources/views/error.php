<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>500 Internal Server Error</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <div style="text-align: center; background-color: lightgray">
        <div class="ws-container" style="padding: 3rem 0">
            <h1 style="font-size: 3rem;margin-bottom:1.5rem">Ooops...</h1>
            <p>We encountered an error, please kindly reload this page to proceed<br>If the error persists please email us the issue to <br /><a href="mailto:<?= config("mail.accounts.admin") ?>" style="color:blue"><?= config("mail.accounts.admin") ?></a></p>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>