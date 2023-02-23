<!DOCTYPE html>
<html class="cms" lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Manage news article - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <?php require views_path("/commons/cms_page_header.php") ?>
    <div class="ws-container">
        <div class="list">
            <div class="header">
                <h2>News Manager</h2>
            </div>
            <?php if (isset($news)) : ?>
                <div class="news-container">
                    <?php
                    foreach ($news as $newsArticle) :
                        $link = "/news/" . urlencode(strtolower($newsArticle->getTitle()));
                    ?>
                        <article class="news_article">
                            <div class="image"><img src="<?= $newsArticle->getImage(); ?>" alt="news' image"></div>
                            <div class="body">
                                <h3><a href=<?= $link; ?>><?= $newsArticle->getTitle(); ?></a></h3>
                                <div class="footer">
                                    <a href="/cms/news/edit?id=<?= $newsArticle->getId(); ?>" class="link">Update</a><a href="/cms/delete?type=news&id=<?= $newsArticle->getId(); ?>" class="link">Delete</a><span><i class="fas fa-clock"></i> <?= $newsArticle->getTimeSincePublication(); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <div style="width: 80%; margin: auto; text-align: center; padding: 3rem 0;">There are no news articles.</div>
            <?php endif; ?>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>