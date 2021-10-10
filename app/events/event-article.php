<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA Event: We are having a meeting at the president's home</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="event-article">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<!-- Page Content -->
	<div class="page-content">
		<div class="page-header">
			<div class="wrap"></div>
			<div class="ws-container">
				<h1>Upcoming Events</h1>
			</div>
		</div>
		<div class="ws-container">
			<div class="cs-grid">
				<div id="event-wrap">
					<div class="thumbnail">
						<div class="timer" data-date="2022-01-27 21:00:00">
							<div class="timer_day">
								<span>Days</span>
								<span>00</span>
							</div>
							<div class="timer_hour">
								<span>Hours</span>
								<span>00</span>
							</div>
							<div class="timer_minute">
								<span>Min</span>
								<span>00</span>
							</div>
							<div class="timer_second">
								<span>Sec</span>
								<span>00</span>
							</div>
						</div>
						<img src="/static/images/gallery/students.jpg" alt="event-thumbnail" />
						<div class="title">
							<h2>We are having a meeting at the president's home</h2>
							<p class="location">
								<span>Colonel Ndi Roundabout, Pk 10 Douala, Cameroon on Saturday 31 July 2021 at 3pm</span>
							</p>
						</div>
					</div>
					<div class="desc">
						<h3>Event Description</h3>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam quis diam erat. Duvelit lecspoe a blandit sit amet, tempor at lorem. Donec ultricies, lorem sed ultrices interdum, leo luctfiiius sem, vel vulputate diam ipsum sed lorem. Donec tempor arcu nisl, et molestie massa hhisque ut. Nunc at rutrum leo. Mauris metus mauris, tridd.</p>
						<p>Lorem ipsum condimentum ligula. Fusce fringilla magna non sapien dictum, eget faucibus dui maximus. Donec fringilla vel mi consequat tempor. Proin sed ultrices erat. Praesent vdd warius ultricemassa at faucibus. Aenean dignissim, orci sed faucibus pharetra, dui mi dir ssim tortor, sit amet ntum mi ligula sit amet augue. Pellentesqs placerat.</p>
					</div>
				</div>
				<aside id="aside_wrap">
					<section id="schedule">
						<h3>Event Schedule</h3>
						<div>
							<h5>Opening Prayer</h5>
						</div>
						<div>
							<h5>Recall of last meeting</h5>
						</div>
						<div class="accordion">
							<h5>Debate over matters <i class="fas fa-angle-down"></i></h5>
							<ul class="content">
								<li>Review of CADEXSA's status</li>
								<li>Detailed explanation about he mission and vision statement of the association</li>
								<li>Meeting with the proprietor</li>
								<li>Discussion on how to handle new and old graduates</li>
								<li>Discussion on hoow to boost association's coffers</li>
								<li>Presentation of the advancement of the website and Facebook page</li>
								<li>A word from the Guest</li>
							</ul>
						</div>
						<div>
							<h5>Closing remarks</h5>
						</div>
					</section>
				</aside>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
	<script>
		let accordions = document.querySelectorAll(".accordion");
		setTimeout(() => {
			for (const accordion of accordions)
				accordion.click();
			console.log("done");
		}, 1000);
	</script>
</body>

</html>