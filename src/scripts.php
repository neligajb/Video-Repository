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
  else if (isset($_POST['delete-all'])) {
    deleteAllMovies();
  }
  else {    // else we are attempting to add a movie

    if ($_POST['name'] == '' || $_POST['name'] == NULL || !is_string($_POST['name'])) {
      $error = urlencode("Entry must have a title.");
      header("Location: /~neliganj/assignment4-part2/?action=$error");
    }

    $ok_chars = array(' ', '/', '-');

    if (($_POST['category'] != '' || $_POST['category'] != NULL) &&
         !ctype_alpha(str_replace($ok_chars, '', $_POST['category']))) {
      $error = urlencode("Category must consist only of letters, '-' and '/'.");
      header("Location: /~neliganj/assignment4-part2/?action=$error");
    }
    else if (($_POST['length'] != '' || $_POST['length'] != NULL) &&
              !ctype_digit($_POST['length']) || $_POST['length'] < 0 || $_POST['length'] > 999) {
      $error = urlencode('Length must be a number 0 - 999.');
      header("Location: /~neliganj/assignment4-part2/?action=$error");
    }
    else {
      addMovie();
    }
  }
}

function actionMessage() {
  if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
      echo '<div ><p class="notify">' . urldecode($_GET['action']) . '</p></div>';
    }
  }
}


function deleteAllMovies() {
  global $password;
  global $db_address;
  global $db_user;
  global $db_name;

  $mysqli = new mysqli($db_address, $db_user, $password, $db_name);
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
  }

  if (!($stmt = $mysqli->prepare("DELETE FROM video_store"))) {
    echo "Prepared statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }

  if (!$stmt->execute()) {
    echo "Execute statement failed: (" . $mysqli->errno . ") " . $mysqli->error;
  }
  else {
    mysqli_close($mysqli);
    $message = urlencode('All movies deleted.');
    header("Location: /~neliganj/assignment4-part2/?action=$message");
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
      $message = urlencode('Movie checked in.');
    }
    else {
      $message = urlencode('Movie checked out.');
    }
    header("Location: /~neliganj/assignment4-part2/?action=$message");
    exit;
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
    $message = urlencode('Movie deleted.');
    header("Location: /~neliganj/assignment4-part2/?action=$message");
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
    $message = urlencode("Movie added.");
    header("Location: /~neliganj/assignment4-part2/?action=$message");
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
    <caption><h3>My Movies</h3></caption>
    <thead>
      <tr>
        <th>Title</th><th>Category</th><th>Length (mins.)</th><th>Status</th><th>Check-in/<br>Check-out</th><th>Delete</th>
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
      if ($out_length === 0) {
        $out_length = '';
      }
      echo "<tr><td>$out_name</td><td>$out_cat</td><td>$out_length</td>";
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
    if ($category === '') {
      //do nothing
    }
    else {
      array_push($categories, $category);
    }
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
  echo '<input class="button" id="filter" type="submit" value="Filter"></form>';


  mysqli_close($mysqli);
}


?>

