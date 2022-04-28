<?php $url = parse_url($_SERVER['PHP_SELF']); ?>
<div class="cms-header">
    <div class="ws-container">
        <h3><a href="/cms/">Content Management System</a></h3>
        <ul>
            <li <?php if (preg_match("/^\/cms\/index\.php$/", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/">Home</a>
            </li>
            <li <?php if (preg_match("/\/cms\/events\//", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/events/">Events</a>
            </li>
            <li <?php if (preg_match("/\/cms\/news\//", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/news/">News articles</a>
            </li>
            <li <?php if (preg_match("/\/cms\/pictures\/upload/", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/pictures/upload">Picture Uploader</a>
            </li>
        </ul>
    </div>
</div>