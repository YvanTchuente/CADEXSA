<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\MiddleWare\{
    Request,
    Constants,
    TextStream,
    ServerRequest
};
use Application\Database\Connection;
use Application\CMS\EventChangeDetector;
use Application\CMS\Events\EventManager;
use Application\Membership\MemberManager;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request =  (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
    $outgoing_request =  new Request();
    $eventID = (int)$incoming_request->getParsedBody()['eventID'];
    $body = new TextStream(json_encode($incoming_request->getParsedBody()));
    $Detector = new EventChangeDetector($outgoing_request->withBody($body), $eventID, $EventManager);
    if ($changes = $Detector->detect()) {
        $EventManager->modify($eventID, $changes);
        header('Location: /events/' . $eventID);
    } else {
        $error_msg = "No changes were detected";
        $incoming_request =  $incoming_request->withParsedBody(array('id' => $eventID));
    }
}

$params = $incoming_request->getParsedBody();
if (empty($params)) {
    header('Location: /cms/events');
}
$eventID = $params['id'];
$event = $EventManager->get($eventID);
$deadline_timestamp = strtotime($event->getDeadlineDate());
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Edit Planned Events - CADEXSA</title>
    <?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
    <script src="/node_modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms-manage" id="cms-event">
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
                <h1>Edit planned events</h1>
                <div class="cms-links">
                    <span>
                        <a href="/cms/">Home</a>
                    </span>
                    <span>
                        <a href="/cms/events/">Events</a>
                    </span>
                    <span>
                        <a href="plan">Plan an event</a>
                    </span>
                </div>
            </div>
            <div class="cs-grid">
                <div>
                    <form id="events-form" action="/cms/events/edit" method="post">
                        <?php if (isset($success_msg)) : ?><div class="form-msg success"><span><?= $success_msg; ?></span></div><?php endif; ?>
                        <?php if (isset($error_msg)) : ?><div class="form-msg error"><span><?= $error_msg; ?></span></div><?php endif; ?>
                        <div class="form-grouping">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= $event->getTitle(); ?>" required>
                        </div>
                        <div class="form-grouping">
                            <label for="title">Venue</label>
                            <input type="text" class="form-control" id="venue" name="venue" value="<?= $event->getVenue(); ?>" required>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="deadline">Deadline</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" value="<?= date("Y-m-d", $deadline_timestamp); ?>" style="margin-bottom:1rem;" required>
                            </div>
                            <div>
                                <label for="deadline">Time</label>
                                <input type="time" class="form-control" id="deadline_time" name="deadline_time" value="<?= date("H:i", $deadline_timestamp); ?>" style="margin-bottom:1rem;" required>
                            </div>
                        </div>
                        <input type="hidden" name="eventID" value="<?= $eventID; ?>">
                        <textarea id="editor" name="body"><?= $event->getBody(); ?></textarea>
                        <div>
                            <button type="submit" class="save-btn">Save and Publish</button>
                        </div>
                    </form>
                </div>
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