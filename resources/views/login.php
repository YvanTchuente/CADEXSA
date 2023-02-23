<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Yvan Tchuente">
    <title>Sign In - Member Space</title>
    <?php require views_path("/commons/metadata.php"); ?>
</head>

<body id="login">
    <?php require views_path("/commons/loader.html"); ?>
    <?= $page_header; ?>
    <div class="ws-container">
        <div>
            <div>
                <h2>Welcome Back</h2>
                <p>To keep connected with us please login with your personal informations and keep showing love for our alma-mater.</p>
            </div>
        </div>
        <form action="/exstudents/login" method="post" id="login-form">
            <h2 style="text-align: center;">Sign in</h2>
            <?php if (isset($response)) : ?><span class="error"><?php echo $response; ?></span><?php endif ?>
            <div class="form-element-container">
                <label for="username">Username</label>
                <div>
                    <i class="fas fa-user"></i>
                    <input type="text" id="username" name="username" placeholder="Type your username" required />
                </div>
            </div>
            <div class="form-element-container">
                <label for="password">Password</label>
                <div>
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Type your password" required />
                    <button type="button" class="password-visibility-button"><i class="fas fa-eye"></i></button>
                </div>
            </div>
            <input type="hidden" name="token" value="<?= $token; ?>" />
            <?php if (isset($goto)) : ?><input type="hidden" name="goto" value="<?= $goto; ?>"><?php endif; ?>
            <a href="/exstudents/password_reset" id="forgot-pass">Forgot Password ?</a>
            <button type="submit" class="full-width">Sign in</button>
            <footer>
                <p>Not yet a member? <a href="/exstudents/signup">Sign up</a></p>
            </footer>
        </form>
    </div>
    <script>
        const submit_button = document.querySelector("button[type='submit']");
        submit_button.addEventListener("click", () => {
            const password_input = document.getElementById("password");
            const type = password_input.getAttribute("type");
            if (type == "text") {
                password_input.setAttribute("type", "password");
            }
        })
    </script>
    <?php require views_path("/commons/page_footer.php"); ?>
</body>

</html>