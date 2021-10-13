<?php

require_once dirname(__DIR__) . '/config/index.php';

use Classes\MiddleWare\{
	Request,
	Constants,
	TextStream,
	ServerRequest
};

if ($member->is_logged_in()) {
	$member->logout();
	$response = "You are logged out";
}

$incoming = new ServerRequest();
$incoming->initialize();
$outgoing = new Request();

if ($incoming->getMethod() == Constants::METHOD_POST) {
	$body = new TextStream(json_encode($incoming->getParsedBody()));
	$response = $member->login($outgoing->withBody($body));
	if ($response === true) {
		header('Location: profile.php?id=' . $_SESSION['id']);
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Sign in to your CADEXSA account">
	<title>CADEXSA - Sign In</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="login-body">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<div class="page-content">
		<div class="ws-container">
			<div id="login-grid">
				<div id="login-thumb">
					<div id="login-thumb-content">
						<h2>Welcome Back</h2>
						<p>To keep connected with us please login with your personal informations and keep showing love for our alma mater</p>
					</div>
				</div>
				<form action="login.php" method="post" id="login-form">
					<div class="form-header">
						<h2>Sign in</h2>
					</div>
					<?php if (isset($response)) : ?><p class="error"><?php echo $response; ?></p><?php endif ?>
					<div class="form-grouping">
						<label for="username">Username</label>
						<div><i class="fas fa-user"></i><input type="text" id="username" name="username" placeholder="Type your username" required /></div>
					</div>
					<div class="form-grouping">
						<label for="password">Password</label>
						<div><i class="fas fa-lock"></i><input type="password" id="password" name="password" placeholder="Type your password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
					</div>
					<a href="recover_password.php" id="forgot-pass">Forgot Password ?</a>
					<button name="login" type="submit" class="form-btn">Sign in</button>
					<p class="form-footer">Not yet a member? <a href="register.php">Sign up</a></p>
				</form>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>