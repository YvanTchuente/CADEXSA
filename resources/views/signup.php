<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Sign Up - Member Space</title>
    <?php require views_path("/commons/metadata.php"); ?>
    <script type="module" src="/js/nice_selects.js"></script>
</head>

<body id="signup">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <div class="ws-container">
        <div>
            <div>
                <h2>Join our vibrant community of ex-students</h2>
                <p>Show affection for your alma-mater and help us make it grow. Please fill out the form completely.</p>
            </div>
        </div>
        <form action="/exstudents/signup" method="post" id="signup_form" class="signup">
            <h1>Ex-student registration</h1>
            <?php if (isset($response)) : ?><span class="error"><?php echo $response; ?></span><?php endif ?>
            <div class="form-element-container grid">
                <label for="firstname">First name</label>
                <input type="text" id="firstname" name="firstname" required />
            </div>
            <div class="form-element-container grid">
                <label for="lastname">Last name</label>
                <input type="text" id="lastname" name="lastname" required />
            </div>
            <div class="form-element-container grid">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required />
            </div>
            <div class="form-element-container grid">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="form-element-container grid">
                <label for="password">Password</label>
                <div><input type="password" id="password" name="password" required /><button type="button" class="password-visibility-button"><i class="fas fa-eye"></i></button></div>
            </div>
            <div class="form-element-container grid">
                <label for="confirmation_password">Confirm your password</label>
                <div><input type="password" id="confirmation_password" name="confirmation_password" required /><button type="button" class="password-visibility-button"><i class="fas fa-eye"></i></button></div>
            </div>
            <div class="form-element-container grid">
                <label>Graduation year</label>
                <div class="nice-select" id="nice-select-1">
                    <span class="current" style="color: black;">Batch year</span>
                    <ul class="dropdown">
                        <li class="selected">Batch year</li>
                        <li>2022</li>
                        <li>2021</li>
                        <li>2020</li>
                        <li>2019</li>
                        <li>2018</li>
                        <li>2017</li>
                        <li>2016</li>
                    </ul>
                    <select id="select-year" name="batch_year" required>
                        <option value="" selected>Batch year</option>
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                        <option value="2020">2020</option>
                        <option value="2019">2019</option>
                        <option value="2018">2018</option>
                        <option value="2017">2017</option>
                        <option value="2016">2016</option>
                    </select>
                </div>
            </div>
            <div class="form-element-container grid">
                <label>Orientation</label>
                <div class="nice-select" id="nice-select-2">
                    <span class="current" style="color: black;">Orientation</span>
                    <ul class="dropdown">
                        <li class="selected">Orientation</li>
                        <li>Science</li>
                        <li>Arts</li>
                    </ul>
                    <select id="select-orientation" name="orientation" required>
                        <option value="" selected>Orientation</option>
                        <option value="Science">Science</option>
                        <option value="Arts">Arts</option>
                    </select>
                </div>
            </div>
            <div class="form-element-container grid">
                <label for="country">Country</label>
                <input type="text" id="country" name="country" required />
            </div>
            <div class="form-element-container grid">
                <label for="city">City</label>
                <input type="text" id="city" name="city" required />
            </div>
            <div class="form-element-container grid">
                <label for="phone_number">Phone number</label>
                <input class="phone_number" id="main_contact" name="phone_number" required />
            </div>
            <div class="form-element-container grid">
                <label for="description">Description</label>
                <textarea id="aboutme" name="description" placeholder="Let other ex-students know what you are up to" required /></textarea>
            </div>
            <input type="hidden" name="token" value="<?= $token; ?>" />
            <button type="submit" class="full-width">Register</button>
            <footer>
                <p>Already have an account? <a href="/exstudents/login">Sign in</a></p>
            </footer>
        </form>
    </div>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>