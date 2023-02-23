<?php

use Cadexsa\Domain\Model\ExStudent\Level;

?>
<div class="user-panel">
    <span><?= $username; ?></span>
    <img id="drop-button" src="<?= $profilePicture; ?>" alt="user" />
    <ul class="dropdown">
        <li>
            <img src="<?= $profilePicture; ?>" />
            <div>
                <span><?= $name; ?></span>
                <span><?= $username; ?></span>
            </div>
        </li>
        <li><a href="<?= $pathToProfile; ?>"><i class="fas fa-user"></i>My account</a></li>
        <li><a href="<?= $pathToProfile . "/settings"; ?>"><i class="fas fa-user-cog"></i>Edit account</a></li>
        <li><a href="<?= $pathToProfile . "/messages"; ?>"><i class="fas fa-envelope-open-text"></i>My messages</a></li>
        <?php if ($level !== Level::REGULAR) : ?>
            <li style="text-transform: none;"><a href="/cms/events/publish"><i class="fas fa-calendar-day"></i> Publish an event</a></li>
            <li style="text-transform: none;"><a href="/cms/news/publish"><i class="fas fa-layer-group"></i> Publish an article</a></li>
            <li style="text-transform: none;"><a href="/cms/pictures_upload"><i class="fas fa-upload"></i> Upload pictures</a></li>
        <?php endif; ?>
        <li><a href="/exstudents/login"><i class="fas fa-sign-out-alt"></i>Log out</a></li>
    </ul>
</div>