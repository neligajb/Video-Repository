<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <LINK href="videoStyles.css" rel="stylesheet" type="text/css">
  <title>Boone's Video Repository</title>
</head>
<?php
include "scripts.php";
ini_set('display_errors', 1);
error_reporting(E_ALL);
?>
<body>
  <h1>Boone's Video Repo</h1>
  <div>
    <form action="scripts.php" method="post" class="text-input">
      <h4>Add Video</h4>
      <p>Name: <input type="text" name="name" required /></p>
      <p>Category: <input type="text" name="category" /></p>
      <p>Length (mins): <input type="number" size="4" min="0" max="999" name="length" /></p>
      <p><input type="submit" value="Add Movie" /></p>
    </form>
    <?php actionMessage(); ?>
    <form>
      <?php getCategories(); ?>
    </form>
  </div>
  <section>
    <?php showTable(); ?>
    <div>
      <form action="scripts.php" method="post">
        <p><input type="submit" name="delete-all" value="Delete All Movies" /></p>
      </form>
    </div>
  </section>
</body>
</html>