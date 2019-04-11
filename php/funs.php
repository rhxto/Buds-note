<?php
//buono
  function logD(String $s) {
    shell_exec("logger $s");
  }

  function accLimit($usr, $pw, $conn){
    $usr = '"' . $usr . '"';
    $pw = '"' . $pw . '"';
    $accs = $conn->query("SELECT fail_acc FROM user WHERE (username = $usr) AND (pw = $pw)");
    $accs->setFetchMode(PDO::FETCH_ASSOC);
    $acc = $accs->fetchAll();
    print_r($acc);
    $fail_acc = $acc[0];
    if($fail_acc['fail_acc'] <= 5){
      return true;
    } else {
      return false;
      error_log("**POSSIBILE ATTACCO BRUTE FORCE**");
    }
  }

  function mysqlWriteCrd(String $server, String $username, String $password, String $usernameDb, String $passwordDb, String $Email, int $accLvl, int $fail_acc, String $date) {
    try {
      $conn = new PDO("mysql:host=$server;dbname=Buds_db", $username, $password);
      //echo "Connected successfully to mysql!";
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //echo "Excepion errmode set!";
      $conn->exec("USE Buds_db;");
      //echo "Database selected successfully!";
      $conn->exec("INSERT INTO user (username, pw, mail, acc_lvl, fail_acc, last_log) VALUES ($usernameDb, $passwordDb, $Email,$accLvl, $fail_acc, $date)");
      //echo "Done!";
    } catch(PDOException $e) {
      echo "<h1>Errore interno</h1>";
      error_log($e->getMessage());
      die();
    } finally {
      $conn = null;
    }
  }

  function mysqlRetrieveCrd(String $server, String $username, String $password, String $cnfUsr, String $cnfPw) {
    try {
      $conn = new PDO("mysql:host=$server;dbname=Buds_db", $username, $password);
      //echo "Connected successfully to mysql!";
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //echo "Excepion errmode set!";
      $conn->exec("USE Buds_db;");
      //echo "Database selected successfully!";
      $getUsers = $conn->query("SELECT * FROM user ORDER BY username");
      $getUsers->setFetchMode(PDO::FETCH_ASSOC);
      $users = $getUsers->fetchAll();
      $utenti = array();
      $passwords = array();
      foreach ($users as $user) {
	      array_push($utenti, $user['username']);
        array_push($passwords, $user['pw']);
      }
      if (in_array($cnfUsr, $utenti)) {
        if(accLimit($cnfUsr, $cnfPw, $conn)){
          if (in_array($cnfPw, $passwords)) {
            echo "Logged in!";
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = $cnfUsr");
          } else {
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET fail_acc = fail_acc+1 WHERE username = $cnfUsr");
            die("Incorrect username or password!");
          }
        } else {
         die("<h3>Too many login attempts!</h3>");
          $cnfUsr = '"' . $cnfUsr . '"';
          die("Incorrect username or password!");
        }
      } else {
        die("Incorrect username or password!");
        $conn->exec("UPDATE user SET fail_acc = fail_acc+1 WHERE username = $cnfUsr");
      }
    } catch(PDOException $e) {
      echo "<h1>Errore interno</h1>";
      error_log($e->getMessage());
      die();
    } finally {
      $conn = null;
    }

  }

?>
