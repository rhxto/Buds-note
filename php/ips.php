<?php
  /*
   * Blocca un ip e se stava provando a loggarsi con uno user blocca anche lo user
   *
   * @param $ip Líp dell'utente che sta cercando di entrare
   * @param $conn La connessione con cui stiamo lavorando
   * @param $user Il nome dell'utente con cui sta provando a loggarsi, se non si sta provando a loggare con alcun nome utente bisogna mettere "null"
   *
   */
  function blockIp(String $ip, $conn, String $user) {
      try {
        $query = $conn->prepare("INSERT INTO ban_ip (ip, date) VALUES (:ip, NOW())");
        $query->bindParam(":ip", $ip);
        $query->execute();
        if ($user != "null") {
          $query = $conn->prepare("UPDATE ban_ip SET user = :user WHERE ip = :ip");
          $query->bindParam(":user", $user);
          $query->bindParam(":ip", $ip);
          $query->execute();
        }
      } catch(PDOException $e) {
        logD("errore blockip");
        PDOError($e);
        $conn = null;
        die();
      } finally {
          $conn = null;
      }
    }

    /* Cancella l'ip dalla tabella ban_ip e se c'è anche user allora porta il fail_acc dello user a 0
     *
     * @param $conn La connessione su cui stiamo lavorando
     * @param $ip L'indirizzo ip che desideriamo sbannare
     * @param $user Il nome dello user del quale riportare fail_acc a 0, se non è collegato a nessuno suer allora inserire "null"
     *
     */
    function mysqlUnbanIp($conn, String $ip, String $user) {
      try {
        $query = $conn->prepare("DELETE FROM ban_ip WHERE ip = :ip");
        $query->bindParam(":ip", $ip);
        $query->execute();
        if ($user != "null") {
          $query = $conn->prepare("UPDATE user SET fail_acc = 0 WHERE username = :user");
          $query->bindParam(":user", $user);
          $query->execute();
        }
      } catch(PDOException $e) {
        logD("errore unbanip");
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    /* Controlla se l'ip è fra quelli di ban_ip
     *
     * @param $ip L'ip che dobbiamo controllare
     * @param $conn La connessione su cui stiamo lavorando
     *
     * @return true Se l'ip è fra quelli bannati
     * @return false Se l'ip non è fra quelli bannati
     */
    function mysqlCheckIp(String $ip, $conn) : bool {
      try {
        $query = $conn->prepare("SELECT * FROM ban_ip WHERE ip = :ip");
        $query->bindParam(":ip", $ip);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $ips = $query->fetchAll();
        if (empty($ips[0]["ip"])) {
          return false;
        } else {
          return true;
        }
      } catch(PDOException $e) {
        logD("errore in mysqlcheckip");
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }


    /*
     * Aggiunge l'ip a ban_ext_ip o na incrementa il try e se ha raggiunto i 5 try lo mette ion ban_ip senza uno username
     *
     * @param $ip L'ip che ha provato ad accedere
     * @param $conn La connesione con cui stiamo lavorando
     *
     * @return true Se l'ip ha raggiunto i 5 try e viene spostato nei ban_ip
     * @return false Se non ha raggiunto i 5 try nonostante l'incremento o se non era in ban_ext_ip ed è stato aggiunto
     */
    function blockIpTmp(String $ip, $conn) {
      try {
        $query = $conn->prepare("SELECT ip FROM ban_ext_ip WHERE ip = :ip");
        $query->bindParam(":ip", $ip);
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $query->execute();
        $ips = $query->fetchAll();
        if (!empty($ips[0]["ip"])) {
          $query = $conn->prepare("UPDATE ban_ext_ip SET try = try+1 WHERE ip = :ip");
          $query->bindParam(":ip", $ip);
          $query->execute();
          $query = $conn->prepare("SELECT try FROM ban_ext_ip WHERE ip = :ip");
          $query->bindParam(":ip", $ip);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $trys = $query->fetchAll();
          if ($trys[0]['try'] >= 5) {
            blockIp($ip, $conn, "null");
            return true;
          } else {
            return false;
          }
        } else {
          $query = $conn->prepare("INSERT INTO ban_ext_ip (ip, date, try) VALUES (:ip, NOW(), 1)");
          $query->bindParam(":ip", $ip);
          $query->execute();
          return false;
        }
      } catch(PDOException $e) {
        logD("errore blockiptmp");
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    //viene chiamata tutte le volte che un utente si logga e cancella da ban_ext_ip gli indirizzi bannati se la differenza fra l'ora di ban e il momento in cui viene chiamata è > di 10 min
    /*
     * Controlla gli ip bannati in ban_ext_ip e se l'ultimo try risale a più di 10 minuti fa viene sbloccato l'ip (la funzione viene chiamata tutte le volte che si logga un utente)
     *
     * @param $conn La connessione con cui stiamo lavorando
     */
    function checkBannedIps($conn) {
      try {
         $query = $conn->prepare("SELECT ip FROM ban_ext_ip WHERE TIMESTAMPDIFF(SECOND, date, NOW()) >= 600");
         $query->setFetchMode(PDO::FETCH_ASSOC);
         $query->execute();
         $ips = $query->fetchAll();
         foreach($ips as $row) {
            mysqlUnbanExtIp($conn, $row['ip']);
        }
      } catch(PDOException $e) {
        require 'exceptions.php';
        logD("errore checkbannedips");
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    /*
     * Rimuove dalla tabella ban_ext_ip un'ip, quindi lo sbanna
     *
     * @param $conn La connessione con cui stiamo lavorando
     * @param $ip L'ip che vogliamo sbloccare
     */
    function mysqlUnbanExtIp($conn, String $ip) {
      try {
        $query = $conn->prepare("DELETE FROM ban_ext_ip WHERE ip = :ip");
        $query->bindParam(":ip", $ip);
        $query->execute();
      } catch(PDOException $e) {
        PDOError($e);
        logD("errore unbanextip");
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    //viene chiamata tutte le volte che un utente si logga e cancella da ban_ip gli indirizzi bannati se la differenza fra l'ora di ban e il momento in cui viene chiamata è > di 10 min
    /*
     * Rimuove dalla tabella ban_ip tutti gli ip che sono stati bannati più di 10 minuti fa, rispetto al momento in cui viene chiamata (viene chiamata tutte le volte che un utente si logga)
     *
     * @param $ip
     *
     */
    function loginCheck($ip) {
      require "funs.php";
      try {
        $conn = new PDO("mysql:host=localhost;dbname=Buds_db", "checkBan", "bansEER"); //per questioni di sicurezza
        $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("USE Buds_db;");
        if (mysqlCheckIp($ip, $conn)) {
          logD("check: $ip");
          $query = $conn->prepare("SELECT user FROM ban_ip WHERE (TIMESTAMPDIFF(SECOND, ban_ip.date, NOW()) >= 600) AND (ip = :ip)");
          $query->bindParam(":ip", $ip);
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $query->execute();
          $user = $query->fetchAll();
          if(empty($user)){
            die("<script>window.location.href = '../ban/'</script>");
          }else{
            if($user[0]['user'] == NULL){
              mysqlUnbanIp($conn, $ip, "null");
            } else {
              mysqlUnbanIp($conn, $ip, $user[0]['user']);
            }
          }
        }
      } catch(PDOException $e) {
        logD("errore logincheck");
        PDOError($e);
        $conn = null;
        die();
      } finally {
        checkBannedIps($conn);
        $conn = null;
      }
    }
?>
