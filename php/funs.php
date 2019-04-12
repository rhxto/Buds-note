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
          require 'timeFuns.php';
          $ip = $_SERVER['REMOTE_ADDR'];
          if (mysqlCheckIp($ip, $conn)) {
            $ip = '"' . $ip . '"';
            $getIps = $conn->query("SELECT date FROM ban_ip WHERE ip = $ip");
            $getIps->setFetchMode(PDO::FETCH_ASSOC);
            $banDate = $getIps->fetchAll();
            $diff = differenzaData($banDate, date("Y-m-d H:i:s"));
            if ($diff >= 600) {
              $valid = true;
            } else {
              $valid = false;
            }
            if($valid) {
              mysqlUnbanIp($conn, $ip, $cnfUsr);
            }
          } else {
            blockIp($ip, $conn, $cnfUsr);
            echo "<h3>Too many login attempts!</h3>";
            return false;
          }
        }
      } else {
        require 'ips.php';
        echo 'Incorrect username or password!';
        blockIp($ip, $conn, "null");
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
  function mysqlChckUsr(String $server, String $username, String $password, String $Username) : bool{
    $Username = hash("sha256", $Username);
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
      if (in_array($Username, $utenti)) {
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
?>
