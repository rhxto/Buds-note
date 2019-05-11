<?php
//buono3
  require_once "core.php";
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
    $Email = '"' . $Email . '"';
    try {
      $conn = connectDb();
      $conn->exec("INSERT INTO user (username, pw, mail, acc_lvl, fail_acc, last_log) VALUES ($usernameDb, $passwordDb, $Email, $accLvl, $fail_acc, $date)");
      return "passed";
    } catch(PDOException $e) {
      PDOError($e);
      return "internalError";
    } finally {
      $conn = null;
    }
  }

  function mysqlRetrieveCrd(String $server, String $username, String $password, String $cnfUsr, String $cnfPw) : String {
    try {
      $conn = connectDb();
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
        if(accLimit($cnfUsr, $cnfPw, $conn)) {
          if (in_array($cnfPw, $passwords)) {
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = $cnfUsr");
            return "true";
          } else {
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET fail_acc = fail_acc+1 WHERE username = $cnfUsr");
            return "false";
          }
        } else {
          //se un utente é sbannato ma i tentativi di login sono 6 allora non puó funzionare il reset di fail_acc
          if (in_array($cnfPw, $passwords)) {
            $cnfUsr = '"' . $cnfUsr . '"';
            $conn->exec("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = $cnfUsr");
	          return "true";
	        } else {
            require 'ips.php';
            $ip = $_SERVER['REMOTE_ADDR'];
            blockIp($ip, $conn, $cnfUsr);
            return 'bannato';
          }
        }
      } else {
        require 'ips.php';
        $ip = $_SERVER['REMOTE_ADDR'];
        if(blockIpTmp($ip, $conn)) {
          return 'bannato';
        } else {
          return "false";
        }
      }
    } catch(PDOException $e) {
      PDOError($e);
      return "internalError";
    } finally {
      $conn = null;
    }
  }
  function mysqlChckUsr(String $server, String $username, String $password, String $Username) : bool {
    try {
      $conn = connectDb();
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
      PDOError($e);
    } finally {
      $conn = null;
    }
  }
  function getAcclvl($user) {
    $conn = connectDb();
    $getLvl = $conn->prepare("SELECT acc_lvl FROM user WHERE username = :usr");
    $getLvl->bindParam(":usr", $user);
    $getLvl->execute();
    $result = $getLvl->fetchAll();
    return $result[0]["acc_lvl"];
  }
  function setManStatus($val) {
    if (getManStatus() == "true" && $val == "true") {
      return "MANAA";
    } elseif (getManStatus() == "false" && $val != "true") {
      return "MANAT";
    } else {
      try {
        $conn = connectDb();
        $query = $conn->prepare("UPDATE manutenzione SET VAL = :val");
        if ($val == "true") {
          $v = 1;
          //usare un bindParam con 1 qui e 0 nell'else da errore perché bindParam vuole una variabile
        } else {
          $v = 0;
        }
        $query->bindParam(':val', $v);
        $query->execute();
        return "done";
      } catch(PDOException $e) {
        PDOError($e);
        return "IEMANS";
      } finally {
        $conn = null;
      }
    }
  }
  function getManStatus() {
    try {
      $conn = connectDb();
      $query = $conn->query("SELECT VAL FROM manutenzione");
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      if ($result[0]["VAL"] == 1) {
        return "true";
      } else {
        return "false";
      }
    } catch(PDOException $e) {
      PDOError($e);
      return "IEMANR";
    } finally {
      $conn = null;
    }
  }
  function differenzaData($inzio, $fine){
    $inzio = strtotime($inzio);
    $fine = strtotime($fine);

    return ($fine - $inzio);
  }

?>
