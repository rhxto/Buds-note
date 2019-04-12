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
          echo "ip non listato";
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
