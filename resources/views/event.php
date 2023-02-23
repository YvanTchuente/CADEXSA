<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title><?= $name ?> - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>Events</h1>
    </header>
    <div class="ws-container">
        <div id="event-container">
            <label>REMAINING TIME <span>COUNTDOWN</span></label>
            <div class="countdown" data-target-date="<?= $occursOn->format("Y-m-d h:i:s"); ?>">
                <div>
                    <label>Days</label>
                    <div class="flip-card" data-days-tens>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                    <div class="flip-card" data-days-unit>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                </div>
                <div>
                    <label>Hours</label>
                    <div class="flip-card" data-hours-tens>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                    <div class="flip-card" data-hours-unit>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                </div>
                <div>
                    <label>Minutes</label>
                    <div class="flip-card" data-minutes-tens>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                    <div class="flip-card" data-minutes-unit>
                        <div class="topHalf"></div>
                        <div class="bottomHalf"></div>
                    </div>
                </div>
                <div>
                    <label>Seconds</label>
                    <div class="flip-card" data-seconds-tens>
                        <div class="topHalf">9</div>
                        <div class="bottomHalf">9</div>
                    </div>
                    <div class="flip-card" data-seconds-unit>
                        <div class="topHalf">9</div>
                        <div class="bottomHalf">9</div>
                    </div>
                </div>
            </div>
            <div id="event-image">
                <img src="<?= $image; ?>" alt="event-image" />
            </div>
            <div id="event-metadata">
                <h1><?= $name; ?></h1>
                <p>
                    <span><i class="fas fa-calendar-day"></i> <?= $occursOn->format("l d F Y"); ?></span>
                    <span><i class="fas fa-clock"></i> <?= $occursOn->format("g a"); ?></span>
                    <span><i class="fas fa-map-marker-alt"></i> <?= $venue; ?></span>
                </p>
            </div>
            <div id="event-description">
                <h2>Event Description</h2>
                <?= $description; ?>
            </div>
            <div id="call-to-action">
                <?php if (isset($message)) : ?>
                    <span class="error"><?= $message ?></span>
                <?php else : ?>
                    <a href="?action=participate" class="button">Participate</a>
                <?php endif; ?>
            </div>
            <?php if (isset($participants)) : ?>
                <div id="event-participants">
                    <h2>Participants</h2>
                    <div id="event-participants-container">
                        <?php foreach ($participants as $participant) : ?>
                            <div class="event-participant">
                                <img src="<?= $participant->getAvatar(); ?>" alt="<?= $participant->getUsername(); ?>_profile_picture">
                                <label><?= (string) $participant->getName(); ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>