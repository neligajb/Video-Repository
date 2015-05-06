<?php include "scripts.php"; ?>
<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <LINK href="videoStyles.css" rel="stylesheet" type="text/css">
  <title>Boone's Video Repository</title>
</head>
<body>
  <h1>Boone's Video Repo</h1>
  <p>Choose an action below...</p>
  <div>
    <form action="scripts.php" method="post">
      <p>Add Video<input type="button" value="+" name="add_vid" /></p>
    </form>
    <form>
      <!-- getCats -->
    </form>
  </div>
  <section>
    <?php showTable(); ?>
  </section>
</body>
</html>