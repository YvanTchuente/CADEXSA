<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Upcomming events">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA - Upcoming Events</title>
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
				<h1>Upcoming Events</h1>
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
							<li>Meetup</li>
							<li>Meeting</li>
						</ul>
						<select id="select-type" name="type" required>
							<option value="" selected>Category</option>
							<option value="meetup">Meetup</option>
							<option value="meeting">Meeting</option>
						</select>
					</div>
					<button type="submit">filter</button>
				</form>
			</div>
			<!-- News article search end -->
			<div class="events_list">
				<div class="single-event">
					<div>
						<div class="event_thumbnail">
							<img src="/static/images/gallery/students.jpg" alt="event_thumbnail" />
						</div>
					</div>
					<div>
						<div class="display-table">
							<div class="table-cell">
								<div class="timer" data-date="2022-07-31 03:00:00">
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
									<div>
										<span>Remaining time</span>
									</div>
								</div>
								<div class="event-text">
									<h2>We are having a meeting at the president's home</h2>
									<h6><i class="fas fa-calendar-day"></i> Saturday 31 July <i class="fas fa-clock"></i> 3pm</h6>
									<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
									<a href="/events/event-article.php" class="event-link">Join us</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="single-event">
					<div>
						<div class="event_thumbnail">
							<img src="/static/images/gallery/group.jpg" alt="event_thumbnail" />
						</div>
					</div>
					<div>
						<div class="display-table">
							<div class="table-cell">
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
									<div>
										<span>Remaining time</span>
									</div>
								</div>
								<div class="event-text">
									<h2>We are organising an End of Year party in december</h2>
									<h6><i class="fas fa-calendar-day"></i> Monday 27 December <i class="fas fa-clock"></i> 9pm</h6>
									<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
									<a href="/events/event-article.php" class="event-link">Join us</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="single-event">
					<div>
						<div class="event_thumbnail">
							<img src="/static/images/gallery/img2.jpg" alt="event_thumbnail" />
						</div>
					</div>
					<div>
						<div class="display-table">
							<div class="table-cell">
								<div class="timer" data-date="2022-09-25 04:30:00">
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
									<div>
										<span>Remaining time</span>
									</div>
								</div>
								<div class="event-text">
									<h2>We are planning to become an non-profit organization</h2>
									<h6><i class="fas fa-calendar-day"></i> Thursday 25 October<i class="fas fa-clock"></i> 4:30pm</h6>
									<p>Lorem ipsm dolor sitg amet, csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam, quis csetur adipicing elit, sed do eiusmod tempor dncint ut labore et dolore magna alis enim ad minim veniam.</p>
									<a href="/events/event-article.php" class="event-link">Join us</a>
								</div>
							</div>
						</div>
					</div>
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