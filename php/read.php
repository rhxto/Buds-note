<?php session_start();
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      echo 'false';
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      echo 'nonAN';
    } else {
      $Username = test_input($_POST["Username"]);
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      echo 'nonAN';
    } else {
      $Password = test_input($_POST["Password"]);
    }
  }
  if($_POST["Username"] != $Username) {
    echo 'nonAN';
  }
  if ($_POST["Password"] != $Password) {
    echo 'nonAN';
  }
  $Username = hash("sha256", $Username);
  $Password = hash("sha256", $Password);
  if (mysqlRetrieveCrd("localhost", "system", "the_best_admin_passwd", $Username, $Password)) {
    echo 'passed';
    $_SESSION['logged_in'] = '1'; //1 = loggato, NULL no.
  } else {
    echo 'credenziali';
  }
?>
