<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="CADEXSA Gallery pictures">
    <meta name="author" content="Yvan Tchuente">
    <title>Upload pictures - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/pages/cms_picture_uploader.js"></script>
</head>

<body id="cms-picture-uploader">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <main id="page-container">
            <header>
                <h1>Upload your pictures</h1>
                <p>The pictures after being uploaded are the properties of the association.</p>
            </header>
            <?php if (isset($message)) : ?><span class="error"><?= $message; ?></span><?php endif; ?>
            <section id="dropbox">
                <div>
                    <img src="/images/graphics/gallery.png">
                    <h3>Drop your pictures here or <span>browse</span></h3>
                    <p>Accepted file types: .jpg and .jpeg only.</p>
                </div>
            </section>
            <form id="picture-upload" action=".">
                <input type="file" name="picture">
            </form>
            <section id="pictures">
            </section>
        </main>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
    <div class="modal-window" id="picture-description-modal">
        <div class="modal-panel picture-description">
            <div class="header">
                <h3>Picture Description</h3>
            </div>
            <div>
                <div class="form-element-container grid">
                    <label for="shotOn">Shot on</label>
                    <input type="datetime-local" name="shotOn" form="picture-upload" placeholder="Enter the date and time at which the picture was shot" class="form-control">
                </div>
                <div class="form-element-container grid">
                    <label for="desc">Description</label>
                    <textarea name="description" form="picture-upload" placeholder="Enter a description of the event at which this picture was taken" class="form-control"></textarea>
                </div>
            </div>
            <div>
                <button id="cancel_button">Cancel</button>
                <button id="accept_button">Accept</button>
            </div>
        </div>
</body>

</html>