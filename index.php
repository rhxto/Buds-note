<?php
  require 'php/ips.php';
  $ip = $_SERVER['REMOTE_ADDR'];
  try {
    $conn = new PDO("mysql:host=localhost;dbname=Buds_db", "checkBan", "bansEER");
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("USE Buds_db;");
    if (mysqlCheckIp($ip, $conn)) {
      echo "<p>Too many login attempts, retry 10 minutes after your ban</p>";
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
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Buds_note</title>
    <link rel="stylesheet" type="text/css" href="html/stylesheets/positions.css" />
    <link rel="stylesheet" type="text/css" href="html/stylesheets/main.css" />
  </head>
  <body>
    <p style="position:sticky;top:0px;width:100%">
    <div class="navbar" id="navbar">
      <a href="index.html" class="navbar-left">HOME</a>
      <a href="html/login.php" class="navbar-right">LOGIN</a>
      <a href="html/register.php" class="navbar-right">REGISTER</a>
    </div>
      </p>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
    <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
  </body>
</html>
