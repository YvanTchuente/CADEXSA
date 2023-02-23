<!-- Website Header Section -->
<header class="page-header">
    <div>
        <div class="ws-container">
            <?php if (isset($level) and $level !== 1) : ?>
                <div id="admin-space">
                    <a href="/cms/"><i class="fas fa-cogs"></i>CMS</a>
                </div>
            <?php endif; ?>
            <div id="member-space">
                <?php
                if (isset($user_panel)) {
                    echo $user_panel;
                } else {
                    switch (true) {
                        case (preg_match("/\/login$/", $target)):
                ?>
                            <a href="/exstudents/signup" class="header-button">Join CADEXSA</a>
                        <?php
                            break;
                        case (preg_match("/\/signup$/", $target)):
                        ?>
                            <a href="/exstudents/login" class="header-button">Log in</a>
                        <?php
                            break;
                        default:
                        ?>
                            <a href="/exstudents/login" class="header-button">Log in</a>
                            <a href="/exstudents/signup" class="header-button">Join CADEXSA</a>
                <?php
                            break;
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <div class="ws-container">
        <div class="logo"><a href="/"><img src="/images/logo/Logo.png" alt="logo" /></a></div>
        <nav id="main-menu">
            <ul>
                <?= $navigation_menu_links; ?>
            </ul>
        </nav>
        <!-- Mobile navigation -->
        <div class="hamburger-icon">
            <div class="bars"></div>
        </div>
        <nav class="mobile-menu">
            <ul>
                <?= $navigation_menu_links; ?>
                <?php if (!isLoggedIn()) : ?>
                    <li><a href="/exstudents/login">LOG IN</a></li>
                    <li><a href="/exstudents/signup">CREATE ACCOUNT</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <!--End-->
    </div>
</header>