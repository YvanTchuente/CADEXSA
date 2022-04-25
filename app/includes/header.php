<!-- Website Header Section -->
<?php

require_once dirname(__DIR__) . '/config/index.php';

use Application\Membership\MemberManager;
use Application\MiddleWare\ServerRequest;

if (MemberManager::Instance()->is_logged_in()) {
    $profilePicture = MemberManager::Instance()->getMember($_SESSION['ID'])->getPicture();
}
?>
<?php $urlPath = (((new ServerRequest())->initialize())->getUri())->getPath(); ?>
<header>
    <div id="topnav">
        <div class="ws-container">
            <div class="content">
                <div id="contact-us">
                    <div class="contact_info">
                        <span><i class="fas fa-phone-alt"></i> (+237) 657384876</span>
                        <span><i class="fas fa-envelope"></i> contact@cadexsa.org</span>
                    </div>
                </div>
                <div id="members-space">
                    <?php
                    if (MemberManager::Instance()->is_logged_in()) {
                        $pathToProfile = "/members/profiles/" . strtolower($_SESSION['username']);
                    ?>
                        <div class="user-panel">
                            <span>Hello <?= $_SESSION['username']; ?></span>
                            <img class="dropbtn" src="<?= $profilePicture; ?>" alt="user" />
                            <ul class="dropdown">
                                <li>
                                    <img src="<?= $profilePicture; ?>" />
                                    <div>
                                        <span><?= $_SESSION['fullname']; ?></span>
                                        <span><?= $_SESSION['username']; ?></span>
                                    </div>
                                </li>
                                <li><a href="<?= $pathToProfile; ?>"><i class="fas fa-user"></i>My Profile</a></li>
                                <li><a href="<?= $pathToProfile . "/chats"; ?>"><i class="fas fa-envelope-open-text"></i>My Messages</a></li>
                                <li><a href="<?= $pathToProfile . "/settings"; ?>"><i class="fas fa-user-cog"></i>Account Settings</a></li>
                                <?php if ($_SESSION['level'] != 3) {
                                ?>
                                    <li style="text-transform: none;"><a href="/cms/news/publish"><i class="fas fa-layer-group"></i> Publish an article</a></li>
                                    <li style="text-transform: none;"><a href="/cms/events/plan"><i class="fas fa-calendar-day"></i> Plan an event</a></li>
                                    <li><a href="/cms/"><i class="fas fa-cogs"></i>CMS</a></li>
                                <?php
                                }
                                ?>
                                <li><a href="/members/login"><i class="fas fa-sign-out-alt"></i>Log out</a></li>
                            </ul>
                        </div>
                        <?php
                    } else {
                        switch (true) {
                            case (preg_match("/\/login$/", $urlPath)):
                        ?>
                                <a href="/members/register" class="header-btn">Create account</a>
                            <?php
                                break;
                            case (preg_match("/\/register$/", $urlPath)):
                            ?>
                                <a href="/members/login" class="header-btn">Login</a>
                            <?php
                                break;
                            default:
                            ?>
                                <a href="/members/login" class="header-btn">Login</a>
                                <a href="/members/register" class="header-btn">Create account</a>
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
                        <li <?php if (preg_match("/^\/$/", $urlPath)) { ?>class="active" <?php } ?>><a href="/">Home</a></li>
                        <li <?php if (preg_match("/^\/news\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/news/">News</a></li>
                        <li <?php if (preg_match("/^\/events\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/events/">Events</a></li>
                        <li <?php if (preg_match("/^\/gallery\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/gallery/">Gallery</a></li>
                        <li <?php if (preg_match("/^\/about_us\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/about_us/">About Us</a></li>
                        <li <?php if (preg_match("/^\/contact_us\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/contact_us/">Contact Us</a></li>
                        <?php if (MemberManager::Instance()->is_logged_in()) { ?>
                            <li <?php if (preg_match("/^\/members\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/members/">Members</a></li>
                        <?php } ?>
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
                        <?php if (MemberManager::Instance()->is_logged_in()) { ?>
                            <li <?php if (preg_match("/^\/members\/?/", $urlPath)) { ?>class="active" <?php } ?>><a href="/members/">Members</a></li>
                        <?php } ?>
                        <?php
                        if (!preg_match("/^\/members\/?/", $urlPath)) {
                            if (!MemberManager::Instance()->is_logged_in()) {
                        ?>
                                <li><a href="/members/login">Login</a></li>
                                <li><a href="/members/register">Create account</a></li>
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