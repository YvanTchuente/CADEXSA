<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\{
	Request,
	Constants,
	TextStream,
	ServerRequest
};
use Application\Membership\MemberManager;

if (MemberManager::Instance()->is_logged_in()) {
	if (MemberManager::Instance()->logout()) {
		$response = "You are logged out";
	}
}

$incoming_request =  (new ServerRequest())->initialize();

if ($incoming_request->getMethod() == Constants::METHOD_POST) {
	$outgoing_request =  new Request();
	$body = new TextStream(json_encode($incoming_request->getParsedBody()));
	$outgoing_request =  $outgoing_request->withBody($body);
	$response = MemberManager::Instance()->login($outgoing_request);
	if ($response === true) {
		$member = strtolower($_SESSION['username']);
		if (isset($incoming_request->getParsedBody()['goto'])) {
			$goto = $incoming_request->getParsedBody()['goto'];
		}
		if (isset($goto)) {
			if (isset($_SESSION['login-goto'])) {
				unset($_SESSION['login-goto']);
			}
			header('Location: ' . $goto);
		} else {
			header('Location: profiles/' . $member);
		}
	}
}
if (isset($_SESSION['login-goto'])) {
	$goto = $_SESSION['login-goto'];
	unset($_SESSION['login-goto']);
}
if (!empty($incoming_request->getQueryParams())) {
	$params = $incoming_request->getParsedBody();
	if (isset($params['goto'])) {
		$goto = urldecode($params['goto']);
		$_SESSION['login-goto'] = $goto;
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Sign in to your CADEXSA account">
	<title>Sign In - CADEXSA</title>
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
						<p>To keep connected with us please login with your personal informations and keep showing love for our alma-mater</p>
					</div>
				</div>
				<form action="login" method="post" id="login-form">
					<div class="form-header">
						<h2>Sign in</h2>
					</div>
					<?php if (isset($response)) : ?><p class="error"><?php echo $response; ?></p><?php endif ?>
					<div class="form-grouping">
						<label for="username">Username</label>
						<div>
							<i class="fas fa-user"></i>
							<input type="text" id="username" name="username" placeholder="Type your username" required />
						</div>
					</div>
					<div class="form-grouping">
						<label for="password">Password</label>
						<div>
							<i class="fas fa-lock"></i>
							<input type="password" id="password" name="password" placeholder="Type your password" required />
							<button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button>
						</div>
					</div>
					<?php if (isset($goto)) : ?><input type="hidden" name="goto" value="<?= $goto; ?>"><?php endif; ?>
					<a href="recovery" id="forgot-pass">Forgot Password ?</a>
					<button type="submit" class="form-btn" onclick='() => {
						const pwd = document.getElementById("password"); const type=pwd.getAttribute("type"); if (type=="text") { pwd.setAttribute("type", "password"); } }'>Sign in</button>
					<p class="form-footer">Not yet a member? <a href="register">Sign up</a></p>
				</form>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>