<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Publish Events - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script src="/modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div>
            <h1>Event Publisher</h1>
            <div class="cms-links">
                <span>
                    <a href="/cms/">Home</a>
                </span>
                <span>
                    <a href="/cms/events/">Events</a>
                </span>
            </div>
        </div>
        <div class="cs-container">
            <form action="/cms/events/publish" method="post">
                <?php if (isset($message)) : ?><div class="form-msg success"><span><?= $message; ?></span></div><?php endif; ?>
                <div class="form-element-container grid">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-element-container grid">
                    <label for="name">Venue</label>
                    <input type="text" class="form-control" id="venue" name="venue" required>
                </div>
                <div class="form-element-container grid">
                    <label for="occursOn">Occurs on</label>
                    <input type="datetime-local" class="form-control" id="occursOn" name="occursOn" style="margin-bottom:1rem;" required>
                </div>
                <input type="hidden" name="image" id="image" required>
                <div class="form-element-container grid">
                    <label for="editor">Description</label>
                    <textarea id="editor" name="description"></textarea>
                </div>
                <input type="hidden" name="token" value="<?= $token; ?>" />
                <div>
                    <button type="submit" class="publish-btn">Publish</button>
                </div>
            </form>
            <aside>
                <section>
                    <h3>Featuring picture</h3>
                    <div id="image-upload">
                        <span>Preview</span>
                        <div class="modal-window blurred-background">
                            <button>Choose an image from gallery</button>
                        </div>
                    </div>
                </section>
            </aside>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
    <script type="module">
        import {
            toggleModalVisibility
        } from "/resources/js/functions/random.js";
        const open_uploader_button = document.querySelector("#image-upload button");
        open_uploader_button.addEventListener("click", () => toggleModalVisibility("picture-selection-modal"));
    </script>
    <script>
        CKEDITOR.replace('editor');
        CKEDITOR.config.height = 500;
        const form = document.getElementById("events-form");
        form.onsubmit = function(event) {
            const image = form.querySelector("#image");
            if (image.value == "") {
                event.preventDefault();
            }
        }
    </script>
    <div class="modal-window blurred-background" id="picture-selection-modal">
        <span class="fas fa-times exit"></span>
        <div class="modal-panel picture-selection-panel">
            <div>
                <h3>Choose a picture</h3>
                <p>Select a picture from the gallery as the featuring picture</p>
            </div>
            <div>
                <?php
                foreach ($pictures as $picture) :
                    $src = $picture->getLocation();
                ?>
                    <img src="<?= $src; ?>">
                <?php endforeach; ?>
            </div>
            <div>
                <div>
                    <input type="text" class="form-control" name="picture-url" , id="picture-url">
                </div>
                <div>
                    <button>Select</button>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        import {
            selectPicture,
            previewPicture
        } from "/resources/js/functions/random.js";

        const pictures = document.querySelectorAll(
            ".picture-selection-panel > div:nth-child(2) img"
        );
        const select_picture_button = document.querySelector(
            ".picture-selection-panel > div:nth-child(3) button"
        );

        for (const picture of pictures) {
            picture.addEventListener("click", (event) => {
                selectPicture(event, "picture-url");
            });
        }
        select_picture_button.addEventListener("click", () =>
            previewPicture("picture-url", "image-upload", "image")
        );
    </script>
</body>

</html>