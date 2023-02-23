<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>News Articles - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/nice_selects.js"></script>
</head>

<body id="news">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header">
        <h1>News</h1>
    </header>
    <div class="ws-container">
        <?php if (isset($news)) : ?>
            <!-- News article search start -->
            <div class="filters-container">
                <form id="filter" action="/news/filter">
                    <div class="nice-select" id="nice-select-1">
                        <span class="current">month</span>
                        <ul class="dropdown">
                            <li class="selected">month</li>
                            <?php foreach (cal_info(CAL_GREGORIAN)['months'] as $month) : ?>
                                <li><?= $month; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <select id="select-month" name="month" required>
                            <option value="" selected>Month</option>
                            <?php foreach (cal_info(CAL_GREGORIAN)['months'] as $month) : ?>
                                <option value="<?= strtolower($month); ?>"><?= $month; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="nice-select" id="nice-select-2">
                        <span class="current">year</span>
                        <ul class="dropdown">
                            <li class="selected">year</li>
                            <?php foreach ($years as $year) : ?>
                                <li><?= $year; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <select id="select-year" name="year" required>
                            <option value="" selected>Year</option>
                            <?php foreach ($years as $year) : ?>
                                <option value="<?= $year; ?>"><?= $year; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="nice-select" id="nice-select-3">
                        <span class="current">tag</span>
                        <ul class="dropdown">
                            <li class="selected">Tag</li>
                            <?php foreach ($tags as $tag) : ?>
                                <li><?= $tag; ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <select id="select-tag" name="tag" required>
                            <option value="" selected>Tag</option>
                            <?php foreach ($tags as $tag) : ?>
                                <option value="<?= strtolower($tag); ?>"><?= $tag; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit">filter</button>
                </form>
            </div>
            <!-- News article search end -->
            <div class="news-container">
                <?php
                foreach ($news as $newsArticle) :
                    $link = "/news/" . urlencode(strtolower($newsArticle->getTitle()));
                ?>
                    <article class="news_article">
                        <div class="image"><img src="<?= $newsArticle->getImage(); ?>"></div>
                        <div class="body">
                            <h3><a href="<?= $link; ?>"><?= $newsArticle->getTitle(); ?></a></h3>
                            <p><?= $newsArticle->getBody(true); ?></p>
                            <div class="footer"><a href="<?= $link; ?>" class="link">More</a><span><i class="fas fa-clock"></i> <?= $newsArticle->getTimeSincePublication(); ?></span></div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php if ($pageCount > 1) : ?>
                <div class="pagination-area">
                    <ul class="pagination">
                        <li class="page-item <?php if ($page == 1) echo "disabled"; ?>"><a href="?page=<?= $page - 1; ?>" class="page-link"><span class="fas fa-angle-double-left"></span></a></li>
                        <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                            <li class="page-item <?php if ($page == $i) echo "active"; ?>"><a href="?page=<?= $i; ?>" class="page-link"><?= $i; ?></a></li>
                        <?php endfor; ?>
                        <li class="page-item <?php if (($page + 1) > $total_pages) echo "disabled"; ?>"><a href="?page=<?= $page + 1; ?>" class="page-link"><span class="fas fa-angle-double-right"></span></a></li>
                    </ul>
                </div>
            <?php endif; ?>
        <?php elseif ($msg) : ?>
            <div style="text-align: center; padding: 4rem 0;"><?= $msg; ?></div>
        <?php endif; ?>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>