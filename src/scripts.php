<?php
include 'secret.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli("127.0.0.1", 'root', 'baseballsql', 'boonelocaldb');
if ($mysqli->connect_errno) {
  echo "Faile to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else {
  echo "Connection worked!<br>";
}

function showTable() { ?>
  <table>
    <caption>My Movies</caption>
    <thead>
      <tr>
        <th></th><th>Title</th><th>Category</th><th>Length (mins.)</th><th>Status</th>
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

  while ($stmt->fetch()) {
    echo "<tr><td>$out_id</td><td>$out_name</td><td>$out_cat</td><td>$out_length</td><td>$out_rented</td></tr>";
  }



}


?>
