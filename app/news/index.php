<?php

define('ITEMS_PER_PAGE', 6);

require_once dirname(__DIR__) . '/config/index.php';

use Application\CMS\Paginator;
use Application\DateTime\Constants;
use Application\CMS\News\TagManager;
use Application\Database\Connection;
use Application\CMS\News\NewsManager;
use Application\DateTime\TimeDuration;
use Application\MiddleWare\ServerRequest;

$incoming_request = (new ServerRequest())->initialize();
$NewsManager = new NewsManager(Connection::Instance());
$TagManager = new TagManager(Connection::Instance());

// Pagination
$page = (int) ($_GET['page'] ?? 1);
$paginator = new Paginator($NewsManager, ITEMS_PER_PAGE);
$total_pages = $paginator->getTotalNumberOfPages();

try {
	$articles = $paginator->paginate($page);
} catch (\Throwable $e) {
	$url = $incoming_request->getUri()->getPath();
	header('Location: ' . $url);
}

// Define time difference object
$timeDuration = new TimeDuration();

// Retrieve tags from the database
foreach ($TagManager->list() as $tagObj) {
	$tags[] = $tagObj->getName();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Latest news">
	<meta name="author" content="Yvan Tchuente">
	<title>Latest News - CADEXSA</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="news_dir">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<!-- Page Content -->
	<div class="page-content">
		<div class="page-header">
			<div class="ws-container">
				<h1>Recent News</h1>
			</div>
		</div>
		<div class="ws-container">
			<?php if (!empty($articles)) : ?>
				<!-- News article search start -->
				<div class="filter-area">
					<form id="news-filter">
						<div class="nice-select" id="nice-select-1">
							<span class="current">month</span>
							<ul class="dropdown">
								<li class="selected">month</li>
								<?php
								foreach (Constants::MONTH_NAMES as $month) {
								?>
									<li><?= $month; ?></li>
								<?php
								}
								?>
							</ul>
							<select id="select-month" name="month" required>
								<option value="" selected>Month</option>
								<?php
								foreach (Constants::MONTH_NAMES as $month) {
								?>
									<option value="<?= $month; ?>"><?= $month; ?></option>
								<?php
								}
								?>
							</select>
						</div>
						<div class="nice-select" id="nice-select-2">
							<span class="current">year</span>
							<ul class="dropdown">
								<li class="selected">year</li>
								<li>2021</li>
								<li>2022</li>
							</ul>
							<select id="select-year" name="year" required>
								<option value="" selected>Year</option>
								<option value="2022">2022</option>
								<option value="2021">2021</option>
							</select>
						</div>
						<div class="nice-select" id="nice-select-3">
							<span class="current">tag</span>
							<ul class="dropdown">
								<li class="selected">Tag</li>
								<?php
								foreach ($tags as $tag) {
								?>
									<li><?= $tag; ?></li>
								<?php
								}
								?>
							</ul>
							<select id="select-type" name="type" required>
								<option value="" selected>Tag</option>
								<?php
								foreach ($tags as $tag) {
								?>
									<option value="<?= $tag; ?>"><?= $tag; ?></option>
								<?php
								}
								?>
							</select>
						</div>
						<button type="submit">filter</button>
					</form>
				</div>
				<!-- News article search end -->
			<?php endif; ?>
			<?php if (!empty($articles)) { ?>
				<div class="news-grid-container">
					<?php
					foreach ($articles as $article) {
						$articleID = $article->getID();
						$preview = $NewsManager->preview((int)$articleID, $timeDuration);
						$preview['body'] = preg_replace('/<\/?(strong|b)>/', '', $preview['body']);
					?>
						<div>
							<article class="news-item">
								<div class="news-thumb"><img src="<?= $preview['thumbnail']; ?>" alt="news' thumbnail"></div>
								<div class="news-content">
									<h5><a href="/news/articles/<?= $articleID; ?>"><?= $preview['title']; ?></a></h5>
									<?= $preview['body']; ?>
									<div class="news-item-footer"><a href="/news/articles/<?= $articleID; ?>" class="news-link">More</a><span><i class="fas fa-clock"></i> <?= $preview['timeDiff']; ?></span></div>
								</div>
							</article>
						</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div style="width: 80%; margin: auto; text-align: center; padding: 4rem 0;">There is no news articles, we invite you to come back later.</div>
			<?php } ?>
			<?php if ($total_pages > 1) : ?>
				<div class="pagination-area">
					<ul class="pagination">
						<li class="page-item <?php if ($page == 1) echo "disabled"; ?>"><a href="?page=<?= $page - 1; ?>" class="page-link"><span class="fas fa-angle-double-left"></span></a></li>
						<?php
						for ($i = 1; $i <= $total_pages; $i++) :
						?>
							<li class="page-item <?php if ($page == $i) echo "active"; ?>"><a href="?page=<?= $i; ?>" class="page-link"><?= $i; ?></a></li>
						<?php endfor; ?>
						<li class="page-item <?php if (($page + 1) > $total_pages) echo "disabled"; ?>"><a href="?page=<?= $page + 1; ?>" class="page-link"><span class="fas fa-angle-double-right"></span></a></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>