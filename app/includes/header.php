<!-- Website Header Section -->
<?php
require_once dirname(__DIR__) . '/config/index.php';
if ($member->is_logged_in()) {
    $profilePicture = $member->getInfo($_SESSION['ID'])['picture'];
}
?>
<?php $url = parse_url($_SERVER['PHP_SELF']); ?>
<header>
    <div id="topnav">
        <div class="ws-container">
            <div class="content">
                <div id="contact-us">
                    <div class="contact_info">
                        <span><i class="fas fa-phone-alt"></i> (+237) 657384876</span>
                        <span><i class="fas fa-envelope"></i> info@cadexsa.org</span>
                    </div>
                </div>
                <div id="members-space">
                    <?php
                    if ($member->is_logged_in()) {
                    ?>
                        <div class="user-panel">
                            <span>Hello <?php echo $_SESSION['firstname']; ?></span>
                            <img class="dropbtn" src="<?= $profilePicture; ?>" alt="user" />
                            <ul class="dropdown">
                                <li>
                                    <img src="<?= $profilePicture; ?>" />
                                    <div>
                                        <span><?= $_SESSION['firstname']; ?> <?php echo $_SESSION['lastname']; ?></span>
                                        <span><?= $_SESSION['username']; ?></span>
                                    </div>
                                </li>
                                <li><a href="/members/profile/"><i class="fas fa-user"></i>My Profile</a></li>
                                <li><a href="/members/profile/?tab=chats"><i class="fas fa-envelope"></i>My Messages</a></li>
                                <li><a href="/members/profile/?tab=settings"><i class="fas fa-user-cog"></i>Account Settings</a></li>
                                <li><a href="/members/login.php"><i class="fas fa-sign-out-alt"></i>Log out</a></li>
                            </ul>
                        </div>
                        <?php
                    } else {
                        switch (true) {
                            case (preg_match("/\/login\.php$/", $url['path'])):
                        ?>
                                <a href="/members/register.php" class="header-btn">Create account</a>
                            <?php
                                break;
                            case (preg_match("/\/register\.php$/", $url['path'])):
                            ?>
                                <a href="/members/login.php" class="header-btn">Login</a>
                            <?php
                                break;
                            default:
                            ?>
                                <a href="/members/login.php" class="header-btn">Login</a>
                                <a href="/members/register.php" class="header-btn">Create account</a>
                    <?php
                                break;
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div class="header-content">
        <div class="ws-container">
            <div class="header-grid-container">
                <div class="logo"><a href="/"><img src="/static/images/logo/Logo.png" alt="logo" /></a></div>
                <div class="nav">
                    <ul>
                        <li <?php if (preg_match("/^\/index\.php$/", $url['path'])) { ?>class="active" <?php } ?>><a href="/">Home</a></li>
                        <li <?php if (preg_match("/^\/news\/?/", $url['path'])) { ?>class="active" <?php } ?>><a href="/news/">News</a></li>
                        <li <?php if (preg_match("/^\/events\/?/", $url['path'])) { ?>class="active" <?php } ?>><a href="/events/">Events</a></li>
                        <li <?php if (preg_match("/^\/gallery\/?/", $url['path'])) { ?>class="active" <?php } ?>><a href="/gallery/">Gallery</a></li>
                        <li <?php if (preg_match("/^\/about_us\/?/", $url['path'])) { ?>class="active" <?php } ?>><a href="/about_us/">About Us</a></li>
                        <li <?php if (preg_match("/^\/contact_us\/?/", $url['path'])) { ?>class="active" <?php } ?>><a href="/contact_us/">Contact Us</a></li>
                    </ul>
                </div>
                <!-- Navigation for mobile-->
                <div class="menu-wrapper">
                    <div class="menu"></div>
                </div>
                <div class="menu-links">
                    <ul class="nav">
                        <li class="active"><a href="/">Home</a></li>
                        <li><a href="/news/">News</a></li>
                        <li><a href="/events/">Events</a></li>
                        <li><a href="/gallery/">Gallery</a></li>
                        <li><a href="/about_us/">About Us</a></li>
                        <li><a href="/contact_us/">Contact Us</a></li>
                        <?php
                        if (!preg_match("/^\/members\/?/", $url['path'])) {
                            if (!$member->is_logged_in()) {
                        ?>
                                <li><a href="/members/login.php">Login</a></li>
                                <li><a href="/members/register.php">Create account</a></li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <!--End-->
            </div>
        </div>
    </div>
</header>