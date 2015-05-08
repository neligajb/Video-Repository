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
      <p>Name: <input id="name" type="text" name="name" required /></p>
      <p>Category: <input id="category" type="text" name="category" /></p>
      <p>Length: <input id="length" type="number" size="4" min="0" max="999" name="length" /> mins</p>
      <p><input class="button" id="add-movie" type="submit" value="Add Movie" /></p>
    </form>
  </div>
  <div>
    <?php actionMessage(); ?>
  </div>
  <div>
    <form>
      <?php getCategories(); ?>
    </form>
  </div>
  <section>
    <?php showTable(); ?>
    <div>
      <form action="scripts.php" method="post">
        <p><input class="button" type="submit" name="delete-all" value="Delete All Movies" /></p>
      </form>
    </div>
  </section>
</body>
</html>