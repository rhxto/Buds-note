<?php
  require_once "core.php";

  /*
   * Funzione per creare delle entry nel journalctl (log live per debug)
   *
   * @param $s stringa da inserire
  */
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
    $query = $conn->prepare("SELECT fail_acc FROM user WHERE username = :username");
    $query->bindParam(":username", $usr);
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $query->execute();
    $acc = $query->fetchAll();
    if($acc[0]["fail_acc"] <= 5){
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

  /*
   * Esegue il login e restituisce una stringa con il feedback e se sbagliata la pw aggiorna il fail_acc
   *
   * @param $cnfUsr Lo username dello username da testare
   * @param $cnfPw La password hashata da testare
   *
   * @return "true" Se esiste lo username e la pw corrisponde (fail_acc azzerati)
   * @return "false" Se esiste lo username ma la password è sbagliata, o se non esiste lo username
   * @return "bannato" Se uno ha raggiunto il fail_acc limite e viene bannato
   * @return "internalError" Se c'é stata una PDOException
   */
  function login(String $cnfUsr, String $cnfPw) : String {
    require_once 'ips.php';
    try {
      $conn = connectDb();
      $query = $conn->prepare("SELECT * FROM user WHERE username = :username");
      $query->bindParam(":username", $cnfUsr);
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $query->execute();
      $userinfo = $query->fetchAll();
      if (!empty($userinfo[0]['username'])) {
        if(accLimit($userinfo[0]["username"], $conn)) {
          if ($userinfo[0]["pw"] == $cnfPw) {
            $query = $conn->prepare("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
            return "true";
          } else {
            $query = $conn->prepare("UPDATE user SET fail_acc = fail_acc+1 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
            return "false";
          }
        } else {
          if ($userinfo[0]["pw"] == $cnfPw) {
            $query = $conn->prepare("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
	          return "true";
	        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            blockIp($ip, $conn, $cnfUsr);
            return 'bannato';
          }
        }
      } else {
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

  /*
   * La funzione ritorna il livello di accesso di un utente
   *
   * @param $user Lo username dell'utente del quale si vuole sapere l'acc_lvl
   *
   * @return Il numero corrispondente all'acc_lvl
   */
  function getAcclvl($user) {
    try {
      $conn = connectDb();
      $getLvl = $conn->prepare("SELECT acc_lvl FROM user WHERE username = :usr");
      $getLvl->bindParam(":usr", $user);
      $getLvl->execute();
      $result = $getLvl->fetchAll();
      return $result[0]["acc_lvl"];
    } catch(PDOException $e) {
      PDOError($e);
      return "IEAG";
    } finally {
      $conn = null;
    }
  }

  /*
   * Serve ad attivare o disattivare lo stato manutenzione
   *
   * @param $val Il valore a cui voglio settare manutenzione (TRUE per attivata, FALSE per disattivata)
   *
   * @return "done" Se la query di modifica è andata a buon $fine
   * @return "MANAA" Se lo stato era già attivato
   * @return "MANAT" Se lo stato era già disattivato
   * @return "IEMANS" Se viene sollevato un'PDOException
   */
  function setManStatus(bool $val) {
    if (getManStatus() && $val == true) {
      return "MANAA";
    } elseif (!getManStatus() && $val != true) {
      return "MANAT";
    } else {
      try {
        $conn = connectDb();
        $query = $conn->prepare("UPDATE manutenzione SET val = :val");
        $val = (int)$val;
        $query->bindParam(':val', $val);
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

  /*
   * Serve a ritornare il valore manutenzione
   *
   * @return true Se è attivata la manutenzione
   * @return false Se è disattivata la manutenzione
   */
  function getManStatus() {
    try {
      $conn = connectDb();
      $query = $conn->query("SELECT val FROM manutenzione");
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      if ($result[0]["val"] == 1) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      PDOError($e);
      return "IEMANR";
    } finally {
      $conn = null;
    }
  }

  /*
   * Prende due date sotto forma di stringhe e restituisce la differenza
   *
   * @param inizio La data iniziale
   * @param fine La data finale
   *
   * @return La differenza fra due date riportate come int
   */
  function differenzaData($inizio, $fine){
    $inizio = strtotime($inizio);
    $fine = strtotime($fine);

    return ($fine - $inizio);
  }

?>
