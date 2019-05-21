<?php

  require_once "core.php";
  function logD(String $s) {
    shell_exec("logger $s");
  }

/*
 * Funzione per verificare se l'utente non ha superato i 5 failed access
 *
 * @param $usr Lo username dell'utente da ricercare
 * @param $conn La connessione che sto usando per comunicare con il DB
 *
 * @return true Se l'utente ha 5 o meno di 5 failed access
 * @return false Se l'utente ha più di 5 failed access
 */
  function accLimit($usr, $conn){
    $usr = '"' . $usr . '"';
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

  /*
   * Aggiunge un utente con i parametri passati
   *
   * @param $username Lo username che si vuole dare al nuovo user
   * @param $password La password che si vuole dare al nuovo user
   * @param $email La mail che si vuole collegare al nuovo user
   * @param $acc_lvl L'acc_lvl che si vuole assegnare al nuovo user
   * @param $fail_acc Il numero di fail_acc che si vogliono attribuire al nuovo user
   * @param $last_log La data dell'ultimo log dell'utente
   *
   * @return "passed" Se tutto è andato bene
   * @return "internalError" Se manca username o password o email o se viene sollevato una PDOException durente il binding o quando viene lanciata la query
   */
  function mysqlWriteCrd(String $username, String $password, String $email, int $acc_lvl, int $fail_acc, String $last_log) {
    //$email = '"'.$email.'"';
    if(($username == " ") || ($password == " ") || ($email == " ")){
	    return "internalError";
    }
    if(($fail_acc<0) || ($fail_acc>5)){
	    $fail_acc = 0;
    }
    try {
      $conn = connectDb();
      $query = $conn->prepare("INSERT INTO user (username, pw, mail, acc_lvl, fail_acc, last_log) VALUES (:username, :password, :email, :acc_lvl, :fail_acc, :last_log)");
      $query->bindParam(":username", $username);
      $query->bindParam(":password", $password);
      $query->bindParam(":email", $email);
      $query->bindParam(":acc_lvl", $acc_lvl);
      $query->bindParam(":fail_acc", $fail_acc);
      $query->bindParam(":last_log", $last_log);
      $query->execute();
      return "passed";
    } catch(PDOException $e) {
      if (PDOError($e)) {
        return "ge";
      } else {
        return "internalError";
      }
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
        if(accLimit($cnfUsr, $conn)) {
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

  /*
   * La funzione serve a verificare che uno user con il dato username sia presente nel DB
   *
   * @param $username Lo username che deve avere lo user nel DB
   *
   * @return true Se il dato username è contenuto nel DB
   * @return false Se il dato username non è presente nel DB
   */
  function mysqlChckUsr(String $username) : bool {
    try {
      $conn = connectDb();
      $query = $conn->prepare("SELECT username FROM user WHERE username LIKE :username");
      $query->bindParam(":username", $username);
      /*
      *$getLvl->execute();
      $result = $getLvl->fetchAll();
      return $result[0]["acc_lvl"];
      */
      $query->execute();
      $users = $query->fetchAll();
      if (!empty($users)) {
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
        $query = $conn->prepare("UPDATE manutenzione SET val = :val");
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
      $query = $conn->query("SELECT val FROM manutenzione");
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      if ($result[0]["val"] == 1) {
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
