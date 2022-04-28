<?php

require_once dirname(__DIR__, 2) . '/config/index.php';
require_once dirname(__DIR__, 2) . '/config/mailserver.php';

use Application\MiddleWare\{
    Request,
    Constants,
    TextStream,
    ServerRequest
};
use Application\Network\Requests;
use Application\PHPMailerAdapter;
use Application\Database\Connection;
use Application\DateTime\TimeDuration;
use Application\CMS\Events\EventManager;
use Application\Membership\MemberManager;
use Application\CMS\Gallery\PictureManager;
use Application\Membership\NewsletterAgent;

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request =  (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
    $outgoing_request =  new Request();
    $body = new TextStream(json_encode($incoming_request->getParsedBody()));
    // Creates and publish the result
    $eventID = $EventManager->save($outgoing_request->withBody($body));
    sleep(1);
    $preview = $EventManager->preview($eventID, new TimeDuration());
    $template_file_url = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/includes/mail_templates/new_event_mail.php?deadline=" . urlencode($preview['deadline']);
    $template_file_content = (new Requests())->post($template_file_url, $preview);

    $title = $EventManager->get($eventID)->getTitle();
    $mail_subject = 'Event: ' . $title;
    $mail_body = $template_file_content;
    $mailer = new PHPMailerAdapter(MAILSERVER_HOST, MAILSERVER_NEWSLETTER_ACCOUNT, MAILSERVER_PASSWORD);
    $newsletter_agent = new NewsletterAgent(Connection::Instance(), $mailer);
    $newsletter_agent->broadcast($mail_subject, $mail_body, 'Cadexsa Event Alerts');
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
                            <div class="background-wrapper blurred">
                                <button>Choose an image from gallery</button>
                            </div>
                        </div>
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
        const form = document.getElementById("events-form");
        form.onsubmit = function(event) {
            const thumbnail = form.querySelector("#thumbnail");
            if (thumbnail.value == "") {
                event.preventDefault();
            }
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
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        import {
            selectPicture,
            previewPicture
        } from "/static/src/js/functions/random.js";
        const uploader_picture_elems = document.querySelectorAll(
            ".select-picture #pictures img"
        );
        const select_picture_button = document.querySelector(
            ".select-picture #footer button"
        );

        for (const picture of uploader_picture_elems) {
            picture.addEventListener("click", (event) => {
                selectPicture(event, "picture-url");
            });
        }
        select_picture_button.addEventListener("click", () =>
            previewPicture("picture-url", "thumbnail-upload", "thumbnail")
        );
    </script>
</body>

</html>