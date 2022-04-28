<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\DateTime\Constants;
use Application\Database\Connection;
use Application\CMS\Events\EventManager;
use Application\MiddleWare\ServerRequest;

$incoming_request = (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());

$events = $EventManager->list();
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="CADEXSA Upcomming events">
	<meta name="author" content="Yvan Tchuente">
	<title>Upcoming Events - CADEXSA</title>
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
			<?php if (!empty($events)) : ?>
				<!-- Events search start -->
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
							<span class="current">category</span>
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
				<!-- Events search end -->
			<?php endif; ?>
			<?php
			if (!empty($events)) {
			?>
				<div class="events_list">
					<?php
					foreach ($events as $event) {
						$eventID = $event->getID();
						$preview = $EventManager->preview((int)$eventID);
					?>
						<div class="single-event">
							<div>
								<div class="event_thumbnail">
									<img src="<?= $preview['thumbnail']; ?>" alt="event_thumbnail" />
								</div>
							</div>
							<div>
								<div class="display-table">
									<div class="table-cell">
										<div class="countdown" data-date="<?= $preview['deadline']; ?>">
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
										<div class="event-text">
											<h2><?= $preview['title']; ?></h2>
											<h6><i class="fas fa-calendar-day"></i> <?= date("l j F", strtotime($preview['deadline'])); ?> <i class="fas fa-clock"></i> <?= date("g a", strtotime($preview['deadline'])); ?></h6>
											<?= $preview['body']; ?>
											<a href="/events/<?= $eventID; ?>" class="event-link">Join us</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			<?php } else { ?>
				<div style="width: 80%; margin: auto; text-align: center; padding: 4rem 0;">There is currently no upcoming event scheduled.</div>
			<?php } ?>
			<?php if (!empty($events)) : ?>
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
			<?php endif; ?>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>