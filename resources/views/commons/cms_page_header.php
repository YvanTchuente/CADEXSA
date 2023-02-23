<?php $url = parse_url($_SERVER['PHP_SELF']); ?>
<section class="cms-header">
    <div class="ws-container">
        <h3><a href="/cms/">Content Manager</a></h3>
        <ul>
            <li <?php if (preg_match("/^\/cms$/", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/">Home</a>
            </li>
            <li <?php if (preg_match("/\/cms\/events/", $url['path'])) { ?>class=" active" <?php } ?>>
                <a href="/cms/events/">Events</a>
            </li>
            <li <?php if (preg_match("/\/cms\/news/", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/news/">News</a>
            </li>
            <li <?php if (preg_match("/\/cms\/pictures\/upload/", $url['path'])) { ?>class="active" <?php } ?>>
                <a href="/cms/pictures_upload">Picture Uploader</a>
            </li>
        </ul>
    </div>
</section>