<?php require_once __DIR__ . '/config/index.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="On top of providing your affection to our alma-mater (Lacadenelle Anglo-saxon high school), which is, for many of us, the main reason, your membership consolidates...">
	<meta name="author" content="Yvan Tchuente">
	<title>Home Page - CADEXSA</title>
	<?php require_once __DIR__ . "/includes/head_tag_includes.php"; ?>
</head>

<body>
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once __DIR__ . "/includes/header.php"; ?>
	<!-- Headline and Sub-headline statements -->
	<div id="headline-wrapper">
		<div id="head-carousel" class="carousel">
			<div class="headline carousel-item" id="headline1">
				<div class="container_box">
					<h1>Contribute to the growth of the network and its expansion</h1>
					<p>On top of providing your affection to our alma-mater (Lacadenelle Anglo-saxon high school), which is, for many of us, the main reason, your membership consolidates our unity and friendship and affords many advantages like career opportunities</p>
					<!-- Main Call to action -->
					<a href="/members/register" class="button">Join our network</a>
				</div>
			</div>
			<div class="headline carousel-item" id="headline2">
				<div class="container_box">
					<h1>We are committed to ensure the unity of our members</h1>
					<p>CADEXSA is an initiative of ex-student from la Cadenelle Anglo Saxon college who agreed and took a decision of creating an association which shall regroup all graduate of the school so as to maintain our friendship</p>
					<!-- Main Call to action -->
					<a href="/about_us/" class="button">Read About Us</a>
				</div>
			</div>
		</div>
		<div class="carousel-nav">
			<button class="prev" data-ride="prev"><i class="fas fa-arrow-left"></i></button>
			<button class="next" data-ride="next"><i class="fas fa-arrow-right"></i></button>
		</div>
	</div>
	<!-- Page Content -->
	<div id="sub-head" class="ws-container">
		<div class="subhead-grid-container">
			<div>
				<h1>Our Purpose of Ex-Students Association</h1>
				<p id="sh_p">The purpose of our ex-students association is to re-unite all the ex-students from all the various batches that had graduated from our Alma-Mater, so we support each others through the upcoming challenges of our professional careers and life.</p>
			</div>
			<div>
				<img src="static/images/graphics/mission.png" alt="img" />
				<h5>Mission</h5>
				<p>At multiple occasions, we organise and pay visits to our alma-mater during which we share our experience with the current students and advice them on how to succeed their academic year.</p>
			</div>
			<div>
				<img src="node_modules/fontawesome/svgs/solid/eye.svg" alt="img" />
				<h5>Vision</h5>
				<p>CADEXSA aims primarily to bring together and unite all the graduates of our alma-mater so to initiate and preserve friendship and unity for as long as possible.</p>
			</div>
			<div>
				<img src="static/images/graphics/dartboard.png" alt="img" />
				<h5>Objectives</h5>
				<p>The objectives of CADEXSA in a long-run is to create job opportunities for its member and provide support to our alma-mater in all the aspects that it entails.</p>
			</div>
		</div>
	</div>
	<!-- Features section -->
	<div id="features" class="content_block">
		<div class="ws-container">
			<h1>Our Occupations</h1>
			<div class="grid-container">
				<div>
					<img src="static/images/graphics/scholars.png" alt="image" />
					<h5 class="text-center">Promote Excellence</h5>
					<p class="text-center">We encourage current students and members to achieve excellence, being their best in their studies, professional careers and personal lives to make our network reach greater heights in the foreseeable future.</p>
				</div>
				<div>
					<img src="static/images/graphics/student.png" alt="image" />
					<h5 class="text-center">Help Current Students</h5>
					<p class="text-center">Our aim is to provide assistance to students of our alma-mater. Every year, we organize mentorship sessions where volunteered ex-students are assigned a group of students to which they provide with academic pieces of advice, academic support and guidance.</p>
				</div>
				<div>
					<img src="static/images/graphics/school.png" alt="image" />
					<h5 class="text-center">Support our High School</h5>
					<p class="text-center">As being one of our missions, we are committed to provide support to our alma-mater by various means. We yearly provision our alma-mater with school essentials among other things we provide to support.</p>
				</div>
				<div>
					<img src="static/images/graphics/community.png" alt="image" />
					<h5 class="text-center">Building our network</h5>
					<p class="text-center">Networking being an important factor of our unity, we aim at staying in touch with each other to share our experiences in life and knowledge. We do organize several meetings and meetups each years for this purpose.</p>
				</div>
			</div>
		</div>
	</div>
	<!-- Upcomming events slideshow -->
	<div id="events">
		<div class="ws-container">
			<div class="grid-container">
				<div class="event-carousel-wrap">
					<div class="event-title">
						<h2>Upcoming Events</h2>
					</div>
					<div id="event_carousel" class="carousel">
						<div class="carousel-item">
							<div class="event-grid-container">
								<div>
									<div class="display-table">
										<div class="table-cell">
											<div class="event-text">
												<div class="countdown" data-date="2022-01-27 21:00:00">
													<div class="timer" id="day">
														<span>Days</span>
														<span>00</span>
													</div>
													<div class="timer" id="hour">
														<span>Hours</span>
														<span>00</span>
													</div>
													<div class="timer" id="minute">
														<span>Min</span>
														<span>00</span>
													</div>
													<div class="timer" id="second">
														<span>Sec</span>
														<span>00</span>
													</div>
													<div>
														<span>Remaining time</span>
													</div>
												</div>
												<h2>We are organising an End of Year party in december</h2>
												<h6><i class="fas fa-calendar-day"></i> Monday 27 December <i class="fas fa-clock"></i> 9pm</h6>
												<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
												<a href="/events/event-article.php" class="event-link">Join us</a>
											</div>
										</div>
									</div>
								</div>
								<div>
									<div class="event_thumbnail">
										<img src="static/images/gallery/img.jpg" alt="event_thumbnail" />
									</div>
								</div>
							</div>
						</div>
						<div class="carousel-item">
							<div class="event-grid-container">
								<div>
									<div class="display-table">
										<div class="table-cell">
											<div class="event-text">
												<div class="countdown" data-date="2022-09-25 04:30:00">
													<div class="timer" id="day">
														<span>Days</span>
														<span>00</span>
													</div>
													<div class="timer" id="hour">
														<span>Hours</span>
														<span>00</span>
													</div>
													<div class="timer" id="minute">
														<span>Min</span>
														<span>00</span>
													</div>
													<div class="timer" id="second">
														<span>Sec</span>
														<span>00</span>
													</div>
													<div>
														<span>Remaining time</span>
													</div>
												</div>
												<h2>We are having a meeting at the president's home</h2>
												<h6><i class="fas fa-calendar-day"></i> Saturday 31 July <i class="fas fa-clock"></i> 3pm</h6>
												<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
												<a href="/events/event-article.php" class="event-link">Join us</a>
											</div>
										</div>
									</div>
								</div>
								<div>
									<div class="event_thumbnail">
										<img src="static/images/gallery/img5.jpg" alt="event_thumbnail" />
									</div>
								</div>
							</div>
						</div>
						<div class="carousel-item">
							<div class="event-grid-container">
								<div>
									<div class="display-table">
										<div class="table-cell">
											<div class="event-text">
												<div class="countdown" data-date="2022-01-27 21:00:00">
													<div class="timer" id="day">
														<span>Days</span>
														<span>00</span>
													</div>
													<div class="timer" id="hour">
														<span>Hours</span>
														<span>00</span>
													</div>
													<div class="timer" id="minute">
														<span>Min</span>
														<span>00</span>
													</div>
													<div class="timer" id="second">
														<span>Sec</span>
														<span>00</span>
													</div>
													<div>
														<span>Remaining time</span>
													</div>
												</div>
												<h2>We are planning to become an non-profit organization</h2>
												<h6><i class="fas fa-calendar-day"></i> Thursday 25 October<i class="fas fa-clock"></i> 4:30pm</h6>
												<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
												<a href="/events/event-article.php" class="event-link">Join us</a>
											</div>
										</div>
									</div>
								</div>
								<div>
									<div class="event_thumbnail">
										<img src="static/images/gallery/img2.jpg" alt="event_thumbnail" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<!-- Carousel controls -->
					<div class="event_nav carousel-nav">
						<button class="event_btn" data-ride="prev"><i class="fas fa-arrow-left fa-lg"></i></button>
						<button class="event_btn" data-ride="next"><i class="fas fa-arrow-right fa-lg"></i></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- General informations -->
	<div id="info_box">
		<div class="ws-container">
			<div class="grid-container">
				<div>
					<div style="display:inline-block; vertical-align:middle">
						<i class="fas fa-users"></i>
					</div>
					<div style="display:inline-block; vertical-align:middle">
						<span class="emphasis counter" data-target="150">0</span>
						<span>Members</span>
					</div>
				</div>
				<div>
					<div style="display:inline-block; vertical-align:middle">
						<i class="fas fa-images"></i>
					</div>
					<div style="display:inline-block; vertical-align:middle">
						<span class="emphasis counter" data-target="250">0</span>
						<span>Photos</span>
					</div>
				</div>
				<div>
					<div style="display:inline-block; vertical-align:middle">
						<i class="fas fa-calendar-check"></i>
					</div>
					<div style="display:inline-block; vertical-align:middle">
						<span class="emphasis counter" data-target="50">0</span>
						<span>Events</span>
					</div>
				</div>
				<div>
					<div style="display:inline-block; vertical-align:middle">
						<i class="fas fa-award"></i>
					</div>
					<div style="display:inline-block; vertical-align:middle">
						<span class="emphasis counter" data-target="20">0</span>
						<span>Achievements</span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Gallery -->
	<div id="gallery">
		<div><img src="static/images/gallery/img10.jpg" /></div>
		<div><img src="static/images/gallery/img7.jpg" /></div>
		<div><img src="static/images/gallery/img5.jpg" /></div>
		<div><img src="static/images/gallery/img15.jpg" /></div>
		<div><img src="static/images/gallery/img6.jpg" /></div>
		<div><img src="static/images/gallery/img4.jpg" /></div>
		<div><img src="static/images/gallery/img11.jpg" /></div>
		<div><img src="static/images/gallery/img12.jpg" /></div>
	</div>
	<!-- Latest News Sections -->
	<div id="news" class="content_block">
		<div class="ws-container">
			<h1>Recent News</h1>
			<div class="grid-container">
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="static/images/gallery/img.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="#">Recently we launched a massive project for new</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="" class="news-link">More</a><span><i class="fas fa-clock"></i> 1 day ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="static/images/gallery/img6.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="#">Several ex-students became ministers of government</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="" class="news-link">More</a><span><i class="fas fa-clock"></i> 5 hours ago</span></div>
						</div>
					</article>
				</div>
				<div>
					<article class="news-item">
						<div class="news-thumb"><img src="static/images/gallery/img8.jpg" alt="news image"></div>
						<div class="news-content">
							<h5><a href="#">The party organized last meeting went all well</a></h5>
							<p>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
							<div class="news-item-footer"><a href="" class="news-link">More</a><span><i class="fas fa-clock"></i> 5 months ago</span></div>
						</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Testimonials section -->
	<div id="testimonials" class="content_block">
		<div class="ws-container">
			<h1>Some Speech About Us</h1>
			<div id="testimonials_carousel" class="carousel">
				<div class="carousel-item">
					<div class="testimony">
						<p><i class="fas fa-quote-left"></i>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam. De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand</p>
					</div>
					<div class="testimony-author">
						<img src="static/images/graphics/profile-placeholder.png" alt="author">
						<div><span class="testimony-author-name">Hessack Ryan</span><span class="testimony-author-status">Young graduate</span></div>
					</div>
				</div>
				<div class="carousel-item">
					<div class="testimony">
						<p><i class="fas fa-quote-left"></i>De create building thinking about your requirment and latest treand on our marketplace area. De create building thinking about your requirment and latest treand. Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
					</div>
					<div class="testimony-author">
						<img src="static/images/graphics/profile-placeholder.png" alt="author">
						<div><span class="testimony-author-name">Mbake Collins</span><span class="testimony-author-status">Vice-president</span></div>
					</div>
				</div>
				<div class="carousel-item">
					<div class="testimony">
						<p><i class="fas fa-quote-left"></i>quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis nostrud exercitation ullamco. Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
					</div>
					<div class="testimony-author">
						<img src="static/images/graphics/profile-placeholder.png" alt="author">
						<div><span class="testimony-author-name">Kafack Steve</span><span class="testimony-author-status">Member</span></div>
					</div>
				</div>
			</div>
			<div class="carousel-nav">
				<button class="prev" data-ride="prev"><i class="fas fa-arrow-left"></i></button>
				<button class="next" data-ride="next"><i class="fas fa-arrow-right"></i></button>
			</div>
		</div>
	</div>
	<?php require_once __DIR__ . "/includes/footer.php"; ?>
</body>

</html>