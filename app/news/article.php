<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\CMS\News\CategoryManager;
use Application\CMS\News\NewsManager;
use Application\MiddleWare\ServerRequest;

$incoming = (new ServerRequest())->initialize();
$NewsManager = new NewsManager(Connection::Instance());
$CategoryManager = new CategoryManager(Connection::Instance());

// Retrieve the article
$payload = $incoming->getParsedBody();
$id = (int) $payload['id'];
if (!$NewsManager->exists($id)) {
	header('Location: /news/');
}
$article = $NewsManager->get($id);
$title = $article->getTitle();
$publication = array(
	'date' => date('l, j F Y', strtotime($article->getPublicationDate())),
	'time' => date('g:i a', strtotime($article->getPublicationDate()))
);
$body = $article->getBody();
$thumbnail = $article->getThumbnail();
// Retrieve info about article's category
$categories = $CategoryManager->getCategory($article);
// Article's author
$author = MemberManager::Instance()->getMember($article->getAuthorID());
$authorName = $author->getName();
$authorPicture = $author->getPicture();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA News: <?= $title; ?></title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="news_article">
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
			<div class="cs-grid">
				<div id="article-wrapper">
					<div class="article-content-header">
						<h2><?= $title; ?></h2>
						<h6><span><img src="<?= $authorPicture; ?>" alt="article_author" /><?= $authorName; ?></span><span><i class="fas fa-calendar-day"></i><?= $publication['date']; ?></span><span><i class="fas fa-clock"></i><?= $publication['time']; ?></span>
							<div><?php foreach ($categories as $category) : ?><span class="label"><?= $category->getName(); ?></span><?php endforeach; ?></div>
						</h6>
					</div>
					<div class="article-content">
						<div class="article-thumb"><img src="<?= $thumbnail; ?>" alt="news thumbnail"></div>
						<?= $body; ?>
					</div>
					<div class="share-div">
						<div><span>Share this article</span></div>
						<div>
							<a href="#" class="btn-facebook"><span class="fab fa-facebook-f"></span></a>
							<a href="#" class="btn-twitter"><span class="fab fa-twitter"></span></a>
						</div>
					</div>
				</div>
				<aside id="news-article-aside">
					<section>
						<form id="search_articles" action="/search" method="get">
							<input type="text" class="form-control" name="search" id="search_article" placeholder="Search articles">
							<button type="submit"><i class="fas fa-search"></i></button>
						</form>
					</section>
					<section>
						<h4>Categories</h4>
						<ul>
							<?php foreach ($CategoryManager->list() as $category) : ?>
								<li><a href="#"><?= $category->getName(); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</section>
				</aside>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>