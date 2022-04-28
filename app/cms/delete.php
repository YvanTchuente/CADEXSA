<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\CMS\Caretaker;
use Application\MiddleWare\Stream;
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

$filePath = dirname(__DIR__) . '/tmp/cms_deleted_items';
if (!file_exists($filePath)) {
    if (!empty($incoming_request->getParsedBody())) {
        touch($filePath);
    } else {
        header('Location: /cms/');
    }
}

try {
    $cms_deletion_history_file = new Stream($filePath);
} catch (RuntimeException $e) {
    goto stream_not_opened;
}
$cms_deletion_history = (string) $cms_deletion_history_file; // Calls the __toString method of the stream object

if (isset($payload['undo'])) {
    if (!empty($cms_deletion_history)) {
        if (isset($payload['l'])) {
            $level = $payload['l'];
        }
        $caretaker = unserialize($cms_deletion_history);
        if (isset($level)) {
            $memento = $caretaker->getHistory()[$level];
            $mementoName = $memento->getName();
            $caretaker->undo($level);
        } else {
            $memento = $caretaker->getLastMemento();
            $mementoName = $memento->getName();
            $caretaker->undo();
        }
        //Retrieve the originator and undo the deletion
        $deleteCommand = $caretaker->getOriginator();
        $deleteCommand->undo();
        if (empty($caretaker->getHistory())) {
            unlink($filePath);
            unset($cms_deletion_history);
        } else {
            $serialized_caretaker = serialize($caretaker);
            $cms_deletion_history_file->rewind(); // Reset pointer to beginning of the file
            $cms_deletion_history_file->write($serialized_caretaker);
            $cms_deletion_history = $serialized_caretaker;
        }
        $msg = "<h2>Restored back</h2><h5>$mementoName</h5><a href='/cms/' style='color: blue;'>Move to homepage</a>";
        $has_done = true;
    } else {
        $error = true;
    }
}

if (isset($payload['type']) && isset($payload['id'])) {
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

    if ($manager->exists($payload['id'])) {
        $title = $manager->get($payload['id'])->getTitle();
        if (!empty($cms_deletion_history)) {
            $caretaker = unserialize($cms_deletion_history);
            $caretaker->setOriginator($deleteCommand);
        } else {
            $caretaker = new Caretaker($deleteCommand);
        }

        $deleteCommand->setID($payload['id']);
        $caretaker->backup();
        // Store the serialized object
        $serialized_caretaker = serialize($caretaker);
        $cms_deletion_history_file->rewind(); // Reset pointer to beginning of the file
        $cms_deletion_history_file->write($serialized_caretaker); // Store the mementos
        $cms_deletion_history = $serialized_caretaker;
        // Execute the deletion
        $deleteCommand->execute();
        if ($payload['type'] === "news") {
            Connection::Instance()->getConnection()->query("DELETE FROM news_tags WHERE newsID = " . $payload['id']);
        }
        $has_done = true;
    } else {
        header('Location: /cms/');
    }
}

stream_not_opened:
if (!empty($cms_deletion_history)) {
    $caretaker = unserialize($cms_deletion_history);
    if (!empty($caretaker->getHistory())) {
        $history_list = $caretaker->getHistory();
    }
} elseif (!$has_done) {
    header('Location: /cms/');
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
        <div style="margin: 2rem 0">
            <h2>Deletion Page</h2>
            <?php if (isset($has_done)) { ?>
                <div style="width: 80%; margin: auto; text-align: center; padding: 3rem 0;">
                    <?php if (isset($error)) { ?>
                        <h1>ERROR</h1>
                        <p>There is no previously deleted news article or event detected. Please move to the cms <a href="/cms/" style="color: blue;">homepage</a></p>
                    <?php } else if (isset($msg)) {
                        echo "<p>$msg</p>";
                    } else { ?>
                        <h2 style="color: green;">Successfully deleted</h2>
                        <h5 style="margin-bottom: 1.5rem;"><?= $title; ?></h5>
                        <a href="/cms/delete?undo" class="button">Restore Back</a>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php
            if (isset($history_list)) {
            ?>
                <div class="list" id="mementos">
                    <div class="header">
                        <h3>Recently deleted items</h3>
                    </div>
                    <?php
                    for ($i = 0; $i < count($history_list); $i++) {
                        $history_item = $history_list[$i];
                        $name = $history_item->getName();
                        $date = $history_item->getDate();
                    ?>
                        <div class="item">
                            <h5><?= $name; ?></h5>
                            <p>
                                <a href="/cms/delete?undo&l=<?= $i; ?>" class="button" style="margin-right: 0.5rem;"><i class="fas fa-undo"></i> Restore</a>
                                <span>Deleted on <?= date("d/m/Y", strtotime($date)) . " at " . date("h:i A", strtotime($date)); ?></span>
                            </p>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>
        </div>
    </div>
    <?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>