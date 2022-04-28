<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Database\Connection;
use Application\CMS\News\NewsManager;
use Application\DateTime\TimeDuration;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request =  (new ServerRequest())->initialize();
$NewsManager = new NewsManager(Connection::Instance());

$articles = $NewsManager->list(3);
$timeDuration = new TimeDuration();
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Manage News article - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
</head>

<body class="cms-news cms-homepage">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/header.php"; ?>
    <?php require_once dirname(__DIR__, 2) . "/includes/cms-header.php"; ?>
    <div class="ws-container">
        <div class="cms-list-wrapper">
            <div class="list">
                <div class="header">
                    <h2>All News articles</h2>
                </div>

                <?php
                if (!empty($articles)) { ?>
                    <div class="news-grid-container">
                        <?php foreach ($articles as $article) {
                            $articleID = $article->getID();
                            $preview = $NewsManager->preview((int)$articleID, $timeDuration);
                    ?>
                            <div>
                                <article class="news-item">
                                    <div class="news-thumb"><img src="<?= $preview['thumbnail']; ?>" alt="news' thumbnail"></div>
                                    <div class="news-content">
                                        <h5><a href="/news/articles/<?= $articleID; ?>"><?= $preview['title']; ?></a></h5>
                                        <div class="news-item-footer"><a href="/cms/news/edit?id=<?= $articleID; ?>" class="news-link">Update</a><a href="/cms/delete?type=news&id=<?= $articleID; ?>" class="news-link">Delete</a><span><i class="fas fa-clock"></i> <?= $preview['timeDiff']; ?></span></div>
                                    </div>
                                </article>
                            </div>
                        <?php } ?>
                    </div>
                <?php } else { ?>
                    <div style="width: 80%; margin: auto; text-align: center; padding: 3rem 0;">There is no news articles.</div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
</body>

</html>