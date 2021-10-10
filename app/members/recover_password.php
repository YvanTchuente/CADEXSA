<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Recover your password and sign in back">
	<title>CADEXSA - Password Recovery</title>
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
				<form action="" method="post">
					<div class="form-header">
						<h2>Password Recovery</h2>
					</div>
					<p>Enter an email to which a code to recover you account will be sent</p>
					<div class="form-grouping">
						<!-- <label for="username">Username</label> -->
						<div><i class="fas fa-envelope"></i><input type="email" id="email" name="email" placeholder="Enter an email" required /></div>
					</div>
					<button name="login" type="submit" class="form-btn">Submit</button>
					<p class="form-footer">Remember your password? <a href="login.php">Sign in</a></p>
				</form>
			</div>
		</div>
	</div>
	<?php require_once dirname(__DIR__) . "/includes/footer.php"; ?>
</body>

</html>