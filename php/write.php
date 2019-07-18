<?php
require 'funs.php';
  function test_input($data, $mail) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "" || (strpos($data, ".") !== false && !$mail) || strpos($data, "/") !== false || strpos($data, "'") !== false || strpos($data, ")") !== false) {
      //il controllo del . e dello / server perché se uno si chiama tipo ../ e crea/modifica/etc.. qualcosa come le note la costruzione del percorso si screwa
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
      $Username = test_input($_POST["Username"], false);
      if (mysqlChckUsr($Username)) {
        echo 'usernameEsiste';
        die();
      }
    }

    if (empty($_POST["Password"]) || $_POST["Password"] == "" || strlen($_POST["Password"]) > 30) {
      echo 'nonAN';
      die();
    } else {
      $Password = test_input($_POST["Password"], false);
    }
    if (empty($_POST["Mail"]) || $_POST["Mail"] == "" || strlen($_POST["Mail"]) > 50) {
      echo 'nonAN';
      die();
    } else {
      //@ e . non sono html special chars
      $Mail = test_input($_POST["Mail"], true);
    }
    if($Password != $_POST["Password"] || $Username != $_POST["Username"] || $Mail != $_POST["Mail"]) {
      echo 'nonAN';
      die();
    }
    $Password = hash("sha256", $Password);
    $Mail = hash("sha256", $Mail);
    $LastLog =  date("Y-m-d H:i:s");
    //$UsernameDb = '"' . $Username . '"';
    //$PasswordDb = '"' . $Password . '"';
    $accLvl = 0;
    $fail_acc = 0;
    $status = mysqlWriteCrd($Username, $Password, $Mail, $accLvl, $fail_acc, $LastLog);
    //ok, non so perché ma per avere l'output in $r al posto che farlo andare chissá dove (in genere va in ${APACHE_LOG_DIR}) doobbiamo mettere 2>&1 alla fine... bho?
    if ($status == "passed") {
      exec("mkdir ../notedb/$Username 2>&1", $r);
      exec("mkdir ../notedb/$Username/uploads 2>&1", $r);
      exec("chmod o-x ../notedb/$Username 2>&1", $r);
      exec("chmod g-x ../notedb/$Username 2>&1", $r);
      exec("chmod o-x ../notedb/$Username/uploads 2>&1", $r);
      exec("chmod g-x ../notedb/$Username/uploads 2>&1", $r);
      if (!empty($r)) {
        error_log("Risposta da comando shell di write insapettata! Risposta: " . print_r($r, true));
        die('UWFE');
      }
      echo 'passed';
    } elseif ($status == "ge") {
      echo 'usernameEsiste';
    } else {
      echo "UWIE";
    }
  }
  ?>
