<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>404 Page not found</title>
    <?php require views_path("commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("commons/loader.html"); ?>
    <?= $page_header; ?>
    <div style="text-align: center; background-color: lightgray">
        <div class="ws-container" style="padding: 3rem 0">
            <h1 style="font-size: 5rem;">404</h1>
            <p>Page Not Found on<br /><a href="/" style="color: blue;"><?= $_SERVER['HTTP_HOST']; ?></a></p>
        </div>
    </div>
    <?php require views_path("commons/page_footer.php"); ?>
</body>

</html>