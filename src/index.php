<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <LINK href="videoStyles.css" rel="stylesheet" type="text/css">
  <title>Boone's Video Repository</title>
</head>
<?php include "scripts.php"; ?>
<body>
  <h1>Boone's Video Repo</h1>
  <p>Choose an action below...</p>
  <div>
    <form action="scripts.php" method="post">
      <h4>Add Video</h4>
      <p>Name: <input type="text" name="name" required /></p>
      <p>Category: <input type="text" name="category" required /></p>
      <p>Length (mins): <input type="number" size="6" min="0" max="999" name="length" required /></p>
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