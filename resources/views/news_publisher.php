<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Publish Articles - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script src="/js/picture-upload.js" defer></script>
    <script src="/modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div>
            <h1>News Publisher</h1>
            <div class="cms-links">
                <span>
                    <a href="/cms/">Home</a>
                </span>
                <span>
                    <a href="/cms/news/">News</a>
                </span>
            </div>
        </div>
        <div class="cs-container">
            <form action="/cms/news/publish" method="post">
                <?php if (isset($message)) : ?><div class="form-msg success"><span><?= $message; ?></span></div><?php endif; ?>
                <div class="form-element-container grid">
                    <label for="title">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-element-container grid">
                    <label for="tags">Tags</label>
                    <input type="text" class="form-control" id="tags" name="tags" placeholder="Enter a semi-colon separated list of tags" required>
                </div>
                <div class="form-element-container grid">
                    <label for="tags">Body</label>
                    <textarea id="editor" name="body"></textarea>
                </div>
                <input type="hidden" name="authorId" value="<?= user()->getId(); ?>">
                <input type="hidden" name="image" id="image" required>
                <input type="hidden" name="token" value="<?= $token; ?>" />
                <div>
                    <button type="submit" name="action" value="create" class="save-btn">Create</button>
                    <button type="submit" name="action" value="publish" class="publish-btn">Publish</button>
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
                <section id="categories">
                    <h3>Tags</h3>
                    <ul>
                        <?php foreach ($tags as $tag) : ?>
                            <li><a href="#"><?= $tag; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
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
        const form = document.getElementById("news-form");
        form.onsubmit = function(event) {
            const image = form.querySelector("#image");
            if (image.value == "") event.preventDefault();
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
                    <input type="text" class="form-control" name="picture-url" id="picture-url">
                </div>
                <div>
                    <button>Select</button>
                    <button>Upload</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-window blurred-background" id="picture-uploader-modal">
        <span class="fas fa-times exit"></span>
        <div class="modal-panel picture-uploader">
            <div class="header">
                <h3>Upload Featuring Picture</h3>
            </div>
            <div>
                <div id="picture-preview">
                    <p><i class="fas fa-upload"></i><br />Drag and Drop Here<br />or<br /><span>Browse files</span><br /><span style="color: black; font-size: 0.5em; cursor:auto;">Size limit: 3 MB</span></p>
                </div>
                <p>Accepted file types: .jpeg and .jpg only</p>
            </div>
            <div>
                <button>Upload</button>
                <button>Cancel</button>
                <form>
                    <input type="file" accept=".jpeg, .jpg" name="picture" />
                    <input type="hidden" name="action" id="action" value="updateAvatar" />
                    <input type="hidden" name="exStudentId" id="exStudentId" value="<?= user()->getId(); ?>" />
                    <input type="hidden" name="username" value="<?= user()->getUsername(); ?>" />
                </form>
            </div>
        </div>
    </div>
    <script type="module">
        import {
            selectPicture,
            previewPicture,
            toggleModalVisibility,
        } from "../../../../resources/js/functions/random.js";

        const pictures = document.querySelectorAll(
            ".picture-selection-panel > div:nth-child(2) img"
        );
        const [select_picture_button, upload_option_button] = document.querySelectorAll(
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
        upload_option_button.addEventListener("click", () => {
            toggleModalVisibility("picture-selection-modal");
            toggleModalVisibility("picture-uploader-modal");
        });
    </script>
</body>

</html>