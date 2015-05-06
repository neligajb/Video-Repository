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
      <h4>Add Video</h4>
      <p>Name: <input type="text" name="name" /></p>
      <p>Category: <input type="text" name="category" /></p>
      <p>Length: <input type="text" name="length" /></p>
      <p><input type="submit" value="Add Movie" /></p>
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