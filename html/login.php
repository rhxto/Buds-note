<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  //se il broswer mette il login in cache l'accesso non é bloccato, questo disabilita il caching.
  require '../php/ips.php';
  require_once '../php/timeFuns.php';
  $ip = $_SERVER['REMOTE_ADDR'];
  try {
    $conn = new PDO("mysql:host=localhost;dbname=Buds_db", "checkBan", "bansEER");
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("USE Buds_db;");
    if (mysqlCheckIp($ip, $conn)) {
      $ip = '"' . $ip . '"';
      $getIps = $conn->query("SELECT date FROM ban_ip WHERE ip = $ip");
      $getIps->setFetchMode(PDO::FETCH_ASSOC);
      $banDate = $getIps->fetchAll();
      $banDate = $banDate[0];
      $diff = differenzaData($banDate['date'], date("Y-m-d H:i:s"));
      if ($diff >= 600) {
        $valid = true;
      } else {
        $valid = false;
      }
      if($valid) {
        $getUsr = $conn->query("SELECT user FROM ban_ip WHERE ip = $ip");
        $getUsr->setFetchMode(PDO::FETCH_ASSOC);
        $usr = $getUsr->fetchAll();
        $usr = $usr[0];
        if ($usr['user'] == NULL) {
          $user = "null";
        } else {
          $user = $usr['user'];
        }
        mysqlUnbanIp($conn, $ip, $user);
      } else {
        die("<p>Too many login attempts, retry 10 minutes after your ban</p>");
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
    checkBannedIps($conn);
    $conn = null;
  }
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form di accesso</title>
    <script src="jquery.min.js"></script>
    <script src="login.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheets/form.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/positions.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
  </head>
  <body>

    <div>
      <p class="warning" id="Warning" name="Warning"></p>
    </div>

    <div id="login">
      <form name="form" action="../php/read.php" method="post">
        <div class="field top10 left15 width65">
          <span class="icon">C</span>
          <input type="text" name="Username" placeholder="Username" id="Username" class="txtinput" required autocomplete="off"/>
        </div>
        <div class="field top30 left15 width65">
          <span class="icon">v</span>
          <input type="password" name="Password" placeholder="Password" id="Password" class="txtinput " required autocomplete="off"/>
        </div>
      </form>
      <button onclick="testInput()" id="btn" class="button top50 left30 width40 height10">LOGIN</button>
      <button onclick="window.location.href='register.php'" id="btn" class="button top70 left30 width40 height10">Crea un account</button>
    </div>
  </body>
</html>
<?php
  echo "stato login: " . $_SESSION['logged_in'];
 ?>
