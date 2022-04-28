<?php
extract($_POST); // $_POST contains the preview data
$host = $_SERVER['REQUEST_SCHEME'] . "://" . $_SERVER['HTTP_HOST'];
?>
<html lang="en">

<body style="display: flex; 
            line-height: 1.5;
            align-items: center;
            justify-content: center;
            font-family: Helvetica, Arial, sans-serif;">
  <style>
    p {
      margin: 1rem 0;
    }
  </style>
  <div style="width: 400px;
              margin: 1.5em 0;
              height: fit-content;">
    <div style="display: flex; justify-content: center;">
      <img src="<?= $host; ?>/static/images/logo/Logo.png" alt="logo" style="width: 170px">
    </div>
    <div style="padding: 1.5em 0;
                border-bottom: 1px solid #d3d3d3">
      <div style="height: 200px">
        <img src="<?= $host; ?><?= $thumbnail; ?>" alt="event_thumbnail" style="height: 100%;
                                                                                  object-fit: cover;
                                                                                  width: 100%;" />
      </div>
      <div style="padding: 1em 0 0.5em">
        <h2 style="font-size: 1.2em;
                   margin: 0 0 0.5em 0;
                   line-height: 1.2;"><?= $title; ?></h2>
        <?= $body; ?>
        <h4 style="margin: 0 0 1em 0; text-align: center;">
          <?= date("l, j F", strtotime($deadline)); ?>
          at
          <?= date("g a", strtotime($deadline)); ?>
        </h4>
        <div style="display: flex; justify-content: center">
          <a href="<?= $host; ?>/events/<?= $id; ?>" style="color: white;
                                                                font-weight: 700;
                                                                border-radius: 2rem;
                                                                padding: 0.5rem 3rem;
                                                                text-decoration: none;
                                                                background-color: steelblue;">Join us</a>
        </div>
      </div>
    </div>
    <?php require_once dirname(__DIR__, 2) . '/includes/newsletter_footer.php'; ?>
  </div>
</body>

</html>