<?php
include 'secret.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($db_address, $db_user, $password, $db_name);
if ($mysqli->connect_errno) {
  echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
else {
  //echo "Connection worked!<br>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['delete'])) {
    deleteMovie($_POST['delete']);
  }
  else if (isset($_POST['checkout'])) {
    checkoutMovie($_POST['checkout']);
  }
  else {    // else we are attempting to add a movie
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


function checkoutMovie($id) {

  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("SELECT rented FROM video_store WHERE id = ?"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }


  if (!$stmt->bind_param("i", $id)) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  $rented = NULL;

  if (!$stmt->bind_result($rented)) {
    echo "Binding result failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  $stmt->fetch();

  mysqli_close($mysqli);

  if ($rented) {
    $new_rented = 0;
  }
  else {
    $new_rented = 1;
  }

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("UPDATE video_store SET rented = ? WHERE id = ?"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  if (!$stmt->bind_param("ii", $new_rented, $id)) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  else {
    mysqli_close($mysqli);
    if ($new_rented == 0) {
      header("Location: /assignment4-part2/src/?action=check-in");
    }
    else {
      header("Location: /assignment4-part2/src/?action=check-out");
      exit;
    }
  }
}


function deleteMovie($id) {

  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("DELETE FROM video_store WHERE id = ?"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  if (!$stmt->bind_param("i", $id)) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  else {
    mysqli_close($mysqli);
    header("Location: /assignment4-part2/src/?action=remove");
  }
}


function addMovie() {

  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("INSERT INTO video_store (name, category, length) VALUES (?,?,?)"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  $lowerCat = strtolower($_POST['category']);

  if (!$stmt->bind_param("ssi", $_POST['name'], $lowerCat, $_POST['length'])) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  else {
    mysqli_close($mysqli);
    header("Location: /assignment4-part2/src/?action=add");
  }
}


function showTable() {

  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  ?>
  <table>
    <caption>My Movies</caption>
    <thead>
      <tr>
        <th></th><th>Title</th><th>Category</th><th>Length (mins.)</th><th>Status</th><th>Check-in/<br>Check-out</th><th>Delete</th>
      </tr>
    </thead>

    <?php
    if (!isset($_GET['category-list']) || $_GET['category-list'] === 'all') {
      if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM video_store"))) {
        echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
      }
    }
    else {
      if (!($stmt = $mysqli->prepare("SELECT id, name, category, length, rented FROM video_store WHERE category = ?"))) {
        echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
      }

      if (!$stmt->bind_param("s", $_GET['category-list'])) {
        echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
      }
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
      echo '<td><form action="scripts.php" method="post"><button name="checkout" value="' . $out_id . '">&#10004;</button>';
      echo '</td></form>';
      echo '<td><form action="scripts.php" method="post"><button name="delete" value="' . $out_id . '">X</button>';
      echo '</form></td></tr>';

    }
    echo "</tbody>";
    ?>
  </table>
<?php
  mysqli_close($mysqli);
}

function getCategories() {
  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("SELECT category FROM video_store"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  $category = NULL;
  $categories = array();

  if (!$stmt->bind_result($category)) {
    echo "Binding output params failed: (" . $stmt->errno . ") " . $stmt->error;
  }

  while ($stmt->fetch()) {
    array_push($categories, $category);
  }

  $categories = array_unique($categories);
  $categories = array_values($categories);

  echo '<form action="index.php" id="category-form" method="get">';
  echo '<select name="category-list">';
  echo '<option value="all" selected>All Categories</option>';
  $len = count($categories);
  for ($i = 0; $i < $len; $i++) {
    echo '<option value="' . $categories[$i] . '">' . $categories[$i] . '</option>';
  }
  echo "</select>";
  echo '<input type="submit" value="Filter"></form>';


  mysqli_close($mysqli);
}


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

