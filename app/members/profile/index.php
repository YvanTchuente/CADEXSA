<?php
// Membership profile page
require_once dirname(__DIR__, 2) . '/config/index.php';

use Application\MiddleWare\{
	Request,
	ServerRequest,
};

$incoming = new ServerRequest();
$incoming->initialize();
$outgoing = new Request();

if (!$member->is_logged_in()) {
	header("Location: /members/login.php");
}

$memberInfo = $member->getInfo();

if ($incoming->getParsedBody()) {
	$params = $incoming->getParsedBody();
	if (isset($params['id']) && $params['id'] !== $_SESSION['ID']) {
		$visitor = true;
		$memberID = $params['id'];
		$memberInfo = $member->getInfo((int) $memberID);
	}
}

if (!isset($memberInfo)) {
	header("Location: /members/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CADEXSA - Member Profile</title>
	<?php require_once dirname(__DIR__, 2) . "/includes/head_tag_includes.php"; ?>
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
							<a href="#" onclick="toggle_visibility('update_picture')"><img src="<?= $memberInfo['picture']; ?>" alt="user" /></a>
							<h5><?= $memberInfo['username']; ?></h5>
							<p><?= $memberInfo['email']; ?></p>
						</div>
						<nav>
							<ul>
								<li class="tablink"><span onclick="openTab(event,'profile-info')" id="tabBtn1"><i class="fas fa-user"></i>Profile</span></li>
								<?php if (!isset($visitor) || !$visitor) : ?><li class="tablink"><span onclick="openTab(event,'chats')" id="tabBtn2"><i class="fas fa-envelope"></i>Messages</span></li><?php endif; ?>
								<li class="tablink"><span onclick="openTab(event,'activities')" id="tabBtn3"><i class="fas fa-calendar-alt"></i>Timeline</span></li>
								<?php if (!isset($visitor) || !$visitor) : ?><li class="tablink"><span onclick="openTab(event,'settings')" id="tabBtn4"><i class="fas fa-user-cog"></i>Account Settings</span></li><?php endif; ?>
							</ul>
						</nav>
					</div>
				</div>
				<div class="profile-info">
					<div class="tabcontent" id="profile-info">
						<div class="panel bio-info">
							<h5><i class="fas fa-user"></i>Member info</h5>
							<div>
								<div>
									<p><label>First name</label><span><?= $memberInfo['firstname']; ?></span></p>
								</div>
								<div>
									<p><label>Last name</label><?= $memberInfo['lastname']; ?></span></p>
								</div>
								<div>
									<p><label>Username</label><span><?= $memberInfo['username']; ?></p></span>
								</div>
								<div>
									<p><label>Email</label><span><?= $memberInfo['email']; ?></span></p>
								</div>
								<div>
									<p><label>Residing Country</label><span><?= $memberInfo['country']; ?></span></p>
								</div>
								<div>
									<p><label>City</label><span><?= $memberInfo['city']; ?></span></p>
								</div>
								<div>
									<p><label>Phone number</label><span><?= $memberInfo['contact']; ?></span></p>
								</div>
								<div>
									<p><label>Batch year</label><span><?= $memberInfo['batch_year']; ?></p></span>
								</div>
								<div>
									<p><label>Orientation</label><span><?= $memberInfo['orientation']; ?></span></p>
								</div>
							</div>
							<div class="aboutme">
								<div><label>About me</label></div>
								<div>
									<p><?= $memberInfo['aboutme']; ?></p>
								</div>
							</div>
						</div>
						<div class="additional_info">
							<div class="block">
								<h5><i class="fas fa-user-cog"></i>Account</h5>
								<ul>
									<li><label>Status</label><span>Active</span></li>
									<li><label>Last connection</label><span><?= ($memberInfo['last_connection']) ? date('Y-m-d H:m:s', strtotime($memberInfo['last_connection'])) : '-'; ?></span></li>
									<li><label>Member since</label><span><?= date('Y-m-d H:m:s', strtotime($memberInfo['registered_on'])); ?></span></li>
								</ul>
							</div>
							<div class="block">
								<h5><i class="fas fa-award"></i>Education</h5>
								<ul>
									<?php $twoYearsBack = (int) $memberInfo['batch_year'] - 2; ?>
									<li>Studied at <span>La Cadenelle Bilingual High School</span><span>September <?= $twoYearsBack; ?> - June <?= $memberInfo['batch_year']; ?></span></li>
								</ul>
							</div>
						</div>
					</div>
					<!-- Chats tab -->
					<?php if (!isset($visitor) || !$visitor) : ?>
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
											foreach ($member->getMembers() as $chatUser) :
												if ($memberInfo['ID'] == $chatUser['ID']) {
													continue;
												}
												$state = $member->getState((int) $chatUser['ID']);
											?>
												<li class="user" onclick="openChatTab(event,<?= $chatUser['ID']; ?>,<?= $memberInfo['ID']; ?>)">
													<img src="<?= $chatUser['picture']; ?>" />
													<div>
														<span class="user_name"><?= $chatUser['firstname'] . " " . $chatUser['lastname']; ?></span>
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
											<input type="hidden" name="chat_sender" id="chat_sender" value="<?= $memberInfo['ID']; ?>" form="send_chat" />
											<input type="hidden" name="chat_receiver" id="chat_receiver" value="" form="send_chat" />
											<textarea name="chat_msg" placeholder="Type a message" id="chat_msg" form="send_chat"></textarea>
											<button class="send_btn" onclick="sendChat()"></button>
										</div>
									</div>
								</div>
							</div>
						</div>
					<?php endif; ?>
					<!-- Activities tab -->
					<div class="tabcontent" id="activities">
						<div class="panel">
							<h5><i class="fas fa-calendar-alt"></i> Timeline</h5>
							<ul>
								<li>You commented a blog post yesterday<span>On June 3, 2021 at 3pm</span></li>
								<li>You recently updated your profile picture<span>On June 10, 2021 at 1pm</span></li>
								<li>Receive emails about our planned events.<span>On May 08, 2021 at 5pm</span></li>
								<li>News of our activities on a monthly basis<span>On January 20, 2022 at 5pm</span></li>
							</ul>
						</div>
					</div>
					<!-- Account settings tab -->
					<?php if (!isset($visitor) || !$visitor) : ?>
						<div class="tabcontent" id="settings">
							<div class="panel">
								<h5><i class="fas fa-cog"></i> Update Profile</h5>
								<button onclick="toggle_visibility('b1')">Update Profile picture</button>
								<form action="/members/profile/actions/profile.php" method="POST" id="updateProfile">
									<div><label for="firstname">First name</label><input type="text" id="first-name" name="firstname" class="form-control" disabled /></div>
									<div><label for="lastname">Last name</label><input type="text" id="lastname" name="lastname" class="form-control" disabled /></div>
									<div><label for="username">Username</label><input type="text" id="username" name="username" class="form-control" /></div>
									<div><label for="email">Email</label><input type="email" id="email" name="email" class="form-control" /></div>
									<div><label for="country">Country</label><input type="text" id="country" name="country" class="form-control" disabled /></div>
									<div><label for="city">City</label><input type="text" id="city" name="city" class="form-control" /></div>
									<div><label for="contact">Phone number</label><input type="number" id="phoneNumber" name="contact" class="form-control" /></div>
									<div><label for="batch_year">Batch year</label><input type="number" id="batch_year" name="batch_year" class="form-control" disabled /></div>
									<div><label for="aboutme">About me</label><textarea id="aboutme" name="aboutme" class="form-control"></textarea></div>
									<input type="hidden" name="memberID" value="<?= $memberInfo['ID']; ?>" />
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
	<?php if (!isset($visitor) || !$visitor) : ?>
		<!-- Upload profile picture -->
		<div class="blur_background" id="b1">
			<div id="update_picture">
				<span class="fas fa-times" id="exit"></span>
				<div id="head">
					<h3>Change Profile Picture</h3>
					<p>The picture will be scaled down and cropped</p>
					<button id="upload_btn">Upload</button>
				</div>
				<!-- Preview thumbnail of picture -->
				<div id="picture_preview"></div>
				<div id="footer">
					<form id="profile_picture"></form>
					<input type="file" accept=".jpg, .jpeg, .png" id="input_picture" name="input_picture" form="profile_picture" />
					<label for="input_picture">Select a picture</label>
					<span id="input_text"></span>
					<input type="hidden" name="action" id="action" value="updateAvatar" form="profile_picture" />
					<input type="hidden" name="memberID" id="memberID" value="<?= $memberInfo['ID']; ?>" form="profile_picture" />
					<input type="hidden" name="username" value="<?= $memberInfo['username']; ?>" form="profile_picture" />
					<button id="cancel_btn">Cancel</button>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<!-- End -->
	<script>
		<?php if (isset($params['tab'])) {
			switch ($_GET['tab']) {
				case "chats":
					echo "document.getElementById(\"tabBtn2\").click();";
					break;
				case "activities":
					echo "document.getElementById(\"tabBtn3\").click();";
					break;
				case "settings":
					echo "document.getElementById(\"tabBtn4\").click();";
					break;
			}
		?>
		<?php } else { ?>
			// Open default tab
			document.getElementById("tabBtn1").click();
		<?php } ?>
	</script>
</body>

</html>