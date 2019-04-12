<?php
//buono3
  function logD(String $s) {
    shell_exec("logger $s");
  }

  function accLimit($usr, $pw, $conn){
    $usr = '"' . $usr . '"';
    $pw = '"' . $pw . '"';
    $accs = $conn->query("SELECT fail_acc FROM user WHERE username = $usr");
    $accs->setFetchMode(PDO::FETCH_ASSOC);
    $acc = $accs->fetchAll();
    $fail_acc = $acc[0];
    if($fail_acc['fail_acc'] <= 5){
      return true;
    } else {
      return false;
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
      require 'exceptions.php';
      $exist = err_handler($e->getCode(), $e->getMessage());
      if (!$exist) {
        die("<h1>Errore interno</h1>");
      } else {
        die();
      }
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
            return true;
          } else {
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET fail_acc = fail_acc+1 WHERE username = $cnfUsr");
            echo 'Incorrect username or password!';
            return false;
          }
        } else {
          require 'ips.php';
          $ip = $_SERVER['REMOTE_ADDR'];
          blockIp($ip, $conn);
          echo "<h3>Too many login attempts!</h3>";
          return false;
        }
      } else {
        echo 'Incorrect username or password!';
        $cnfUsr = '"' . $cnfUsr . '"';
        $conn->exec("UPDATE user SET fail_acc = fail_acc+1 WHERE username = $cnfUsr");
      }
    } catch(PDOException $e) {
      require 'exceptions.php';
      $exist = err_handler($e->getCode(), $e->getMessage());
      if (!$exist) {
        die("<h1>Errore interno</h1>");
      } else {
        die();
      }
    } finally {
      $conn = null;
    }
  }
  function mysqlRemoveIp(String $server, String $username, String $password, String $ip) {
    try {
      $conn = new PDO("mysql:host=$server;dbname=Buds_db", $username, $password);
      //echo "Connected successfully to mysql!";
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      //echo "Excepion errmode set!";
      $conn->exec("USE Buds_db;");
      //echo "Database selected successfully!";
      $conn->exec("DELETE FROM blocked_ips (ip) WHERE ip = $ip");
      //echo "Done!";
    } catch(PDOException $e) {
      require 'exceptions.php';
      $exist = err_handler($e->getCode(), $e->getMessage());
      if (!$exist) {
        die("<h1>Errore interno</h1>");
      } else {
        die();
      }
    } finally {
      $conn = null;
    }
  }
  function mysqlChckUsr(String $server, String $username, String $passowrd, String $Username) {
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
      foreach ($users as $user) {
	      array_push($utenti, $user['username']);
      }
      if (in_array($cnfUsr, $utenti)) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      require 'exceptions.php';
      $exist = err_handler($e->getCode(), $e->getMessage());
      if (!$exist) {
        die("<h1>Errore interno</h1>");
      } else {
        die();
      }
    } finally {
      $conn = null;
    }
  }
  function usernameAlreadyExists($Username) {
    $Username = '"' . $Username . '"';
    mysqlChckUsr("localhost", "system", "the_best_admin_passwd", $Username);
  }

?>
