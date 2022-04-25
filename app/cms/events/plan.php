<?php

require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\MiddleWare\{
    Request,
    Constants,
    TextStream,
    ServerRequest
};
use Application\Database\Connection;
use Application\CMS\Events\EventManager;
use Application\Membership\MemberManager;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming = (new ServerRequest())->initialize();
$outgoing = new Request();
$EventManager = new EventManager(Connection::Instance());

if ($incoming->getMethod() == Constants::METHOD_POST) {
    $body = new TextStream(json_encode($incoming->getParsedBody()));
    // Creates and publish the result
    $eventID = $EventManager->save($outgoing->withBody($body));
    header('Location: /events/' . $eventID);
}
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Plan Events - CADEXSA</title>
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
                <h1>Plan an event</h1>
                <div class="cms-links">
                    <span>
                        <a href="/cms/">Home</a>
                    </span>
                    <span>
                        <a href="/cms/events/">Events</a>
                    </span>
                    <span>
                        <a href="edit">Edit an planned event</a>
                    </span>
                </div>
            </div>
            <div class="cs-grid">
                <div>
                    <form id="events-form" action="/cms/events/plan" method="post">
                        <?php if (isset($success_msg)) : ?><div class="form-msg success"><span><?= $success_msg; ?></span></div><?php endif; ?>
                        <div class="form-grouping">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="form-grouping">
                            <label for="title">Venue</label>
                            <input type="text" class="form-control" id="venue" name="venue" required>
                        </div>
                        <div class="form-group">
                            <div>
                                <label for="deadline">Deadline</label>
                                <input type="date" class="form-control" id="deadline" name="deadline" style="margin-bottom:1rem;" required>
                            </div>
                            <div>
                                <label for="deadline">Time</label>
                                <input type="time" class="form-control" id="deadline_time" name="deadline_time" style="margin-bottom:1rem;" required>
                            </div>
                        </div>
                        <input type="hidden" name="thumbnail" id="thumbnail" required>
                        <textarea id="editor" name="body"></textarea>
                        <div>
                            <button type="submit" class="publish-btn">Publish the event</button>
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
                </aside>
            </div>
        </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
    <script>
        CKEDITOR.replace('editor');
        const form = document.getElementById("events-form");
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
                </div>
            </div>
        </div>
    </div>
</body>

</html>