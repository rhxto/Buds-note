<?php
  require '../php/ips.php';
  $ip = $_SERVER['REMOTE_ADDR'];
  try {
    $conn = new PDO("mysql:host=localhost;dbname=Buds_db", "checkBan", "bansEER");
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("USE Buds_db;");
    if (mysqlCheckIp($ip, $conn)) {
      die("<p>Too many login attempts, retry 10 minutes after your ban</p>");
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form di registrazione</title>
    <script src="jquery.min.js"></script>
    <script src="register.js"></script>
    <link rel="stylesheet" type="text/css" href="stylesheets/form.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/positions.css" />
    <link rel="stylesheet" type="text/css" href="stylesheets/main.css" />
  </head>
  <body>

    <div class="warning">
      <p id="Warning" name="Warning"></p>
    </div>

    <div id="login" style="height:50%">
      <form name="form" action="../php/write.php" method="post">
        <div class="field top10 left15 width65">
          <span class="icon">C</span>
          <input type="text" name="Username" placeholder="Username" id="Username" class="txtinput" required autocomplete="off"/>
        </div>
        <div class="field top30 left15 width65">
          <span class="icon">v</span>
          <input type="password" name="Password" placeholder="Password" id="Password" class="txtinput" required autocomplete="off"/>
        </div>
        <div class="field top50 left15 width65">
          <span class="icon">v</span>
          <input type="password" name="Conferma Password" placeholder="Conferma Password" id="Conferma_Password" class="txtinput" required autocomplete="off"/>
        </div>
        <div class="field top70 left15 width65" >
          <span class="icon" style="left: -45px">l</span>
          <input type="email" name="Mail" placeholder="Email" id="Email" class="txtinput" required autocomplete="off">
          </input>
        </div>
      </form>
      <button onclick="testInput()" id="btn" class="button top85 left30 width40 height10">REGISTRATI</button>
    </div>
  </body>
</html>
