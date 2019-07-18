<?php
  require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die("Invalid username, password or email");
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      die("Invalid username or password");
    } else {
      $Username = test_input($_POST["Username"]);
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      die("Invalid username or password");
    } else {
      $Password = test_input($_POST["Password"]);
    }
  }
  if ($_POST["Username"] != $Username) {
    die("Invalid username");
  }
  if ($_POST["Password"] == $Password) {
    die("Invalid password");
  }
  $Username = hash("sha256", $Username);
  $Password = hash("sha256", $Password);
  mysqlRetrieveCrd("localhost", "system", "the_best_admin_passwd", $Username, $Password);
?>
