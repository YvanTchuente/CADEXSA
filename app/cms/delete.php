<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;
use Application\CMS\News\{DeleteNewsCommand, NewsManager, DeleteNewsState};
use Application\CMS\Events\{DeleteEventCommand, EventManager, DeleteEventState};

if (!(MemberManager::Instance()->is_logged_in() && $_SESSION['level'] != 3)) {
    header('Location: /members/login');
}

$incoming_request =  (new ServerRequest())->initialize();
$payload = $incoming_request->getParsedBody();

if (isset($payload['type'])) {
    switch ($payload['type']) {
        case 'event':
            $manager = new EventManager(Connection::Instance());
            $deleteCommand = new DeleteEventCommand($manager);
            break;
        case 'news':
            $manager = new NewsManager(Connection::Instance());
            $deleteCommand = new DeleteNewsCommand($manager);
            break;
    }

    if (isset($payload['id'])) {
        $title = $manager->get($payload['id'])->getTitle();
        $deleteCommand->setID($payload['id']);
        $memento = $deleteCommand->createMemento();
        $_SESSION['LastDeletedItem'] = serialize($memento);
        $deleteCommand->execute();
    }

    if (isset($payload['undo'])) {
        if (isset($_SESSION['LastDeletedItem'])) {
            $memento = unserialize($_SESSION['LastDeletedItem']);
            $deleteCommand->setMemento($memento);
            $deleteCommand->undo();
            unset($_SESSION['LastDeletedItem']);
            $msg = "The deletion was successfully undone.<br/><a href='/cms/' style='color: blue;'>Move to homepage</a>";
        } else {
            $error = true;
        }
    }
} else {
    header('Location: ' . $incoming->getServerParams()['HTTP_REFERER']);
}
?>
<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Deletion page - CADEXSA</title>
    <?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body class="cms-news cms-homepage">
    <div id="loader">
        <div>
            <div class="spinner"></div>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
    <?php require_once dirname(__DIR__) . "/includes/cms-header.php"; ?>
    <div class="ws-container">
        <div style="width: 80%; margin: auto; text-align: center; padding: 4rem 0;">
            <?php if (isset($error)) { ?>
                <h1>ERROR</h1>
                <p>There is no previously deleted news article or event detected. Please move to the cms <a href="/cms/" style="color: blue;">homepage</a></p>
            <?php } else if (isset($msg)) {
                echo "<p>$msg</p>";
            } else { ?>
                <h1>Successfully deleted</h1>
                <h3 style="margin-bottom: 1.5rem;"><?= $title; ?></h3>
                <a href="/cms/delete?type=<?= $payload['type']; ?>&undo" class="button">Undo the deletion</a>
            <?php } ?>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>