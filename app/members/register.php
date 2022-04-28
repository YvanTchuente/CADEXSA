<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\{
	Request,
	Constants,
	TextStream,
	ServerRequest
};
use PHPMailer\PHPMailer\PHPMailer;
use Application\Membership\Validator;
use Application\Membership\MemberManager;

if (MemberManager::Instance()->is_logged_in()) {
	header("Location: /members/login");
}

$incoming_request =  (new ServerRequest())->initialize();

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
	$outgoing_request =  new Request();
	$body = new TextStream(json_encode($incoming_request->getParsedBody()));
	$outgoing_request =  $outgoing_request->withBody($body);
	$response = Validator::validateRegistration($outgoing_request);
	if (is_array($response)) {
		$mailer = new PHPMailer(true);
		$response = MemberManager::Instance()->signup($response, $mailer);
		if ($response === true) {
			header('Location: login');
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Sign up to CADEXSA and express your love for our alma-mater">
	<title>Sign Up - CADEXSA</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
	<script type="module" src="/static/dist/js/pages/signup.js"></script>
</head>

<body id="register-body">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<div class="page-content">
		<div class="ws-container">
			<div id="register-grid">
				<div id="form-thumbnail">
					<div>
						<h2>Welcome to our community</h2>
						<p>Show affection for your alma-mater and help us make it grow. Please fill out the form</p>
					</div>
				</div>
				<form action="register" method="post" id="register-form" class="sign_up">
					<div class="form-grouping form-header">
						<h1>Sign Up</h1>
					</div>
					<?php if (isset($response)) : ?><p class="error"><?php echo $response; ?></p><?php endif ?>
					<fieldset>
						<legend>Personal Information</legend>
						<label for="first-name">Name</label>
						<div class="form-grouping form-group">
							<div>
								<input type="text" id="first-name" name="firstname" placeholder="First Name" required />
							</div>
							<div>
								<input type="text" id="last-name" name="lastname" placeholder="Last Name" required />
							</div>
						</div>
						<div class="form-grouping">
							<label for="username">Username</label>
							<input type="text" id="username" name="username" required />
						</div>
						<div class="form-grouping">
							<label for="email">Email</label>
							<input type="email" id="email" name="email" required />
						</div>
					</fieldset>
					<fieldset>
						<legend>Address</legend>
						<div class="form-grouping">
							<label for="country">Country of residence</label>
							<input type="text" id="country" name="country" placeholder="Cameroon" required />
						</div>
						<div class="form-grouping">
							<label for="city">City of residence</label>
							<input type="text" id="city" name="city" required />
						</div>
					</fieldset>
					<fieldset>
						<legend>Phone</legend>
						<div class="form-grouping">
							<label for="main_contact">Primary phone number</label>
							<input type="number" class="phone_number" ID="main_contact" name="main_contact" required />
						</div>
						<div class="form-grouping">
							<label for="secondary_contact">Secondary phone number</label>
							<input type="number" class="phone_number" id="secondary_contact" name="secondary_contact" />
						</div>
					</fieldset>
					<fieldset>
						<legend>High School Information</legend>
						<div class="form-grouping">
							<div class="form-group">
								<div>
									<div class="nice-select" id="nice-select-1">
										<span class="current">Batch year</span>
										<ul class="dropdown">
											<li class="selected">Batch year</li>
											<li>2022</li>
											<li>2021</li>
											<li>2020</li>
											<li>2019</li>
											<li>2018</li>
											<li>2017</li>
											<li>2016</li>
										</ul>
										<select id="select-year" name="batch_year" required>
											<option value="" selected>Batch Year</option>
											<option value="2022">2022</option>
											<option value="2021">2021</option>
											<option value="2020">2020</option>
											<option value="2019">2019</option>
											<option value="2018">2018</option>
											<option value="2017">2017</option>
											<option value="2016">2016</option>
										</select>
									</div>
								</div>
								<div>
									<div class="nice-select" id="nice-select-2">
										<span class="current">Orientation</span>
										<ul class="dropdown">
											<li class="selected">Orientation</li>
											<li>Science</li>
											<li>Arts</li>
										</ul>
										<select id="select-orientation" name="orientation" required>
											<option value="" selected>Orientation</option>
											<option value="Science">Science</option>
											<option value="Arts">Arts</option>
										</select>
									</div>
								</div>
							</div>
						</div>
					</fieldset>
					<fieldset>
						<legend>Passwords</legend>
						<div class="form-grouping">
							<label for="password">Password</label>
							<div style="position: relative;"><input type="password" id="password" name="password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
						</div>
						<div class="form-grouping">
							<label for="confirm-password">Confirm Password</label>
							<div style="position: relative;"><input type="password" id="confirm-password" name="confirm-password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
						</div>
					</fieldset>
					<div class="form-grouping">
						<label for="aboutme">Let other ex-students know what you are up to</label>
						<textarea id="aboutme" name="aboutme" required /></textarea>
					</div>
					<button type="submit" class="form-btn">Create account</button>
					<p class="form-footer">Already have an account? <a href="login">Sign in</a></p>
				</form>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>