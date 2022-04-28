<?php
extract($_POST); // $_POST contains the preview data
$host = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
?>
<html lang="en">

<body style="margin: 0;
             display: flex;
             line-height: 1.5;
             align-items: center;
             justify-content: center;
             font-family: Helvetica, Arial, sans-serif">
    <div style="width: 400px;
                margin: 1.5em 0;
                height: fit-content">
        <div style="display: flex;
                    justify-content: center;">
            <img src="<?= $host; ?>/static/images/logo/Logo.png" alt="logo" style="width: 170px">
        </div>
        <article style="padding: 1.5em 0;
                        height: fit-content;
                        background-color: white;
                        border-bottom: 1px solid #d3d3d3;">
            <div style="width: 100%;
                        height: 200px;
                        position: relative;">
                <img src="<?= $host; ?><?= $thumbnail; ?>" alt="news' thumbnail" style="width: 100%;
                                                                                        height: 100%;
                                                                                        object-fit: cover;" />
            </div>
            <div style="padding: 1.3rem 0 0;">
                <h3 style="margin: 0; 
                           font-size: 1.2em;
                           line-height: 1.2;">
                    <a href="<?= $host; ?>/news/articles/<?= $id; ?>" style="color: inherit; text-decoration: none;"><?= $title; ?></a>
                </h3>
                <p style="margin: 1rem 0;"><?= $body; ?></p>
                <div style="padding: 1em 0;">
                    <a href="<?= $host; ?>/news/articles/<?= $id; ?>" style="color: white;
                                                                                    font-weight: 600;
                                                                                    border-radius: 2rem;
                                                                                    text-decoration: none;
                                                                                    padding: 0.6rem 2rem;
                                                                                    background-color: steelblue;
                                                                                    transition: 0.3s;">Read more</a>
                    <span style="color: gray;
                                font-size: 0.8rem;
                                float: right;"><?= $timeDiff; ?></span>
                </div>
            </div>
        </article>
        <?php require_once dirname(__DIR__, 2) . '/includes/newsletter_footer.php'; ?>
    </div>
</body>

</html>