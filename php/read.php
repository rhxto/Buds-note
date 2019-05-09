<?php session_start();
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      echo 'nonAN';
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
    die();
  }
  if ($_POST["Password"] != $Password) {
    echo 'nonAN';
    die();
  }
  $Password = hash("sha256", $Password);
  $status = mysqlRetrieveCrd("localhost", "system", "the_best_admin_passwd", $Username, $Password);
  if ($status == "true") {
    $_SESSION['logged_in'] = '1'; //1 = loggato, NULL o 0 no.
    error_log("sessione attivata!");
    echo 'passed';
  } else if ($status == "false"){
    $_SESSION['logged_in'] = '0';
    echo 'credenziali';
  } else if ($status == 'bannato') {
    $_SESSION['logged_in'] = '0';
    echo 'bannato';
  } else {
    $_SESSION['logged_in'] = '0';
    echo 'internalError';
  }
?>
