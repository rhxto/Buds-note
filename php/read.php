<?php session_start();
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    //il controllo del . e dello / server perché se uno si chiama tipo ../ e crea/modifica/etc.. qualcosa come le note la costruzione del percorso si screwa
    if ($data == "" || strpos($data, ".") !== false || strpos($data, "/") !== false || strpos($data, "'") !== false || strpos($data, ")") !== false) {
      echo 'nonAN';
      die();
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
  $status = login($Username, $Password);
  if ($status == "true") {
    $_SESSION['logged_in'] = '1'; //1 = loggato, NULL o 0 no.
    $_SESSION["username"] = $Username;
    echo 'passed';
  } else if ($status == "false"){
    $_SESSION['logged_in'] = '0';
    unset($_SESSION["username"]);
    echo 'credenziali';
  } else if ($status == 'bannato') {
    $_SESSION['logged_in'] = '0';
    unset($_SESSION["username"]);
    echo 'bannato';
  } else {
    $_SESSION['logged_in'] = '0';
    unset($_SESSION["username"]);
    echo 'internalError';
  }
?>
