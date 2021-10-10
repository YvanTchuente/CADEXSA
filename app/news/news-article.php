<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA - News Article</title>
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
				<div id="article-wrap">
					<div class="article-content-header">
						<h2>Several ex-students became ministers of the cameroonian government</h2>
						<h6><span>by</span><span><img src="/static/images/graphics/profile-placeholder.png" alt="article_author" />Webmaster</span><span><i class="fas fa-clock"></i>Sep 5, 2021</span></h6>
					</div>
					<div class="article-content">
						<div class="article-thumb"><img src="/static/images/gallery/group.jpg" alt="news thumbnail"></div>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam quis diam erat. Duvelit lecspoe a blandit sit amet, tempor at lorem. Donec ultricies, lorem sed ultrices interdum, leo luctfiiius sem, vel vulputate diam ipsum sed lorem. Donec tempor arcu nisl, et molestie massa hhisque ut. Nunc at rutrum leo. Mauris metus mauris, tridd.</p>
						<p>Mauris tempus erat laoreet turpis lobortis, eu tincidunt erat fermentum. Aliquam nonh edunt urna. Integer tincidunt nec nisl vitae ullamcorper. Proin sed ultrices erat. Praesent vdd warius ultricemassa at faucibus. Aenean dignissim, orci sed faucibus pharetra, dui mi dir ssim tortor, sit amet ntum mi ligula sit amet augue. Pellentesqs placerat.</p>
						<p>Lorem ipsum condimentum ligula. Fusce fringilla magna non sapien dictum, eget faucibus dui maximus. Donec fringilla vel mi consequat tempor. Proin sed ultrices erat. Praesent vdd warius ultricemassa at faucibus. Aenean dignissim, orci sed faucibus pharetra, dui mi dir ssim tortor, sit amet ntum mi ligula sit amet augue. Pellentesqs placerat.</p>
					</div>
					<div class="share-div">
						<div><span>Share this article</span></div>
						<div>
							<a href="#" class="btn-facebook"><span class="fab fa-facebook-f"></span></a>
							<a href="#" class="btn-twitter"><span class="fab fa-twitter"></span></a>
						</div>
					</div>
				</div>
				<aside id="aside_wrap">
					<section class="aside_box">
						<h4>Search</h4>
						<form id="search_articles" action="/search" method="get">
							<input type="text" class="form-control" name="search" id="search_article" placeholder="Search articles">
							<button type="submit"><i class="fas fa-search"></i></button>
						</form>
					</section>
					<section class="aside_box">
						<h4>Categories</h4>
						<ul class="list-unstyled">
							<li><a href="#">Alumni</a></li>
							<li><a href="#">Events</a></li>
							<li><a href="#">Members</a></li>
							<li><a href="#">School</a></li>
							<li><a href="#">Current students</a></li>
						</ul>
					</section>
				</aside>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>