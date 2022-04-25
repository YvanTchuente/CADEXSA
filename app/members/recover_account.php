<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\MiddleWare\{
	Request,
	ServerRequest
};

if ($member->is_logged_in()) {
	header('Location: profile.php');
}

$incoming = new ServerRequest();
$incoming->initialize();
$outgoing = new Request();
$param = $incoming->getParsedBody();

$step = 1;
if (!empty($param)) {
	if (isset($param['username'])) {
		$username = $param['username'];
		if ($member->check_user_exist($username)) {
			$res = $conn->getConnection()->query("SELECT ID FROM members WHERE username = '" . $username . "'");
			$memberID = $res->fetch(\PDO::FETCH_ASSOC)['ID'];
			$step = 2;
		} else {
			$msg = "Member does not exist";
		}
	}
	if (isset($param['email']) && isset($param['memberID']) && isset($param['username'])) {
		extract($param);
		$key = sha1($username . $memberID . '2Y@#&$');
		$link = "http://localhost/members/recover_account.php?id=" . $memberID . "&key=" . $key;
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$tomail = $email;
			$from = "From: team@cadexsa.org" . "\r\n";
			$subject = "Recover your account";
			$mail_msg = "Click on the link to recover your account\r\n\r\n" . $link;

			if (mail($tomail, $subject, $mail_msg, $from)) {
				$step = 2;
				$msg = "Check your mail for the code sent";
			}
		} else {
			$step = 2;
			$msg = "Invalid email address";
		}
	}
	if (isset($param['id']) && isset($param['key'])) {
		extract($param);
		$username = $member->getInfo($id)['username'];
		if ($key == sha1($username . $id . '2Y@#&$')) {
			$step = 3;
		}
	}
	if (isset($param['password']) && isset($param['id'])) {
		extract($param);
		if (strlen($password) >= 8) {
			$password = password_hash($password, PASSWORD_DEFAULT);
			$stmt = $conn->getConnection()->prepare('UPDATE members SET password = ? WHERE ID = ?');
			if ($stmt->execute([$password, $id])) {
				header('Location: login.php');
			}
		} else {
			$step = 3;
			$msg = "Invalid password";
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Recover your password and sign in back">
	<title>CADEXSA - Account Recovery</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="password-recovery">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<div class="page-content">
		<div class="ws-container">
			<div class="password-recovery-form-wrapper">
				<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post">
					<div class="form-header">
						<h2>Account Recovery</h2>
					</div>
					<?php
					switch ($step):
						case 3:
					?>
							<p>Reset your password</p>
							<?= (isset($msg)) ? "<p class='error'>" . $msg . "</p>" : ''; ?>
							<div class="form-grouping">
								<div style="position: relative;"><i class="fas fa-lock"></i><input type="password" id="password" placeholder="Enter a password" name="password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
							</div>
							<?= (isset($id)) ? '<input type="hidden" name="id" value="' . $id . '">' : ''; ?>
							<button type="submit" class="form-btn">Submit</button>
						<?php
							break;
						case 2:
						?>
							<p>Enter an email to which a code to recover you account will be sent</p>
							<?= (isset($msg)) ? "<p class='error'>" . $msg . "</p>" : ''; ?>
							<div class="form-grouping">
								<div><i class="fas fa-envelope"></i><input type="email" id="email" name="email" placeholder="Enter an email" required /></div>
							</div>
							<?= (isset($memberID)) ? '<input type="hidden" name="memberID" value="' . $memberID . '">' : ''; ?>
							<?= (isset($username)) ? '<input type="hidden" name="username" value="' . $username . '">' : ''; ?>
							<button type="submit" class="form-btn">Submit</button>
						<?php
							break;
						case 1:
						?>
							<p>Enter the current username of your account and continue</p>
							<?= (isset($msg)) ? "<p class='error'>" . $msg . "</p>" : ''; ?>
							<div class="form-grouping">
								<div><i class="fas fa-user"></i><input type="text" id="username" name="username" placeholder="Enter your username" required /></div>
							</div>
							<button type="submit" class="form-btn">Continue</button>
							<p class="form-footer">Remember your password? <a href="login.php">Sign in</a></p>
					<?php
							break;
					endswitch;
					?>
				</form>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>