<?php
// Membership profile page
require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\Membership\MemberManager;
use Application\DateTime\{ChatTimeDuration, TimeDuration};
use Application\MiddleWare\ServerRequest;

$incoming_request = (new ServerRequest())->initialize();

$is_visitor = false;
$memberInfo = MemberManager::Instance()->getMember((int) $_SESSION['ID']);

if ($incoming_request->getParsedBody()) {
	$params = $incoming_request->getParsedBody();
	if (isset($params['id']) && (int) $params['id'] !== $_SESSION['ID']) {
		$is_visitor = true;
		$memberID = $params['id'];
		$memberInfo = MemberManager::Instance()->getMember((int) $memberID);
		$state = MemberManager::Instance()->getState($memberID, new TimeDuration());
		$status = $state['status'];
		if ($status == "offline") {
			$lastConnection = $state['lastSeen'];
		}
	}
}

if (empty($memberInfo)) {
	header("Location: /members/login");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= ucwords($memberInfo->getUserName()); ?> - CADEXSA</title>
	<?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
	<script type="module" src="/static/dist/js/pages/profile.js"></script>
</head>

<body id="profile-page">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__, 2) . "/includes/header.php"; ?>
	<div class="page-content">
		<div class="ws-container">
			<div id="profile-grid">
				<div>
					<div class="profile-nav">
						<div class="user-heading">
							<a href="#"><img src="<?= $memberInfo->getPicture(); ?>" alt="<?= $memberInfo->getUserName(); ?>" /></a>
							<h5><?= $memberInfo->getName(); ?></h5>
							<p>
								<span><?= $memberInfo->getEmail(); ?></span>
								<span>(+237) <?= $memberInfo->getContact(); ?></span>
							</p>
						</div>
						<nav>
							<ul>
								<li class="tablink"><span id="tabBtn1"><i class="fas fa-user"></i>Profile</span></li>
								<?php if (!$is_visitor) : ?><li class="tablink"><span id="tabBtn2"><i class="fas fa-envelope-open-text"></i>Messages</span></li><?php endif; ?>
								<?php if (!$is_visitor) : ?><li class="tablink"><span id="tabBtn4"><i class="fas fa-user-cog"></i>Account Settings</span></li><?php endif; ?>
							</ul>
						</nav>
					</div>
				</div>
				<div class="profile-info">
					<div class="tabcontent" id="profile-info">
						<div class="panel bio-info">
							<h5><i class="fas fa-user"></i>Member</h5>
							<div>
								<div>
									<p><label>First name</label><span><?= $memberInfo->getfirstname(); ?></span></p>
								</div>
								<div>
									<p><label>Last name</label><?= $memberInfo->getlastname(); ?></span></p>
								</div>
								<div>
									<p><label>Username</label><span><?= $memberInfo->getUserName(); ?></p></span>
								</div>
								<div>
									<p><label>Email</label><span><?= $memberInfo->getEmail(); ?></span></p>
								</div>
								<div>
									<p><label>Residing Country</label><span><?= $memberInfo->getcountry(); ?></span></p>
								</div>
								<div>
									<p><label>City</label><span><?= $memberInfo->getCity(); ?></span></p>
								</div>
								<div>
									<p><label>Main phone number</label><span><?= $memberInfo->getContact(); ?></span></p>
								</div>
								<?php if (!is_null($memberInfo->getContact("secondary"))) : ?>
									<div>
										<p><label>Second phone number</label><?= $memberInfo->getContact("secondary"); ?></p>
									</div>
								<?php endif; ?>
								<div>
									<p><label>Batch year</label><span><?= $memberInfo->getBatch(); ?></p></span>
								</div>
								<div>
									<p><label>Orientation</label><span><?= $memberInfo->getorientation(); ?></span></p>
								</div>
							</div>
							<div class="aboutme">
								<div><label>About me</label></div>
								<div>
									<p><?= $memberInfo->getaboutme(); ?></p>
								</div>
							</div>
						</div>
						<div class="additional_info">
							<div class="block">
								<h5><i class="fas fa-user-cog"></i>Account</h5>
								<ul>
									<li><label>Status</label><span><?= ($is_visitor) ? ucwords($status) : "Active"; ?></span></li>
									<?php if ($is_visitor && isset($lastConnection)) : ?><li><label>Last connection</label><span><?= $lastConnection; ?></span></li><?php endif; ?>
									<li><label>Member since</label><span><?= date("l j F", strtotime($memberInfo->getRegistrationDate())) . " at " . date('g:m a', strtotime($memberInfo->getRegistrationDate())); ?></span></li>
								</ul>
							</div>
							<div class="block">
								<h5><i class="fas fa-award"></i>Education</h5>
								<ul>
									<?php $twoYearsBack = (int) $memberInfo->getBatch() - 2; ?>
									<li>Studied at <span>La Cadenelle Bilingual High School</span><span>September <?= $twoYearsBack; ?> - June <?= $memberInfo->getBatch(); ?></span></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- Chats tab -->
					<?php if (!$is_visitor) : ?>
						<div class="tabcontent" id="chats">
							<div class="panel">
								<!-- Chat box wrapper -->
								<div class="chatbox">
									<!-- Chat users -->
									<div class="chat_users">
										<div class="user_search">
											<input type="text" id="user_search" placeholder="Search Ex-students by names" />
										</div>
										<ul class="list_users">
											<?php
											foreach (MemberManager::Instance()->getMembers(6) as $chatUser) :
												if ($memberInfo->getID() == $chatUser->getID()) {
													continue;
												}
												$timeDiff = new ChatTimeDuration();
												$state = MemberManager::Instance()->getState($chatUser->getID(), $timeDiff);
											?>
												<li class="user" data-requesterUserID="<?= $memberInfo->getID(); ?>" data-requestedUserID="<?= $chatUser->getID(); ?>">
													<img src="<?= $chatUser->getPicture(); ?>" />
													<div>
														<span class="user_name"><?= $chatUser->getName(); ?></span>
														<span class="time"><?= $state['lastSeen']; ?></span>
													</div>
													<span class="status <?= $state['status']; ?>"></span>
												</li>
											<?php endforeach; ?>
										</ul>
									</div>
									<!-- Chat box contents -->
									<div class="chat_content">
										<!-- Chat Correspondent -->
										<div class="chat_correspondent">
											<div class="correspondent_info">
												<div class="menu-wrapper">
													<div class="menu"></div>
												</div>
												<img src="/static/images/graphics/profile-placeholder.png" />
												<div>
													<span id="correspondent_name"></span>
													<span class="status" id="correspondent_status"></span>
												</div>
											</div>
										</div>
										<!-- Main Chat Section -->
										<div class="chat_window">
											<div id="chat_alert"><span>No conversation</span></div>
										</div>
										<!-- Chat Input field-->
										<div class="input_field">
											<form id="send_chat"></form>
											<input type="hidden" name="action" value="postChat" form="send_chat" />
											<input type="hidden" name="chat_sender" id="chat_sender" value="<?= $memberInfo->getID(); ?>" form="send_chat" />
											<input type="hidden" name="chat_receiver" id="chat_receiver" value="" form="send_chat" />
											<textarea name="chat_msg" placeholder="Type a message" id="chat_msg" form="send_chat"></textarea>
											<button class="send_btn"></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Account settings tab -->
					<?php if (!$is_visitor) : ?>
						<div class="tabcontent" id="settings">
							<div class="panel">
								<h5><i class="fas fa-cog"></i> Update Profile</h5>
								<button>Update Profile picture</button>
								<form action="/members/profile/actions/profile.php" method="POST" id="updateProfile">
									<div><label for="firstname">First name</label><input type="text" id="first-name" name="firstname" class="form-control" /></div>
									<div><label for="lastname">Last name</label><input type="text" id="lastname" name="lastname" class="form-control" /></div>
									<div><label for="username">Username</label><input type="text" id="username" name="username" class="form-control" /></div>
									<div><label for="email">Email</label><input type="email" id="email" name="email" class="form-control" /></div>
									<div><label for="country">Country</label><input type="text" id="country" name="country" class="form-control" /></div>
									<div><label for="city">City</label><input type="text" id="city" name="city" class="form-control" /></div>
									<div><label for="contact">Phone number</label><input type="number" class="phone_number form-control" name="contact" /></div>
									<div><label for="batch_year">Batch year</label><input type="number" id="batch_year" name="batch_year" class="form-control" disabled /></div>
									<div><label for="aboutme">About me</label><textarea id="aboutme" name="aboutme" class="form-control"></textarea></div>
									<input type="hidden" name="memberID" value="<?= $memberInfo->getID(); ?>" />
									<input type="hidden" name="action" value="updateProfile" />
									<div><button type="submit" class="form-btn">Update Profile</button></div>
								</form>
							</div>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__, 2) . "/includes/footer.php"; ?>
	<?php if (!$is_visitor) : ?>
		<!-- Upload profile picture -->
		<div class="background-wrapper blurred" id="bc1">
			<span class="fas fa-times" id="exit"></span>
			<div class="box profile-picture-uploader" id="update_profile_picture">
				<div id="header">
					<h3>Profile Picture</h3>
					<p>Select a picture and update your profile picture</p>
				</div>
				<div id="picture_preview"></div>
				<div id="footer">
					<div>
						<button>Select a picture</button>
						<button>Upload</button>
					</div>
					<form>
						<input type="file" accept=".jpg, .jpeg, .png" name="input_picture" />
						<span id="picture_name"></span>
						<input type="hidden" name="action" id="action" value="updateAvatar" />
						<input type="hidden" name="memberID" id="memberID" value="<?= $memberInfo->getID(); ?>" />
						<input type="hidden" name="username" value="<?= $memberInfo->getUserName(); ?>" />
					</form>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- End -->
</body>

</html>