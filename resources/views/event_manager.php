<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Manage events - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body class="cms-events cms-homepage">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div class="list">
            <div class="header">
                <h2>Events Manager</h2>
            </div>
            <?php if (isset($events)) : ?>
                <div class="list-container">
                    <?php
                    foreach ($events as $event) :
                        $link = "/events/" . urlencode(strtolower($event->getName()));
                    ?>
                        <article class="event">
                            <div class="event-thumbnail">
                                <img src="<?= $event->getImage(); ?>" alt="event-thumbnail" />
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
                <div style="width: 80%; margin: auto; text-align: center; padding: 3rem 0;">There are no events.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>