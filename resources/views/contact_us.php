<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="keywords" content="contact, form, phone">
    <meta name="description" content="We would love to hear from you, send us a message or request and we'll gladly answer back">
    <meta name="author" content="Yvan Tchuente">
    <title>Contact Us - CADEXSA</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body>
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <header class="page-content-header" id="contact-header">
        <h2>We would love to hear from you</h2>
        <p>Are you interested in our ex-students association, its features and events or anything else, send us a message and we would be happy to answer all your questions.</p>
    </header>
    <div class="ws-container" id="contact-us-content">
        <form action="contactus" method="POST">
            <h3>CONTACT US</h3>
            <p>Please fill out the form completely</p>
            <?php if (isset($msg)) : ?><span class="msg"><?= $msg; ?></span><?php endif; ?>
            <div class="form-element-container-grouping">
                <div><label for="firstname">First name</label><input type="text" class="form-control" id="firstname" name="firstname" required /></div>
                <div><label for="lastname">Last name</label><input type="text" class="form-control" id="lastname" name="lastname" required /></div>
            </div>
            <div class="form-element-container"><label for="email">Email address</label><input type="email" class="form-control" id="email" name="email" required /></div>
            <div class="form-element-container"><label for="phoneNumber">Phone number</label><input class="form-control phone_number" id="phoneNumber" name="phoneNumber" /></div>
            <label for="message">Message</label><textarea id="message" name="message" class="form-control" placeholder="We're interested in what you have to say"></textarea>
            <input type="hidden" name="token" value="<?= $token; ?>" />
            <button type="submit">Submit</button>
        </form>
        <div>
            <h3 style="margin-bottom: 1em;">Points of Contact</h3>
            <div>
                <span><i class="fas fa-phone-alt" style="padding-right: 10px;"></i>Phone number</span>
                <span>(+237) 657384876</span>
            </div>
            <div>
                <span><i class="fas fa-envelope" style="padding-right: 10px;"></i>E-mail address</span>
                <span><?= config("mail.accounts.info"); ?></span>
            </div>
            <div>
                <span>Follow us</span>
                <div>
                    <a href="#" class="btn-facebook"><span class="fab fa-facebook-f"></span></a>
                    <a href="#" class="btn-twitter"><span class="fab fa-twitter"></span></a>
                </div>
            </div>
        </div>
    </div>
    <div id="contact_footer">
        <div class="ws-container">
            <h3>More than just a website</h3>
            <p>CADEXSA is an association of people who express love for their alma-mater and among themselves, join us now and rock along with us through our achievements</p>
            <a href="/exstudents/signup" class="button">Become a member</a>
        </div>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>