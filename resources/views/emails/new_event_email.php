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
      <img src="<?= $host; ?>/images/logo/Logo.png" alt="logo" style="width: 170px">
    </div>
    <div style="padding: 1.5em 0;
                border-bottom: 1px solid #d3d3d3">
      <div style="height: 200px">
        <img src="<?= $host; ?><?= $image; ?>" alt="event_thumbnail" style="height: 100%;
                                                                                  object-fit: cover;
                                                                                  width: 100%;" />
      </div>
      <div style="padding: 1em 0 0.5em">
        <h2 style="font-size: 1.2em;
                   margin: 0 0 0.5em 0;
                   line-height: 1.2;"><?= $title; ?></h2>
        <?= $body; ?>
        <h4 style="margin: 0 0 1em 0; text-align: center;">
          <?= $occursOn->format("l, j F"); ?>
          at
          <?= $occursOn->format("g a"); ?>
        </h4>
        <div style="display: flex; justify-content: center">
          <a href="<?= $link; ?>" style="color: white;
                                         font-weight: 700;
                                         border-radius: 2rem;
                                         padding: 0.5rem 3rem;
                                         text-decoration: none;
                                         background-color: steelblue;">Participate</a>
        </div>
      </div>
    </div>
    <?php require views_path("/commons/newsletter_footer.php"); ?>
  </div>
</body>

</html>