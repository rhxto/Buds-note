<?php
require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      error_log("Data invalid after script chars trim, ignoring.");
      die("Invalid username, password or email");
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
    if (empty($_POST["Mail"]) || $_POST["Mail"] == "") {
      error_log("Unvalid email");
      die("Invalid email");
    } else {
      $Mail = $_POST["Mail"];
    }
  }
  $Username = hash("sha256", $Username);
  $Password = hash("sha256", $Password);
  $LastLog =  date("Y-m-d");
  $UsernameDb = '"' . $Username . '"';
  $PasswordDb = '"' . $Password . '"';
  $accLvl = 0;
  mysqlWriteCrd("localhost", "system", "the_best_admin_passwd", $UsernameDb, $PasswordDb, $Mail, $accLvl, $LastLog);
  echo "<script>window.location.href = ../html/login.html</script>";
?>
