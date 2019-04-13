<?php
require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      echo '<script>window.location.href = "../html/register.php?errore=nonAN"</script>';
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "") {
      echo '<script>window.location.href = "../html/register.php?errore=nonAN"</script>';
    } else {
      $Username = test_input($_POST["Username"]);
      if (mysqlChckUsr("localhost", "system", "the_best_admin_passwd", $Username)) {
        echo '<script>window.location.href = "../html/register.php?errore=usernameEsiste"</script>';
      }
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "") {
      echo '<script>window.location.href = "../html/register.php?errore=nonAN"</script>';
    } else {
      $Password = test_input($_POST["Password"]);
    }
    if (empty($_POST["Mail"]) || $_POST["Mail"] == "") {
      echo '<script>window.location.href = "../html/register.php?errore=nonAN"</script>';
    } else {
      $Mail = '"' . $_POST["Mail"] . '"';
    }
    $Username = hash("sha256", $Username);
    $Password = hash("sha256", $Password);
    $LastLog =  '"' . date("Y-m-d H:i:s") . '"';
    $UsernameDb = '"' . $Username . '"';
    $PasswordDb = '"' . $Password . '"';
    $accLvl = 0;
    $fail_acc = 0;
    mysqlWriteCrd("localhost", "system", "the_best_admin_passwd", $UsernameDb, $PasswordDb, $Mail, $accLvl, $fail_acc, $LastLog);
    echo '<script>window.location.href = "../html/login.php"</script>';
  }
  ?>
