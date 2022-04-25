<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\CMS\News\NewsManager;
use Application\DateTime\TimeDuration;
use Application\CMS\Events\EventManager;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming = (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());
$NewsManager = new NewsManager(Connection::Instance());

$events = $EventManager->list(3);
$articles = $NewsManager->list(3);
$timeDuration = new TimeDuration();
?>
<!DOCTYPE html>
<html class="cms" lang=" en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Content Management System - CADEXSA</title>
    <?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body class="cms-homepage">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
    <?php require_once dirname(__DIR__) . "/includes/cms-header.php"; ?>
    <div class="ws-container">
        <div class="cms-list-wrapper">
            <div class="list">
                <div class="header">
                    <h2>Upcoming Events</h2>
                    <a href="/cms/events/">See more</a>
                </div>
                <?php if (!empty($events)) : ?>
                    <div class="events_list">
                        <?php
                        foreach ($events as $event) {
                            $eventID = $event->getID();
                            $preview = $EventManager->preview((int)$eventID);
                        ?>
                            <div class="single-event">
                                <div>
                                    <div class="event_thumbnail">
                                        <img src="<?= $preview['thumbnail']; ?>" alt="event_thumbnail" />
                                    </div>
                                </div>
                                <div>
                                    <div class="display-table">
                                        <div class="table-cell">
                                            <div class="event-text">
                                                <h2><a href="/events/<?= $eventID; ?>"><?= $preview['title']; ?></a></h2>
                                                <h6><i class="fas fa-calendar-day"></i> <?= date("l j F", strtotime($preview['deadline'])); ?> <i class="fas fa-clock"></i> <?= date("g a", strtotime($preview['deadline'])); ?></h6>
                                                <a href="/cms/events/edit?id=<?= $eventID; ?>" class="event-link">Update</a>
                                                <a href="/cms/delete?type=event&id=<?= $eventID; ?>" class="event-link">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php else : ?>
                    <div>There is currently no upcoming event</div>
                <?php endif; ?>
            </div>
            <div class="list">
                <div class="header">
                    <h2>Latest News</h2>
                    <a href="/cms/news/">See more</a>
                </div>
                <?php if (!empty($articles)) : ?>
                    <div class="news-grid-container">
                        <?php
                        foreach ($articles as $article) {
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
                <?php else : ?>
                    <div>There is currently no news article</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>