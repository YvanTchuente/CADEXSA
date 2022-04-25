<?php require_once dirname(__DIR__) . '/config/index.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Latest news">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA - Latest News</title>
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
			<!-- News article search start -->
			<div class="filter-area">
				<form id="news-filter">
					<div class="nice-select" id="nice-select-1">
						<span class="current" onclick="openSelect(event,'nice-select-1')">month</span>
						<ul class="dropdown">
							<li class="selected">month</li>
							<li>January</li>
							<li>February</li>
							<li>March</li>
							<li>April</li>
							<li>May</li>
							<li>June</li>
							<li>July</li>
							<li>August</li>
							<li>September</li>
							<li>October</li>
							<li>November</li>
							<li>December</li>
						</ul>
						<select id="select-month" name="month" required>
							<option value="" selected>Month</option>
							<option value="January">January</option>
							<option value="February">February</option>
							<option value="March">March</option>
							<option value="April">April</option>
							<option value="May">May</option>
							<option value="June">June</option>
							<option value="July">July</option>
							<option value="August">August</option>
							<option value="September">September</option>
							<option value="October">October</option>
							<option value="November">November</option>
							<option value="December">December</option>
						</select>
					</div>
					<div class="nice-select" id="nice-select-2">
						<span class="current" onclick="openSelect(event,'nice-select-2')">year</span>
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
						<span class="current" onclick="openSelect(event,'nice-select-3')">category</span>
						<ul class="dropdown">
							<li class="selected">Category</li>
							<li>News from ex-students</li>
							<li>News from the Alma Mater</li>
						</ul>
						<select id="select-type" name="type" required>
							<option value="" selected>Category</option>
							<option value="students">News from ex-students</option>
							<option value="school">News from the Alma Mater</option>
						</select>
					</div>
					<button type="submit">filter</button>
				</form>
			</div>
			<!-- News article search end -->
			<div class="news-grid-container">
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">Recently we launched a massive project for new</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 1 day ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img3.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">Several ex-students became ministers of government</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 5 hours ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img2.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">The party organized last meeting went all well</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 5 months ago</span></div>
						</div>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img4.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">Lorem ipsm dolor sitg amet, csetur adipicing elit</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 2 weeks ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img11.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">Lorem csetur adipicing ipsm dolor sitg amet elit</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 2 weeks ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="/static/images/gallery/img6.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="/news/news-article.php">Csetur adipicing elit, Lorem ipsm dolor sitg amet</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="/news/news-article.php" class="news-link">More</a><span><i class="fas fa-clock"></i> 2 weeks ago</span></div>
						</div>
					</article>
				</div>
			</div>
			<div class="pagination-area">
				<ul class="pagination">
					<li class="page-item disabled"><a href="#" class="page-link"><span class="fas fa-angle-double-left"></span></a></li>
					<li class="page-item active"><a href="#" class="page-link">1</a></li>
					<li class="page-item"><a href="#" class="page-link">2</a></li>
					<li class="page-item"><a href="#" class="page-link">3</a></li>
					<li class="page-item"><a href="#" class="page-link">4</a></li>
					<li class="page-item"><a href="#" class="page-link">5</a></li>
					<li class="page-item"><a href="#" class="page-link"><span class="fas fa-angle-double-right"></span></a></li>
				</ul>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>