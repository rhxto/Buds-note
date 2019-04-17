<?php
require 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      echo 'nonAN';
      die();
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["Username"]) || $_POST["Username"] == "" || strlen($_POST["Username"]) > 30) {
      echo 'nonAN';
      die();
    } else {
      $Username = test_input($_POST["Username"]);
      if (mysqlChckUsr("localhost", "system", "the_best_admin_passwd", $Username)) {
        echo 'usernameEsiste';
        die();
      }
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "" || strlen($_POST["Password"]) > 30) {
      echo 'nonAN';
      die();
    } else {
      $Password = test_input($_POST["Password"]);
    }
    if (empty($_POST["Mail"]) || $_POST["Mail"] == "" || strlen($_POST["Mail"]) > 50) {
      echo 'nonAN';
      die();
    } else {
      //@ e . non sono html special chars
      $Mail = test_input($_POST["Mail"]);
    }
    if($Password != $_POST["Password"] || $Username != $_POST["Username"] || $Mail != $_POST["Mail"]) {
      echo 'nonAN';
      die();
    }
    $Username = hash("sha256", $Username);
    $Password = hash("sha256", $Password);
    $LastLog =  '"' . date("Y-m-d H:i:s") . '"';
    $UsernameDb = '"' . $Username . '"';
    $PasswordDb = '"' . $Password . '"';
    $accLvl = 0;
    $fail_acc = 0;
    $status = mysqlWriteCrd("localhost", "system", "the_best_admin_passwd", $UsernameDb, $PasswordDb, $Mail, $accLvl, $fail_acc, $LastLog);
    if ($status == NULL) {
      echo 'passed';
    } else if ($status){
      echo 'internalError';
    } else {
      echo 'usernameEsiste';
    }
  }
  ?>
