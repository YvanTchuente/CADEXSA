<?php
switch (true):
    case (preg_match("/^\/$/", $target)):
?>
        <li class="active"><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    case (preg_match("/^\/news\/?/", $target)):
    ?>
        <li><a href="/">HOME</a></li>
        <li class="active"><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    case (preg_match("/^\/events\/?/", $target)):
    ?>
        <li><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li class="active"><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    case (preg_match("/^\/gallery\/?/", $target)):
    ?>
        <li><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li class="active"><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    case (preg_match("/^\/aboutus\/?/", $target)):
    ?>
        <li><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li class="active"><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    case (preg_match("/^\/contactus\/?/", $target)):
    ?>
        <li><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li class="active"><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
    default:
    ?>
        <li><a href="/">HOME</a></li>
        <li><a href="/news/">NEWS</a></li>
        <li><a href="/events/">EVENTS</a></li>
        <li><a href="/gallery/">GALLERY</a></li>
        <li><a href="/aboutus">ABOUT US</a></li>
        <li><a href="/contactus">CONTACT US</a></li>
    <?php
        break;
endswitch;
if (isLoggedIn()) :
    if (!preg_match("/^\/exstudents\/?/", $target)) :
    ?>
        <li><a href="/exstudents/">EX-STUDENTS</a></li>
    <?php else : ?>
        <li class="active"><a href="/exstudents/">EX-STUDENTS</a></li>
<?php
    endif;
endif;
