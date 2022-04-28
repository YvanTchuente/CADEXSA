<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\CMS\Events\EventManager;
use Application\MiddleWare\ServerRequest;

$incoming_request = (new ServerRequest())->initialize();
$EventManager = new EventManager(Connection::Instance());

// Retrieve the event details
$payload = $incoming_request->getParsedBody();
$id = (int) $payload['id'];
if (!$EventManager->exists($id)) {
	header('Location: /events/');
}
$event = $EventManager->get($id);
$title = $event->getTitle();
$body = $event->getBody();
$venue = $event->getVenue();
$thumbnail = $event->getThumbnail();
$deadeline_data = $event->getDeadlineDate();
$deadline_timestamp = strtotime($event->getDeadlineDate());
$deadline_date = date("l d F Y", $deadline_timestamp);
$deadline_time = date("g a", $deadline_timestamp);
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA Event: <?= $title; ?></title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
	<?php if (!MemberManager::Instance()->is_logged_in()) : ?><script type="module" src="/static/dist/js/newsletter.js"></script><?php endif; ?>
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
			<div id="event-wrap">
				<div class="countdown" data-date="<?= $deadeline_data; ?>">
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
				</div>
				<div class="event-thumbnail">
					<img src="<?= $thumbnail; ?>" alt="event-thumbnail" />
					<div class="event-metadata">
						<h2><?= $title; ?></h2>
						<p>
							<span><i class="fas fa-calendar-day"></i> <?= $deadline_date; ?></span>
							<span><i class="fas fa-clock"></i> <?= $deadline_time; ?></span>
							<span><i class="fas fa-map-marker-alt"></i> <?= $venue; ?></span>
						</p>
					</div>
				</div>
				<div class="event-desc">
					<h3>Event Description</h3>
					<?= $body; ?>
				</div>
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