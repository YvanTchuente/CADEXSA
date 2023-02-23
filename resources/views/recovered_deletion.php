<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Recovered Deletion - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div style="margin: 2rem 0">
            <h2>Restored back</h2>
            <h5><?= $mementoName; ?></h5>
            <a href="/cms/" style="color: blue;">Move to homepage</a>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>