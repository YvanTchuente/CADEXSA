<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Database\Connection;
use Application\CMS\NewsChangeDetector;
use Application\Membership\MemberManager;
use Application\CMS\News\{TagManager, NewsManager};
use Application\MiddleWare\{Constants, Request, ServerRequest, TextStream};

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request =  (new ServerRequest())->initialize();
$NewsManager = new NewsManager(Connection::Instance());
$TagManager = new TagManager(Connection::Instance());

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
    $outgoing_request =  new Request();
    $action = $incoming_request->getParsedBody()['action'];
    $articleID = (int)$incoming_request->getParsedBody()['articleID'];
    if ($TagManager->validate($incoming_request->getParsedBody()['tag'])) {
        $body = new TextStream(json_encode($incoming_request->getParsedBody()));
        $Detector = new NewsChangeDetector($outgoing_request->withBody($body), $articleID, $NewsManager, $TagManager);
        switch ($action) {
            case 'publish':
                if ($changes = $Detector->detect())
                    $NewsManager->modify($articleID, $changes);
                $is_published = $NewsManager->get($articleID)->was_published();
                if (!$is_published) {
                    $NewsManager->publish($articleID);
                    header('Location: /news/article.php?n=' . $articleID);
                } else {
                    $error_msg = "Article already published";
                    $incoming_request =  $incoming_request->withParsedBody(array('id' => $articleID));
                }
                break;
            case 'save':
                if ($changes = $Detector->detect()) {
                    $NewsManager->modify($articleID, $changes);
                    $success_msg = "Article saved for future edits and/or publication";
                    $incoming_request =  $incoming_request->withParsedBody(array('id' => $articleID));
                } else {
                    $error_msg = "No changes were detected";
                    $incoming_request =  $incoming_request->withParsedBody(array('id' => $articleID));
                }
                break;
        }
    } else {
        $error_msg = "Tag is not valid";
        $incoming_request =  $incoming_request->withParsedBody(array('id' => $articleID));
    }
}

$params = $incoming_request->getParsedBody();
if (empty($params)) {
    header('Location: /cms/news');
}
$newsID = $params['id'];
$news_article = $NewsManager->get($newsID);
$tagName = ($TagManager->getTag($news_article))->getName();
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Edit Articles - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
    <script src="/node_modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms-manage" id="cms-news">
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
                <h1>Edit articles</h1>
                <div class="cms-links">
                    <span>
                        <a href="/cms/">Home</a>
                    </span>
                    <span>
                        <a href="/cms/news/">News</a>
                    </span>
                    <span>
                        <a href="publish">Publish an article</a>
                    </span>
                </div>
            </div>
            <div class="cs-grid">
                <div>
                    <form id="news-form" action="/cms/news/edit" method="post">
                        <?php if (isset($success_msg)) : ?><div class="form-msg success"><span><?= $success_msg; ?></span></div><?php endif; ?>
                        <?php if (isset($error_msg)) : ?><div class="form-msg error"><span><?= $error_msg; ?></span></div><?php endif; ?>
                        <div class="form-grouping">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= $news_article->getTitle(); ?>" required />
                        </div>
                        <div class="form-grouping">
                            <label for="tags">Tag of the article</label>
                            <input type="text" class="form-control" id="tag" name="tag" value="<?= $tagName; ?>" required />
                        </div>
                        <input type="hidden" name="articleID" value="<?= $newsID; ?>">
                        <textarea id="editor" name="body"><?= $news_article->getBody(); ?></textarea>
                        <div>
                            <button type="submit" name="action" value="publish" class="publish-btn">Finally Publish</button>
                            <button type="submit" name="action" value="save" class="save-btn">Save your changes</button>
                        </div>
                    </form>
                </div>
                <aside>
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
    <script>
        CKEDITOR.replace('editor');
        CKEDITOR.config.height = 500;
    </script>
</body>

</html>