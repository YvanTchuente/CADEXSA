<?php
require_once __DIR__ . '/config/index.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CADEXSA Latest news">
    <meta name="author" content="Yvan Tchuente">
    <title>Page Not Found - CADEXSA</title>
    <?php require_once __DIR__ . "/includes/head_tag_includes.php"; ?>
</head>

<body id="news_dir">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once __DIR__ . "/includes/header.php"; ?>
    <div style="text-align: center; background-color: lightgray">
        <div class="ws-container" style="padding: 3rem 0">
            <h1 style="font-size: 5rem;">404</h1>
            <p>Page Not Found on<br /><a href="/" style="color: blue;"><?= $_SERVER['HTTP_HOST']; ?></a></p>
        </div>
    </div>
    <?php require_once __DIR__ . "/includes/footer.php"; ?>
</body>

</html>