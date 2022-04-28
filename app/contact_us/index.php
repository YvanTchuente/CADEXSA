<?php 

require_once dirname(__DIR__) . '/config/index.php'; 
require_once dirname(__DIR__) . '/config/mailserver.php';

use Application\Database\Connection;
use Application\MiddleWare\ServerRequest;

$incoming_request =  (new ServerRequest())->initialize();
$payload = $incoming_request->getParsedBody();

if ($payload) {
	$payload_keys = array_keys($payload);
	$query = (Connection::Instance())->getConnection()->query("SELECT timestamp FROM contact_page_messages ORDER BY timestamp DESC LIMIT 1");
	$lastItem_time = $query->fetch()[0];
	$diff = time() - strtotime($lastItem_time);
	if ($diff <= 2) {
		if (in_array('success', $payload_keys)) { $msg = "Your request has successfully been saved"; }
		if (in_array('error', $payload_keys)) { $msg = "A error occurred while processing the request"; }
	} else { 
		header('Location: /contact_us/');
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="contact, form, phone">
	<meta name="description" content="We would love to hear from you, send us a message or request and we'll gladly answer back">
	<meta name="author" content="Yvan Tchuente">
	<title>Contact Us - CADEXSA</title>
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
	<div id="contact_page" class="page-content">
		<div class="page-header" id="contact-header">
			<div class="ws-container">
				<h2>We would love to hear from you</h2>
				<p>Are you interested in our ex-students association, its features and events or anything else, send us a message and we would be happy to answer all your questions</p>
			</div>
		</div>
		<div class="ws-container">
			<div id="contact-wrapper">
				<div id="contact-form">
					<form action="contact.php" method="POST">
						<h3>CONTACT US</h3>
						<p>Please fill out the form completely</p>
						<?php if(isset($msg)): ?><span class="msg"><?= $msg; ?></span><?php endif; ?>
						<div class="form-group">
							<div><label for="first-name">first name</label><input type="text" class="form-control" id="first-name" name="first-name" required /></div>
							<div><label for="last-name">last name</label><input type="text" class="form-control" id="last-name" name="last-name" required /></div>
						</div>
						<div class="form-grouping"><label for="email">Your email</label><input type="email" class="form-control" id="email" name="email" required /></div>
						<div class="form-grouping"><label for="phoneNumber">phone number</label><input type="number" class="form-control" class="phone_number" name="phoneNumber" /></div>
						<label for="message">message</label><textarea id="message" name="message" class="form-control" placeholder="We're interested in what you have to say"></textarea>
						<button type="submit">Submit</button>
					</form>
				</div>
				<div>
					<div>
						<h4 style="margin-bottom: 0.5em;">Points of Contact</h4>
						<p><b><i class="fas fa-phone-alt" style="padding-right: 10px;"></i>Phone</b><br />(+237) 657384876</p>
						<p><b><i class="fas fa-envelope" style="padding-right: 10px;"></i>Mailbox</b><br /><?= MAILSERVER_INFO_ACCOUNT; ?></p>
						<p style="line-height: unset;">
							<b>Follow us</b><br />
							<a href="#" aria-label="Facebook" class="btn-facebook"><span class="fab fa-facebook-f"></span></a>
							<a href="#" aria-label="Twitter" class="btn-twitter"><span class="fab fa-twitter"></span></a>
						</p>
					</div>
				</div>
			</div>
		</div>
		<div id="contact_footer">
			<div class="ws-container">
				<h3>More than just a website</h3>
				<p>CADEXSA is an association of people who express love for their alma-mater and among themselves, join us now and rock along with us through our achievements</p>
				<a href="/members/register" class="button">Become a member</a>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
	<script>
		const msg_elem = document.querySelector('span.msg');
		const parent_elem = msg_elem.parentElement;
		setTimeout(() => parent_elem.removeChild(msg_elem), 10000)
	</script>
</body>

</html>