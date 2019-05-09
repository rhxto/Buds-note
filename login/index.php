<?php
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  //se il broswer mette il login in cache l'accesso non é bloccato, questo disabilita il caching.
  require '../php/ips.php';
  $ip = $_SERVER['REMOTE_ADDR'];
  loginCheck($ip);
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Form di accesso</title>
    <script src="../jquery/jquery.min.js"></script>
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
      <button onclick="window.location.href='../register/'" id="btn" class="button top70 left30 width40 height10">Crea un account</button>
    </div>
  </body>
</html>
