<!DOCTYPE html>
<html>
<head lang="en">
  <meta charset="UTF-8">
  <LINK href="videoStyles.css" rel="stylesheet" type="text/css">
  <title>Confirmation Page</title>
</head>
<body>

<?php
include 'secret.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("127.0.0.1", 'root', 'baseballsql', 'boonelocaldb');
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else {
  echo "Connection worked!<br>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete'])) {
    deleteMovie($_POST['delete']);
  }
  else {
    $missing = array();

    if ($_POST['name'] == '' || $_POST['name'] == NULL || !is_string($_POST['name'])) {
      array_push($missing, 'name');
    }
    if ($_POST['category'] == '' || $_POST['category'] == NULL || !is_string($_POST['category'])) {
      array_push($missing, 'category');
    }
    if ($_POST['length'] == '' || $_POST['length'] == NULL || !is_numeric($_POST['length'])) {
      array_push($missing, 'length');
    }

    if (count($missing) > 0) {
      homePageErr($missing);
    } else {
      addMovie();
    }
  }
}

function deleteMovie($id) {
  echo "<p>$id";
}


function addMovie() {
  $mysqli = new mysqli("127.0.0.1", 'root', 'baseballsql', 'boonelocaldb');
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("INSERT INTO video_store (name, category, length) VALUES (?,?,?)"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  if (!$stmt->bind_param("ssi", $_POST['name'], $_POST['category'], $_POST['length'])) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  else {
    homePage('Movie successfully added to the database');
  }
}


function showTable() {
  $mysqli = new mysqli("127.0.0.1", 'root', 'baseballsql', 'boonelocaldb');
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  ?>
  <table>
    <caption>My Movies</caption>
    <thead>
      <tr>
        <th></th><th>Title</th><th>Category</th><th>Length (mins.)</th><th>Status</th><th>Remove?</th>
      </tr>
    </thead>

    <?php
    if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM video_store"))) {
      echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->execute()) {
      echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    $out_id = NULL;
    $out_name = NULL;
    $out_cat = NULL;
    $out_length = NULL;
    $out_rented = NULL;

    if (!$stmt->bind_result($out_id, $out_name, $out_cat, $out_length, $out_rented)) {
      echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
    }

    echo "<tbody>";
    while ($stmt->fetch()) {
      echo "<tr><td>$out_id</td><td>$out_name</td><td>$out_cat</td><td>$out_length</td>";
      if (!$out_rented) {
        echo "<td>available</td>";
      }
      else {
        echo "<td>checked out</td>";
      }
      echo '<td><form action="scripts.php" method="post"><button name="delete" value="' . $out_id . '">X</button>';
      echo '</form></td></tr>';

    }
    echo "</tbody>";
    ?>
  </table>

<?php } ?>

<?php

function homePageErr($missing) {
  echo "<p>";
  foreach ($missing as $key => $value) {
    echo "Please enter a valid $value.<br>";
  }
  echo '<p>Click <a href="/assignment4-part2/src/">here</a> to return to the movie index.';
  die();
}

function homePage($message) {
  echo "<p>$message.";
  echo '<p>Click <a href="/assignment4-part2/src/">here</a> to return to the movie index.';
}

?>
</body>
</html>
