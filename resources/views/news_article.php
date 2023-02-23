<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title><?= $title ?> - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>News</h1>
    </header>
    <div class="ws-container">
        <div id="news-article-container">
            <section>
                <header>
                    <h1><?= $title; ?></h1>
                    <div>
                        <span><img src="<?= $authorPicture; ?>" alt="article_author" /><a href="/exstudents/<?= strtolower($authorUserName); ?>"><?= ucwords(strtolower($authorName)); ?></a></span>
                        <span><i class="fas fa-calendar-day"></i><?= $publication['date']; ?></span>
                        <span><i class="fas fa-clock"></i><?= $publication['time']; ?></span>
                    </div>
                </header>
                <figure id="news-article-image"><img src="<?= $image; ?>"></figure>
                <?= $body; ?>
                <div id="news-article-tags">
                    <?php foreach ($tags as $tag) : ?>
                        <span class="tag"><?= $tag; ?></span>
                    <?php endforeach; ?>
                </div>
                <div id="news-article-sharing-widget">
                    <span>Share this article</span>
                    <div>
                        <a href="#" class="btn-facebook"><span class="fab fa-facebook-f"></span></a>
                        <a href="#" class="btn-twitter"><span class="fab fa-twitter"></span></a>
                    </div>
                </div>
            </section>
            <aside>
                <section id="tags">
                    <h3>Tags</h3>
                    <ul>
                        <?php foreach ($all_tags as $tag) : ?>
                            <li><a href="#"><?= $tag; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </section>
                <?php if (!empty($recentNews)) : ?>
                    <section id="articles">
                        <h3>Recent articles</h3>
                        <ul>
                            <?php
                            foreach ($recentNews as $newsArticle) :
                                $id = $newsArticle->getId();
                                $image = $newsArticle->getImage();
                                $title = $newsArticle->getTitle();
                                $link = "/news/" . urlencode($title);
                            ?>
                                <li>
                                    <img src="<?= $image; ?>">
                                    <a href="<?= $link; ?>"><?= $title; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                <?php endif; ?>
            </aside>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>