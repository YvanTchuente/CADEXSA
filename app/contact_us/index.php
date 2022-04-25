<?php require_once dirname(__DIR__) . '/config/index.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="keywords" content="contact, form, phone">
	<meta name="description" content="We would love to hear from you, send us a message or request and we'll gladly answer back">
	<meta name="author" content="Yvan Tchuente">
	<title>CADEXSA - Contact Us</title>
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
				<p>Are you interested in our ex-students association, its features and events or anything else? send us a message and we would be happy to answer any and all your questions</p>
			</div>
		</div>
		<div class="ws-container">
			<div id="contact-wrapper">
				<div id="contact-form">
					<form action="#" method="POST">
						<h3>Contact us</h3>
						<p>Please fill in the form completely</p>
						<div class="form-group">
							<div><label for="first-name">first name</label><input type="text" class="form-control" id="first-name" name="first-name" required /></div>
							<div><label for="last-name">last name</label><input type="text" class="form-control" id="last-name" name="last-name" required /></div>
						</div>
						<div class="form-grouping"><label for="email">email</label><input type="email" class="form-control" id="email" name="email" required /></div>
						<div class="form-grouping"><label for="phoneNumber">phone number</label><input type="number" class="form-control" id="phoneNumber" name="phoneNumber" /></div>
						<label for="message">message</label><textarea id="message" name="message" class="form-control" placeholder="We're interested in what you have to say"></textarea>
						<button type="submit">Submit</button>
					</form>
				</div>
				<div>
					<div>
						<h3 style="margin-bottom: 0.5em;">Points of Contact</h3>
						<p><b><i class="fas fa-location-arrow" style="padding-right: 10px;"></i> Address</b><br />Pk21, Douala, Cameroon</p>
						<p><b><i class="fas fa-phone-alt" style="padding-right: 10px;"></i>Phone</b><br />(+237) 657384876</p>
						<p><b><i class="fas fa-envelope" style="padding-right: 10px;"></i>Mailbox</b><br />contact@localhost.cm</p>
						<p>
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
				<h2>More than just a website</h2>
				<p>CADEXSA is an association of people who express love for their alma mater and among themselves, join us now and rock along with us through our achievements</p>
				<a href="/members/register.php" class="button">Become a member</a>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>