<?php

require_once dirname(__DIR__) . '/config/index.php';
require_once dirname(__DIR__) . '/config/mailserver.php';

use Application\Network\Requests;
use Application\PHPMailerAdapter;
use Application\Security\Securer;
use Application\Database\Connection;
use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (MemberManager::Instance()->is_logged_in()) {
	header('Location: profile.php');
}

$incoming_request =  (new ServerRequest())->initialize();
$param = $incoming_request->getParsedBody();

$step = 1;
if (!empty($param) || !empty($_COOKIE['password_reset_count'])) {
	if (isset($param['username'])) {
		$username = $param['username'];
		if (MemberManager::Instance()->check_member_exist($username)) {
			$stmt = Connection::Instance()->getConnection()->prepare("SELECT ID FROM members WHERE username = ?");
			$stmt->execute([$username]);
			$memberID = $stmt->fetch(\PDO::FETCH_ASSOC)['ID'];
			$step = 2;
		} else {
			$msg = "Member does not exist";
		}
	}
	if (isset($param['email']) && isset($param['memberID']) && isset($param['username'])) {
		extract($param);
		$key = sha1(random_bytes(16));
		$link = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'] . "/members/recovery?key=$key";
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			//Check if the member has not already already attempted to reset password
			$stmt = Connection::Instance()->getConnection()->prepare("SELECT * FROM recover_passwords WHERE memberID = ?");
			$has_attempted = $stmt->execute([$memberID]);
			if ($has_attempted) {
				$step = 2;
				$msg = "Already attempted to reset your password! check you mails for the key sent";
				exit();
			}
			$insert_sql = "INSERT INTO recover_passwords (memberID, email, recover_key) VALUES (?,?,?)";
			$stmt = Connection::Instance()->getConnection()->prepare($insert_sql);
			$query = $stmt->execute([$memberID, $email, $key]);

			if (!$query) {
				$step = 2;
				$msg = "An error occurred, please retry";
				exit();
			}

			$member = MemberManager::Instance()->getMember($memberID);

			$recipientMail = $email;
			$senderMail = MAILSERVER_ACCOUNTS_ACCOUNT;
			$subject = "Recover your account";

			$template_file_url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/includes/mail_templates/recover_account_mail.php';
			$mailBody = (new Requests())->post($template_file_url, ['username' => $member->getUserName(), 'link' => $link]);

			$mailer = new PHPMailerAdapter(MAILSERVER_HOST, MAILSERVER_ACCOUNTS_ACCOUNT, MAILSERVER_PASSWORD);
			$mailer->setSender($senderMail, "Cadexsa Accounts");
			$mailer->setRecipient($recipientMail);
			$mailer->setBody($mailBody, $subject);
			$mailer->send();

			$step = 2;
			$msg = "Check your mail for the code sent";
		} else {
			$step = 2;
			$msg = "Invalid email address";
		}
	}
	if (isset($param['key'])) {
		extract($param);
		$sql = "SELECT * FROM recover_passwords WHERE recover_key = ?";
		$stmt = Connection::Instance()->getConnection()->prepare($sql);
		$stmt->execute([$key]);
		if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			extract($row);
			$submit_time = strtotime($timestamp);
			$expiration_time = 3 * 24 * 60 * 60; // Expiration time of 3 days
			$expiry_time = $submit_time + $expiration_time;
			$current_time = time();
			$time_elpased = $expiry_time - $current_time; // Time in seconds passsed mail to recover account was sent
			if ($time_elpased <= $expiration_time) {
				$id = $memberID;
				$step = 3;
			} else {
				$step = 1;
				$msg = "The key has expired, try again";
				$stmt = Connection::Instance()->getConnection()->prepare("DELETE FROM recover_passwords WHERE memberID = ?");
				$stmt->execute([$memberID]);
			}
		} else {
			$step = 1;
			$msg = "Invalid recovery key";
		}
	}
	if ((isset($param['confirm_password']) && isset($param['id'])) || !empty($_COOKIE['password_reset_count'])) {
		if (!empty($param)) extract($param);
		(isset($_COOKIE['password_reset_count'])) ? $count = $_COOKIE['password_reset_count'] : $count = 0;
		$stmt = Connection::Instance()->getConnection()->prepare("SELECT password, password_key, iv FROM members WHERE id = ?");
		$stmt->execute([$id]);
		if ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
			extract($row);
			$db_pwd = (new Securer())->decrypt($password, $password_key, $iv);
			if ($count <= 2) {
				if ($confirm_password == trim($db_pwd)) {
					$step = 4;
				} else {
					$step = 3;
					$count += 1;
					$msg = "Incorrect password";
					setcookie('password_reset_count', $count, httponly: true);
				}
			} else {
				$step = 1;
				$msg = "Could not confirm your identity";
				$stmt = Connection::Instance()->getConnection()->prepare("DELETE FROM recover_passwords WHERE memberID = ?");
				$stmt->execute([$id]);
				setcookie('password_reset_count');
			}
		}
	}
	if (isset($param['resetted_password']) && isset($param['id'])) {
		extract($param);
		if (ctype_alnum($resetted_password) && strlen($resetted_password) >= 8) {
			$encryption = (new Securer())->encrypt($resetted_password);
			$stmt = Connection::Instance()->getConnection()->prepare("UPDATE members SET password = ?, password_key = ?, iv = ? WHERE ID = $id");
			if ($stmt->execute([$encryption['cipher'], $encryption['key'], $encryption['iv']])) {
				Connection::Instance()->getConnection()->query("DELETE FROM recover_passwords WHERE memberID = $id");
				header('Location: login');
			}
		} else {
			$step = 4;
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
	<title>Account Recovery - CADEXSA</title>
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
				<form action="recovery" method="post">
					<div class="form-header">
						<h2>Account Recovery</h2>
					</div>
					<?php
					switch ($step):
						case 4:
					?>
							<p>Reset your password, you're passwor should be at least 8 characters long</p>
							<?= (isset($msg)) ? "<p class='error'>" . $msg . "</p>" : ''; ?>
							<div class="form-grouping">
								<div style="position: relative;"><i class="fas fa-lock"></i><input type="password" id="password" placeholder="Enter a password" name="resetted_password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
							</div>
							<?= (isset($id)) ? '<input type="hidden" name="id" value="' . $id . '">' : ''; ?>
							<button type="submit" class="form-btn">Submit</button>
						<?php
							break;
						case '3':
						?>
							<p>We verify that you are actually the owner of this account. Enter your current password</p>
							<?= (isset($msg)) ? "<p class='error'>" . $msg . "</p>" : ''; ?>
							<div class="form-grouping">
								<div style="position: relative;"><i class="fas fa-lock"></i><input type="password" id="password" placeholder="Enter a password" name="confirm_password" required /><button type="button" class="password-visibility-btn"><i class="fas fa-eye"></i></button></div>
							</div>
							<?= (isset($id)) ? '<input type="hidden" name="id" value="' . $id . '">' : ''; ?>
							<button type="submit" class="form-btn">Submit</button>
						<?php
							break;
						case 2:
						?>
							<p>We will send a link code to your email account to recover you account. Enter an email</p>
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
							<p class="form-footer">Remember your password? <a href="login">Sign in</a></p>
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