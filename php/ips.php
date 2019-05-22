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
        if (empty($ip[0]["ip"])) {
          return true;
        } else {
          return false;
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

    //controlla che l'ip non sia già bannato in ban_ext_ip e se ha raggiunto i 5 tentativi di accesso senza successo lo metto negli ip bannati senza username
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
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    //viene chiamata tutte le volte che un utente si logga e cancella da ban_ext_ip gli indirizzi bannati se la differenza fra l'ora di ban e il momento in cui viene chiamata è > di 10 min
    function checkBannedIps($conn) {
      try {
        $getIps = $conn->query("SELECT ip FROM ban_ext_ip ORDER BY ip");
        $getIps->setFetchMode(PDO::FETCH_ASSOC);
        $ips = $getIps->fetchAll();
        $ip = array();
        foreach ($ips as $tmp) {
          array_push($ip, $tmp['ip']);
        }
        foreach($ip as $ipa) {
          $query = $conn->prepare("SELECT date FROM ban_ext_ip WHERE ip = :ip");
          $query->bindParam(":ip", $ipa);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $dates = $query->fetchAll();
          $diff = differenzaData($dates[0]['date'], date("Y-m-d H:i:s"));
        if ($diff >= 600) {
            mysqlUnbanExtIp($conn, $ipa);
          }
        }
      } catch(PDOException $e) {
        require 'exceptions.php';
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    //sbanna un ip senza username nella tabella ban_ext_ip
    function mysqlUnbanExtIp($conn, String $ip) {
      try {
        $query = $conn->prepare("DELETE FROM ban_ext_ip WHERE ip = :ip");
        $query->bindParam(":ip", $ip);
        $query->execute();
      } catch(PDOException $e) {
        PDOError($e);
        $conn = null;
        die();
      } finally {
        $conn = null;
      }
    }

    //viene chiamata tutte le volte che un utente si logga e cancella da ban_ip gli indirizzi bannati se la differenza fra l'ora di ban e il momento in cui viene chiamata è > di 10 min
    function loginCheck($ip) {
      require "funs.php";
      try {
        $conn = new PDO("mysql:host=localhost;dbname=Buds_db", "checkBan", "bansEER"); //per questioni di sicurezza
        $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("USE Buds_db;");
        if (mysqlCheckIp($ip, $conn)) {
          $query = $conn->prepare("SELECT date FROM ban_ip WHERE ip = :ip");
          $query->bindParam(":ip", $ip);
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $banDate = $query->fetchAll();
          $diff = differenzaData($banDate[0]['date'], date("Y-m-d H:i:s"));
          if ($diff >= 600) {
            $valid = true;
          } else {
            $valid = false;
          }
          if($valid) {
            $query = $conn->query("SELECT user FROM ban_ip WHERE ip = :ip");
            $query->bindParam(":ip", $ip);
            $query->execute();
            $query->setFetchMode(PDO::FETCH_ASSOC);
            $usr = $query->fetchAll();
            if ($usr[0]['user'] == NULL) {
              $usr = "null";
            } else {
              $usr = $usr['user'];
            }
            mysqlUnbanIp($conn, $ip, $uer);
          } else {
            die("<script>window.location.href = '../ban/'</script>");
          }
        }
      } catch(PDOException $e) {
        PDOError($e);
        $conn = null;
        die();
      } finally {
        checkBannedIps($conn);
        $conn = null;
      }
    }
?>
