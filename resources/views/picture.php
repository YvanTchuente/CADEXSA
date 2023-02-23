<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Members - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>Gallery</h1>
    </header>
    <div class="ws-container" id="picture_wrapper">
        <div><img src="<?= $picture->getLocation(); ?>" alt="image"></div>
        <div>
            <div>
                <h3>Description</h3>
                <p style="text-align: justify;"><?= $picture->getDescription(); ?></p>
            </div>
            <div>
                <h3>Date of shot</h3>
                <span><?= $picture->shotOn()->format('l, j F Y'); ?></span>
            </div>
            <div>
                <h3>Date of upload</h3>
                <span><?= $picture->getPublicationDate()->format('l, j F Y'); ?></span>
            </div>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>