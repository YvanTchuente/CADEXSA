<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Database\Connection;
use Application\CMS\Events\EventManager;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming = (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());
$events = $EventManager->list(3);
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Manage Events - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
</head>

<body class="cms-events cms-homepage">
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
                    <h2>All Events</h2>
                </div>
                <?php
                if (!empty($events)) {
                ?>
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
                <?php } else { ?>
                    <div style="width: 80%; margin: auto; text-align: center; padding: 3rem 0;">There is no scheduled upcoming event.</div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
</body>

</html>