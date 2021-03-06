<?php

require_once dirname(__DIR__, 2) . '/config/index.php';
require_once dirname(__DIR__, 2) . '/config/mailserver.php';

use Application\MiddleWare\{
    Stream,
    Request,
    Constants,
    TextStream,
    ServerRequest
};
use Application\Network\Requests;
use Application\PHPMailerAdapter;
use Application\Database\Connection;
use Application\DateTime\TimeDuration;
use Application\Membership\MemberManager;
use Application\CMS\Gallery\PictureManager;
use Application\Membership\NewsletterAgent;
use Application\CMS\News\{NewsManager, TagManager};

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    $goto = urlencode("/cms/news/publish");
    header('Location: /members/login?goto=' . $goto);
}

$incoming_request =  (new ServerRequest())->initialize();
$outgoing_request =  new Request();
$NewsManager = new NewsManager(Connection::Instance());
$TagManager = new TagManager(Connection::Instance());

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
    $action = $incoming_request->getParsedBody()['action'];
    // In case the user uploaded a picture as thumbnail
    if (!preg_match('/\/articles_thumbnails\//', $incoming_request->getParsedBody()['thumbnail'])) {
        $filename = sha1($_SESSION['username'] . $_SESSION['ID']) . ".jpg";
        if (file_exists($filename)) unlink($filename);
    } else {
        $oldName = sha1($_SESSION['username'] . $_SESSION['ID']) . ".jpg";
        $path = dirname(__DIR__, 2) . "/static/images/articles_thumbnails/";
        $picture = new Stream($path . $oldName);
        $newName = sha1($picture->getContents()) . ".jpg";
        $is_renamed = rename($path . $oldName, $path . $newName);
        if ($is_renamed) {
            $body = $incoming_request->getParsedBody();
            $body['thumbnail'] = "/static/images/articles_thumbnails/" . $newName;
            $incoming_request =  $incoming_request->withParsedBody($body);
        }
    }
    $body = new TextStream(json_encode($incoming_request->getParsedBody()));
    $outgoing_request =  $outgoing_request->withBody($body);
    // Creates and save and/or publish the result
    $articleID = $NewsManager->save($outgoing_request->withBody($body));
    switch ($action) {
        case 'publish':
            sleep(1);
            $preview = $NewsManager->preview($articleID, new TimeDuration());
            $template_file_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/includes/mail_templates/new_article_mail.php';
            $template_file_content = (new Requests())->post($template_file_url, $preview);

            $mail_subject = $NewsManager->get($articleID)->getTitle();
            $mail_body = $template_file_content;
            $mailer = new PHPMailerAdapter(MAILSERVER_HOST, MAILSERVER_NEWSLETTER_ACCOUNT, MAILSERVER_PASSWORD);
            $newsletter_agent = new NewsletterAgent(Connection::Instance(), $mailer);
            $newsletter_agent->broadcast($mail_subject, $mail_body, 'Cadexsa Article Alerts');
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
    <script type="module" src="/static/dist/js/pages/cms_publisher.js"></script>
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
                            <label for="tags">Tag of the article</label>
                            <input type="text" class="form-control" id="tag" name="tag" placeholder="Categorize the article with a tag" required>
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
                            <div class="background-wrapper blurred">
                                <button>Choose an image from gallery</button>
                            </div>
                        </div>
                    </section>
                    <section id="categories">
                        <h4>Tags</h4>
                        <ul>
                            <?php foreach ($TagManager->list() as $tag) : ?>
                                <li><a href="#"><?= $tag->getName(); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </aside>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
    <script type="module">
        import {
            toggleBackgroundWrapperVisibility
        } from "/static/src/js/functions/random.js";
        const open_uploader_button = document.querySelector("#thumbnail-upload button");
        open_uploader_button.addEventListener("click", () => toggleBackgroundWrapperVisibility("bc1"));
    </script>
    <script>
        CKEDITOR.replace('editor');
        CKEDITOR.config.height = 500;
        // Handles form
        const form = document.getElementById("news-form");
        form.onsubmit = function(event) {
            const thumbnail = form.querySelector("#thumbnail");
            if (thumbnail.value == "") event.preventDefault();
        }
    </script>
    <div class="background-wrapper blurred" id="bc1">
        <span class="fas fa-times" id="exit"></span>
        <div class="box select-picture">
            <div id="header">
                <h3>Choose a picture</h3>
                <p>Select a picture from the gallery as the featuring picture</p>
            </div>
            <div id="pictures">
                <?php
                $PictureManager = new PictureManager(Connection::Instance());
                $pictures = $PictureManager->list(8);
                foreach ($pictures as $picture) :
                    $src = $picture->getLocation();
                ?>
                    <img src="<?= $src; ?>">
                <?php endforeach; ?>
            </div>
            <div id="footer">
                <div>
                    <label>Picture's URL (Location)</label>
                    <input type="text" class="form-control" name="picture-url" , id="picture-url">
                </div>
                <div>
                    <button>Select</button>
                    <button>Upload</button>
                </div>
            </div>
        </div>
    </div>
    <div class="background-wrapper blurred" id="bc2">
        <span class="fas fa-times" id="exit"></span>
        <div class="box picture-uploader">
            <div id="header">
                <h3>Upload picture</h3>
                <button>Upload</button>
            </div>
            <div id="preview">
                <button>Drop a picture to upload or browse</button>
            </div>
            <div id="footer">
                <span></span>
                <form>
                    <input type="file" name="picture" style="display: none;">
                    <button type="reset">Reset</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>