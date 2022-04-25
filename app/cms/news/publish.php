<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\MiddleWare\{
    Request,
    Constants,
    TextStream,
    ServerRequest
};
use Application\Database\Connection;
use Application\CMS\News\NewsManager;
use Application\CMS\News\CategoryManager;
use Application\Membership\MemberManager;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    $goto = urlencode("/cms/news/publish");
    header('Location: /members/login?goto=' . $goto);
}

$incoming = (new ServerRequest())->initialize();
$outgoing = new Request();
$NewsManager = new NewsManager(Connection::Instance());
$CategoryManager = new CategoryManager(Connection::Instance());

if ($incoming->getMethod() == Constants::METHOD_POST) {
    $action = $incoming->getParsedBody()['action'];
    $body = new TextStream(json_encode($incoming->getParsedBody()));
    // Creates and save and/or publish the result
    $articleID = $NewsManager->save($outgoing->withBody($body));
    switch ($action) {
        case 'publish':
            header('Location: /news/articles/' . $articleID);
            break;
        case 'save':
            $success_msg = "Article saved for future edits and/or publication";
            break;
    }
}
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Create Articles - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
    <script src="/node_modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms-news cms-manage" id="cms-news">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/header.php"; ?>
    <?php require_once dirname(__DIR__, 2) . "/includes/cms-header.php"; ?>
    <div class="ws-container">
        <div class="page-content">
            <div>
                <h1>Publish an article</h1>
                <div class="cms-links">
                    <span>
                        <a href="/cms/">Home</a>
                    </span>
                    <span>
                        <a href="/cms/news/">News</a>
                    </span>
                    <span>
                        <a href="edit">Edit an article</a>
                    </span>
                </div>
            </div>
            <div class="cs-grid">
                <div>
                    <form id="news-form" action="/cms/news/publish" method="post">
                        <?php if (isset($success_msg)) : ?><div class="form-msg success"><span><?= $success_msg; ?></span></div><?php endif; ?>
                        <div class="form-grouping">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-grouping">
                            <label for="categories">Categories</label>
                            <input type="text" class="form-control" id="categories" name="categories" placeholder="A comma-separated list of categories (e.g Alumni, Current students)" required>
                        </div>
                        <textarea id="editor" name="body"></textarea>
                        <input type="hidden" name="authorID" value="<?= $_SESSION['ID']; ?>">
                        <input type="hidden" name="thumbnail" id="thumbnail" required>
                        <div>
                            <button type="submit" name="action" value="publish" class="publish-btn">Publish the article</button>
                            <button type="submit" name="action" value="save" class="save-btn">Save your changes</button>
                        </div>
                    </form>
                </div>
                <aside>
                    <section>
                        <h4>Featuring picture</h4>
                        <div id="thumbnail-upload">
                            <span>Preview</span>
                            <div class="background-cover blurred">
                                <button onclick="toggle_visibility('bc1')">Choose an image from gallery</button>
                            </div>
                        </div>
                    </section>
                    <section id="categories">
                        <h4>Categories</h4>
                        <ul>
                            <?php foreach ($CategoryManager->list() as $category) : ?>
                                <li><a href="#"><?= $category->getName(); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </aside>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
    <script>
        CKEDITOR.replace('editor');
        const form = document.getElementById("news-form");
        form.onsubmit = function(event) {
            const thumbnail = form.querySelector("#thumbnail");
            if (thumbnail.value == "") {
                event.preventDefault();
            }
        }
    </script>
    <div class="background-cover blurred" id="bc1">
        <span class="fas fa-times" id="exit" onclick="toggle_visibility('bc1')"></span>
        <div class="box select-picture">
            <div id="header">
                <h3>Choose a picture</h3>
                <p>Select a picture from the gallery as the featuring picture</p>
            </div>
            <div id="pictures">
                <img src="/static/images/gallery/img12.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img11.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img10.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img9.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img8.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img7.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img6.jpg" onclick="selectPicture(event, 'picture-url')">
                <img src="/static/images/gallery/img5.jpg" onclick="selectPicture(event, 'picture-url')">
            </div>
            <div id="footer">
                <div>
                    <label>Picture's URL (Location)</label>
                    <input type="text" class="form-control" name="picture-url" , id="picture-url">
                </div>
                <div>
                    <button onclick="previewPicture('picture-url','thumbnail-upload','thumbnail')">Select</button>
                    <button>Upload instead</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>