<html lang="en">

<body style="display: flex;
                background: #d0d0de;
                align-items: center;
                justify-content: center;
                font-family: Helvetica, Arial, sans-serif;
                line-height: 1.5;
                margin: 0;">
    <div style="width: 600px;
                    height: calc(100% - 200px);
                    padding: 2rem 4rem;
                    background: white;">
        <div style="text-align: right; margin-bottom: 1.5rem">
            <span style="display: block; margin-bottom: 0.2rem"><?= $name; ?></span>
            <span style="display: block; margin-bottom: 0.3rem"><?= $email; ?></span>
            <span style="display: block; margin-bottom: 1rem"><?= $phoneNumber; ?></span>
            <span style="display: block; font-weight: bold;"><?= $timestamp; ?></span>
        </div>
        <div>
            <h3 style="margin: 0; text-align: center;">New message</h3>
            <p><?= $message; ?></p>
        </div>
    </div>
</body>

</html>