<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\DateTime\Constants;
use Application\Database\Connection;
use Application\CMS\Gallery\PictureManager;

$PictureManager = new PictureManager(Connection::Instance());
$pictures = $PictureManager->list(12);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Gallery pictures">
	<meta name="author" content="Yvan Tchuente">
	<title>Gallery - CADEXSA</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body>
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
				<h1>Gallery</h1>
			</div>
		</div>
		<div class="ws-container">
			<!-- Gallery article search start -->
			<div class="filter-area">
				<form id="news-filter">
					<div class="nice-select" id="nice-select-1">
						<span class="current" onclick="openSelect(event,'nice-select-1')">month</span>
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
					<button type="submit">filter</button>
				</form>
			</div>
			<!-- Gallery article search end -->
			<div class="gallery-wrapper">
				<?php
				foreach ($pictures as $picture) {
					$pictureID = $picture->getID();
					$location = $picture->getLocation();
					$desc = substr($picture->getDescription(), 0, 50);
					$date = date('d M Y', strtotime($picture->getPublicationDate()));
				?>
					<div class="gallery-item">
						<img src="<?= $location; ?>" alt="" />
						<div class="gallery-hvr-wrap">
							<div class="gallery-hvr-desc">
								<h6><?= $desc; ?></h6>
								<p><?= $date; ?></p>
							</div>
							<a href="pictures/<?= $pictureID; ?>" class="btn-zoom"><img src="/static/images/graphics/zoom-icon.png" /></a>
						</div>
					</div>
				<?php } ?>
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