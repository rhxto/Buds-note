<?php
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      error_log("Username or password invalid after script chars trim, ignoring.");
      die("Invalid username or password");
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      error_log("POST username value null, ignoring.");
      die("Invalid username or password");
    } else {
      $Username = test_input($_POST["Username"]);
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      error_log("POST password vale null, ignoring.");
      die("Invalid username or password");
    } else {
      $Password = test_input($_POST["Password"]);
    }
  }

  $UsernameDb = '"' . $Username . '"';
  $PasswordDb = '"' . $Password . '"';
  mysqlUsr("localhost", "system", "the_best_admin_passwd", $usernameDb, $passwordDb);
?>
