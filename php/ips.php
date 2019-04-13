<?php
  function blockIp(String $ip, $conn, String $user) {
      try {
        $conn->exec("USE Buds_db;");
        $ip = '"' . $ip . '"';
        $data = '"' . date("Y-m-d H:i:s") . '"';
        $conn->exec("INSERT INTO ban_ip (ip, date) VALUES ($ip, $data)");
        if ($user != "null") {
          $user = '"' . $user . '"';
          $conn->exec("UPDATE ban_ip SET user = $user WHERE ip = $ip");
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
    function mysqlUnbanIp($conn, String $ip, String $user) {
      try {
        $conn->exec("DELETE FROM ban_ip WHERE ip = $ip");
        if ($user != "null") {
          $user = '"' . $user . '"';
          $conn->exec("UPDATE user SET fail_acc = 0 WHERE username = $user");
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
    function mysqlCheckIp(String $ipCnf, $conn) : bool {
      try {
        $getIps = $conn->query("SELECT * FROM ban_ip ORDER BY ip");
        $getIps->setFetchMode(PDO::FETCH_ASSOC);
        $ips = $getIps->fetchAll();
        $ip = array();
        foreach ($ips as $tmp) {
  	      array_push($ip, $tmp['ip']);
        }
        if (in_array($ipCnf, $ip)) {
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
    function blockIpTmp(String $ipCnf, $conn) {
      $ipd = '"' . $ipCnf . '"';
      try {
        $getIps = $conn->query("SELECT ip FROM ban_ext_ip ORDER BY ip");
        $getIps->setFetchMode(PDO::FETCH_ASSOC);
        $ips = $getIps->fetchAll();
        $ip = array();
        foreach ($ips as $tmp) {
          array_push($ip, $tmp['ip']);
        }
        if (in_array($ipCnf, $ip)) {
          $conn->exec("UPDATE ban_ext_ip SET try = try+1 WHERE ip = $ipd");
          $getTrys = $conn->query("SELECT try FROM ban_ext_ip WHERE ip = $ipd");
          $getTrys->setFetchMode(PDO::FETCH_ASSOC);
          $trys = $getTrys->fetchAll();
          $trys = $trys[0];
          if ($trys['try'] >= 5) {
            blockIp($ipCnf, $conn, "null");
          }
        } else {
          $d = '"' . date("Y-m-d H:i:s") . '"';
          $conn->exec("INSERT INTO ban_ext_ip (ip, date, try) VALUES ($ipd, $d, 1)");
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
    function checkBannedIps($conn) {
      //per avere la differenza fra le date servirebbe timeFuns ma il require é giá eseguito su login.php
      try {
        $getIps = $conn->query("SELECT ip FROM ban_ext_ip ORDER BY ip");
        $getIps->setFetchMode(PDO::FETCH_ASSOC);
        $ips = $getIps->fetchAll();
        $ip = array();
        foreach ($ips as $tmp) {
          array_push($ip, $tmp['ip']);
        }
        foreach($ip as $ipa) {
          $ipMysql = $ipa;
          $ipa = '"' . $ipa . '"';
          $getDates = $conn->query("SELECT date FROM ban_ext_ip WHERE ip = $ipa");
          $getDates->setFetchMode(PDO::FETCH_ASSOC);
          $dates = $getDates->fetchAll();
          $date = $dates[0];
          $diff = differenzaData($date['date'], date("Y-m-d H:i:s"));
          if ($diff >= 600) {
            mysqlUnbanExtIp($conn, $ipMysql);
          }
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
      function mysqlUnbanExtIp($conn, String $ip) {
        try {
          $ip = '"' . $ip . '"';
          $conn->exec("DELETE FROM ban_ext_ip WHERE ip = $ip");
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
