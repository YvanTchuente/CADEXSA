<?php
// Membership profile page
require_once dirname(__DIR__) . '/config/index.php';

use Classes\MiddleWare\{
	Request,
	ServerRequest,
};

$incoming = new ServerRequest();
$incoming->initialize();
$outgoing = new Request();

if (!$member->is_logged_in() || !$incoming->getParsedBody()) {
	header("Location: /members/login.php");
}

$params = $incoming->getParsedBody();
$memberInfo = $member->getInfo($params['id']);
if (!isset($params['id']) || !$memberInfo) {
	header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>CADEXSA - Member Profile</title>
	<?php require_once dirname(__DIR__) . "/includes/head_tag_includes.php"; ?>
</head>

<body id="profile-page">
	<div id="loader">
		<div>
			<div class="spinner"></div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
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
								<li class="tablink"><span onclick="openTab(event,'chats')" id="tabBtn2"><i class="fas fa-envelope"></i>Messages</span></li>
								<li class="tablink"><span onclick="openTab(event,'activities')" id="tabBtn3"><i class="fas fa-calendar-alt"></i>Timeline</span></li>
								<li class="tablink"><span onclick="openTab(event,'settings')" id="tabBtn4"><i class="fas fa-user-cog"></i>Account Settings</span></li>
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
									<p><label>Country</label><span><?= $memberInfo['country']; ?></span></p>
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
					<div class="tabcontent" id="chats">
						<div class="panel">
							<!-- Chat box wrapper -->
							<div class="chatbox">
								<!-- Chat users -->
								<div class="chat_users">
									<div class="user_search">
										<input type="text" name="user_search" placeholder="Search Ex-students" />
									</div>
									<ul class="list_users">
										<li class="user open" onclick="openChatContent(event,2,1)">
											<img src="/static/images/graphics/profile-placeholder.png" />
											<div>
												<span class="user_name">Mbake Collins</span>
												<span class="time">6 days</span>
											</div>
											<span class="status online"></span>
										</li>
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
												<span id="correspondent_name">Mbake Collins</span>
												<span class="status" id="correspondent_status">Online</span>
											</div>
										</div>
									</div>
									<!-- Main Chat Section -->
									<div class="chats_area">
										<div class="my_chat">
											<div>
												<p>Hi collins</p><span class="time">08:37 AM | Saturday</span>
											</div>
											<img src="/static/images/graphics/profile-placeholder.png">
										</div>
										<div class="client_chat">
											<img src="/static/images/graphics/profile-placeholder.png">
											<div>
												<p>Hi, how are you doing?</p><span class="time">11:46 AM | Saturday</span>
											</div>
										</div>
									</div>
									<!-- Chat Input field-->
									<div class="input_field">
										<form id="send_chat"></form>
										<input type="hidden" name="sendChat" value="sendChat" form="send_chat" />
										<input type="hidden" name="chat_sender" value="<?= $memberInfo['ID']; ?>" form="send_chat" />
										<input type="hidden" name="chat_receiver" id="chat_receiver" value="" form="send_chat" />
										<textarea name="chat_msg" id="chat_msg" form="send_chat"></textarea>
										<button class="send_btn" onclick="send_chat()"></button>
									</div>
								</div>
							</div>
						</div>
					</div>
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
					<div class="tabcontent" id="settings">
						<div class="panel">
							<h5><i class="fas fa-cog"></i> Update Profile</h5>
							<button onclick="toggle_visibility('b1')">Update Profile picture</button>
							<form action="updateProfile.php" method="post" id="updateProfile">
								<div><label for="first-name">First name</label><input type="text" id="first-name" name="firstname" class="form-control" /></div>
								<div><label for="last-name">Last name</label><input type="text" id="lastname" name="lastname" class="form-control" /></div>
								<div><label for="username">Username</label><input type="text" id="username" name="username" class="form-control" disabled /></div>
								<div><label for="email">Email</label><input type="email" id="email" name="email" class="form-control" disabled /></div>
								<div><label for="country">Country</label><input type="text" id="country" name="country" class="form-control" disabled /></div>
								<div><label for="city">City</label><input type="text" id="city" name="city" class="form-control" /></div>
								<div><label for="phone">Phone number</label><input type="number" id="phoneNumber" name="phoneNumber" class="form-control" /></div>
								<div><label for="batch_year">Batch year</label><input type="number" id="batch_year" name="batch_year" class="form-control" disabled /></div>
								<div><label for="aboutme">About me</label><textarea id="aboutme" name="aboutme" class="form-control"></textarea></div>
								<div><button type="submit" name="update" class="form-btn">Update Profile</button></div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/header.php"; ?>
	<div class="page-content">
		<div class="ws-container">
			<div id="profile-grid">
				<div>
					<div class="profile-nav">
						<div class="user-heading">
							<a href="#" onclick="toggle_visibility('update_picture')"><img src="/static/images/graphics/profile-placeholder.png" alt="user" /></a>
							<h5>Webmaster</h5>
							<p>yvantchuente@gmail.com</p>
						</div>
						<nav>
							<ul>
								<li class="tablink"><span onclick="openTab(event,'profile-info')" id="tabBtn1"><i class="fas fa-user"></i>Profile</span></li>
								<li class="tablink"><span onclick="openTab(event,'chats')" id="tabBtn2"><i class="fas fa-envelope"></i>Messages</span></li>
								<li class="tablink"><span onclick="openTab(event,'activities')" id="tabBtn3"><i class="fas fa-calendar-alt"></i>Timeline</span></li>
								<li class="tablink"><span onclick="openTab(event,'settings')" id="tabBtn4"><i class="fas fa-user-cog"></i>Account Settings</span></li>
							</ul>
						</nav>
					</div>
				</div>
				<!-- Preview thumbnail of picture -->
				<div id="picture_preview"></div>
				<div id="footer">
					<form id="profile_picture"></form>
					<input type="file" accept=".jpg, .jpeg, .png" id="input_picture" name="input_picture" form="profile_picture" />
					<label for="input_picture">Select a picture</label>
					<span id="input_text"></span>
					<input type="hidden" name="userid" id="userid" value="<?= $memberInfo['ID']; ?>" form="profile_picture" />
					<input type="hidden" name="username" value="<?= $memberInfo['username']; ?>" form="profile_picture" />
					<button id="cancel_btn">Cancel</button>
				</div>
			</div>
		</div>
		<!-- End -->
		<script>
			<?php if (isset($_GET['tab'])) {
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
			let users = document.querySelectorAll(".chatbox ul.list_users li.user");
			users[0].click();
		</script>
</body>

</html>