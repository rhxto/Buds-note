<?php
  if ($_SERVER["REMOTE_ADDR"] != "localhost") {
    logD("**ips.php VISITATA SENZA AUTORIZZAZIONE NECESSARIA**");
    die("<h1>You aren't authorized to visit this page, this incident will be reported.</h1>");
  }
  function blockIp($ip, $conn) {
      require 'funs.php';
      try {
        $conn->exec("USE Buds_db;");
        $ip = '"' . $ip . '"';
        $conn->exec("INSERT INTO blocked_ips (ip) VALUES ($ip)");
      } catch(PDOException $e) {
        error_log($e->getMessage());
        die();
      } finally {
          $conn = null;
      }
      echo "<script>setTimeout(unbanIp($ip), 10000*60);</script>";
    }
?>
<html>
  <head>
    <meta charset="utf-8" />
    <script>
      function unbanIp(ip) {
        document.getElementById["inpt"].value = ip;
        document.forms["form"].submit();
      }
    </script>
  </head>
  <body>
    <h1>You aren't authorized to visit this page, this incident will be reported.</h1>
    <form name="form" action="unbanIp.php" method="post" hidden>
      <input type="text" id="inpt" name="ip"/>
    </form>
  </body>
</html>
