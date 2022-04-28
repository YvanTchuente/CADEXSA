<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\CMS\News\TagManager;
use Application\CMS\News\NewsManager;
use Application\MiddleWare\ServerRequest;

$incoming_request = (new ServerRequest())->initialize();
$NewsManager = new NewsManager(Connection::Instance());
$TagManager = new TagManager(Connection::Instance());

// Retrieve the article
$payload = $incoming_request->getParsedBody();
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
// Retrieve info about article's tag
$tagName = ($TagManager->getTag($article))->getName();
// Article's author
$author = MemberManager::Instance()->getMember($article->getAuthorID());
$authorUserName = $author->getUserName();
$authorName = $author->getName();
$authorPicture = $author->getPicture();

// Fetch 5 most reccent articles
foreach ($NewsManager->list(5) as $article) {
	if ($article->getID() == $id) {
		continue;
	}
	$articles[] = $article;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA News: <?= $title; ?></title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
	<?php if (!MemberManager::Instance()->is_logged_in()) : ?>
		<script type="module" src="/static/dist/js/newsletter.js"></script>
	<?php endif; ?>
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
			<div id="article-wrapper">
				<div id="article-body">
					<div class="article-content-header">
						<h1><?= $title; ?></h1>
						<div>
							<span>By<img src="<?= $authorPicture; ?>" alt="article_author" /><a href="/members/profiles/<?= strtolower($authorUserName); ?>"><?= ucwords(strtolower($authorName)); ?></a></span>
							<span><i class="fas fa-calendar-day"></i><?= $publication['date']; ?></span>
							<span><i class="fas fa-clock"></i><?= $publication['time']; ?></span>
						</div>
						<div><span class="label"><?= $tagName; ?></span></div>
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
				<aside id="article-aside">
					<section id="categories">
						<h4>Article Tags</h4>
						<ul>
							<?php foreach ($TagManager->list() as $tag) : ?>
								<li><a href="#"><?= $tag->getName(); ?></a></li>
							<?php endforeach; ?>
						</ul>
					</section>
					<?php if (!empty($articles)) : ?>
						<section id="articles">
							<h4>Recent articles</h4>
							<ul>
								<?php
								foreach ($articles as $article) :
									$id = $article->getID();
									$thumbnail = $article->getThumbnail();
									$title = $article->getTitle()
								?>
									<li>
										<img src="<?= $thumbnail; ?>">
										<div><a href="/news/articles/<?= $id; ?>"><?= $title; ?></a></div>
									</li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>
				</aside>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
	<?php if (!MemberManager::Instance()->is_logged_in()) : ?>
		<div class="background-wrapper blurred" id="bw1">
			<span class="fas fa-times" id="exit"></span>
			<div class="newsletter-box">
				<img src="/static/images/logo/Logo.png" alt="Logo">
				<h2>Stay in touch with our newsletter</h2>
				<p>Receive emails about our planned events and news of our activities on a monthly basis.</p>
				<div class="form-grouping">
					<div>
						<i class="fas fa-user"></i>
						<input type="text" class="form-control" placeholder="Name">
					</div>
				</div>
				<div class="form-grouping">
					<div>
						<i class="fas fa-envelope"></i>
						<input type="email" class="form-control" placeholder="E-mail" />
					</div>
				</div>
				<button>Subscribe</button>
			</div>
		</div>
	<?php endif; ?>
</body>

</html>