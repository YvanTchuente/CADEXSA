<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Event editor - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script src="/modules/ckeditor4/ckeditor.js"></script>
</head>

<body class="cms">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div>
            <h1>Event editor</h1>
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
        <div>
            <form action="/cms/events/edit" method="post">
                <?php if (isset($message)) : ?><div class="form-msg success"><span><?= $message; ?></span></div><?php endif; ?>
                <?php if (isset($error)) : ?><div class="form-msg error"><span><?= $error; ?></span></div><?php endif; ?>
                <div class="form-element-container grid">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= $event->getName(); ?>" required>
                </div>
                <div class="form-element-container grid">
                    <label for="venue">Venue</label>
                    <input type="text" class="form-control" id="venue" name="venue" value="<?= $event->getVenue(); ?>" required>
                </div>
                <div class="form-element-container grid">
                    <label for="occursOn">Occurs on</label>
                    <input type="datetime-local" class="form-control" id="occursOn" name="occursOn" value="<?= date($event->getOccurrenceDate()->format("Y-m-d H:i:s")); ?>" style="margin-bottom:1rem;" required>
                </div>
                <input type="hidden" name="eventId" value="<?= $event->getId(); ?>">
                <div class="form-element-container grid">
                    <label for="editor">Description</label>
                    <textarea id="editor" name="description"><?= $event->getDescription(); ?></textarea>
                </div>
                <input type="hidden" name="token" value="<?= $token; ?>" />
                <div>
                    <button type="submit" class="save-btn">Save</button>
                </div>
            </form>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
    <script>
        CKEDITOR.replace('editor');
        CKEDITOR.config.height = 500;
    </script>
</body>

</html>