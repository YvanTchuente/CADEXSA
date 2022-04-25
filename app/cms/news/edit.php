<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Database\Connection;
use Application\CMS\NewsChangeDetector;
use Application\Membership\MemberManager;
use Application\CMS\News\{CategoryManager, NewsManager};
use Application\MiddleWare\{Constants, Request, ServerRequest, TextStream};

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming = (new ServerRequest())->initialize();
$outgoing = new Request();

$NewsManager = new NewsManager(Connection::Instance());
$CategoryManager = new CategoryManager(Connection::Instance());

if ($incoming->getMethod() == Constants::METHOD_POST) {
    $action = $incoming->getParsedBody()['action'];
    $articleID = (int)$incoming->getParsedBody()['articleID'];
    $body = new TextStream(json_encode($incoming->getParsedBody()));
    $Detector = new NewsChangeDetector($outgoing->withBody($body), $articleID, $NewsManager, $CategoryManager);
    if ($changes = $Detector->detect()) {
        $NewsManager->modify($articleID, $changes);
        switch ($action) {
            case 'publish':
                $NewsManager->publish($articleID);
                header('Location: /news/article.php?n=' . $articleID);
                break;
            case 'save':
                $success_msg = "Article saved for future edits and/or publication";
                // Reset incoming request object
                $incoming = $incoming->withParsedBody(array('id' => $articleID));
                break;
        }
    } else {
        $error_msg = "No changes were detected";
        $incoming = $incoming->withParsedBody(array('id' => $articleID));
    }
}

$params = $incoming->getParsedBody();
if (empty($params)) {
    header('Location: /cms/news');
}
$newsID = $params['id'];
$news_article = $NewsManager->get($newsID);
$categories = $CategoryManager->getCategory($news_article);
foreach ($categories as $category) {
    $article_categories[] = $category->getName();
}
$list_categories = implode(", ", $article_categories);
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
                            <label for="categories">Categories</label>
                            <input type="text" class="form-control" id="categories" name="categories" value="<?= $list_categories; ?>" required />
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
    </script>
</body>

</html>