<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>CMS Deletions - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div style="margin: 2rem 0">
            <?php if (isset($noHistory)) : ?>
                <div style="text-align: center; padding: 1rem 0;">
                    <h1>ERROR</h1>
                    <p>There is no previously deleted news article or event detected. Please move to the cms <a href="/cms/" style="color: blue;">homepage</a></p>
                </div>
            <?php elseif (isset($deleted)) : ?>
                <div style="text-align: center; padding: 1rem 0;">
                    <h2 style="color: green;">Successfully deleted</h2>
                </div>
            <?php endif ?>
            <div class="list" id="mementos">
                <div class="header">
                    <h2>Recent deletions</h2>
                </div>
                <?php
                if (isset($mementos)) :
                ?>
                    <?php
                    foreach ($mementos as $memento) :
                        extract($memento);
                    ?>
                        <div class="item">
                            <h3><?= $name; ?></h3>
                            <span style="display: block; margin-bottom: 0.5em">Deleted on <?= date("d/m/Y", strtotime($date)) . " at " . date("h:i A", strtotime($date)); ?></span>
                            <a href="/cms/recover?l=<?= $level; ?>" class="button" style="margin-right: 0.5rem;">Restore</a>
                            <a href="/cms/delete?memento=<?= urlencode($name); ?>" class="button">Delete</a>
                        </div>
                    <?php
                    endforeach;
                    ?>
                <?php
                else :
                ?>
                    <p style="text-align: center;">There are no recent deletions.</p>
                <?php
                endif;
                ?>
            </div>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>