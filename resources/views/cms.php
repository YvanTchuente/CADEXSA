<!DOCTYPE html>
<html class="cms" lang=" en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Content Management System - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div class="list">
            <div class="header">
                <h2>Upcoming Events</h2>
                <a href="/cms/events/">See more</a>
            </div>
            <?php if (isset($events)) : ?>
                <div class="list-container">
                    <?php
                    foreach ($events as $event) :
                        $link = "/events/" . urlencode(strtolower($event->getName()));
                    ?>
                        <article class="event">
                            <div class="event-thumbnail">
                                <img src="<?= $event->getImage(); ?>" alt="event_thumbnail" />
                            </div>
                            <div class="event-description">
                                <h2><a href="/events/<?= urlencode(strtolower($event->getName())); ?>"><?= $event->getName(); ?></a></h2>
                                <div><i class="fas fa-calendar-day"></i><?= $event->getOccurrenceDate()->format("l j F"); ?> <i class="fas fa-clock"></i> <?= $event->getOccurrenceDate()->format("g a"); ?></div>
                                <a href="/cms/events/edit?id=<?= $event->getId(); ?>" class="event-link">Update</a>
                                <a href="/cms/delete?type=event&id=<?= $event->getId(); ?>" class="event-link">Delete</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="text-align: center;">There are no events.</div>
            <?php endif; ?>
        </div>
        <div class="list">
            <div class="header">
                <h2>Latest News</h2>
                <a href="/cms/news/">See more</a>
            </div>
            <?php if (isset($news)) : ?>
                <div class="news-container">
                    <?php
                    foreach ($news as $newsArticle) :
                        $link = "/news/" . urlencode(strtolower($newsArticle->getTitle()));
                    ?>
                        <article class="news_article">
                            <div class="image"><img src="<?= $newsArticle->getImage(); ?>" alt="news' image"></div>
                            <div class="body">
                                <h3><a href=<?= $link; ?>><?= $newsArticle->getTitle(); ?></a></h3>
                                <div class="footer">
                                    <a href="/cms/news/edit?id=<?= $newsArticle->getId(); ?>" class="link">Update</a><a href="/cms/delete?type=news&id=<?= $newsArticle->getId(); ?>" class="link">Delete</a><span><i class="fas fa-clock"></i> <?= $newsArticle->getTimeSincePublication(); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="text-align: center;">There are no news articles.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>