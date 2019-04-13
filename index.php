<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Buds_note</title>
    <script src="html/jquery.min.js"></script>
    <script src="html/session.js"></script>
    <link rel="stylesheet" type="text/css" href="html/stylesheets/positions.css" />
    <link rel="stylesheet" type="text/css" href="html/stylesheets/main.css" />
  </head>
  <body>
    <p style="position:sticky;top:0px;width:100%">
      <div class="navbar" id="navbar">
        <a href="index.php" class="navbar-left">HOME</a>
        <a href="html/login.php" class="navbar-right log">LOGIN</a>
        <a href="html/register.php" class="navbar-right log">REGISTER</a>
        <button id="logout" class="navbar-right logout">LOGOUT</button>
      </div>
    </p>
  </body>
</html>
<?php
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true);</script>";
    } else {
      session_unset();  //quando si esegue il logout logged_in é settato e != da 1 quindi sappiamo che é stato eseguito il logout
      session_destroy();
      echo "<script>$('.logout').attr('hidden', true);</script>";
    }
  } else {
    echo "<script>$('.logout').attr('hidden', true);</script>";
    //se chiudiamo la sessione anche quando uno non é loggato, non riusciamo a settare logged_in a 1
  }
 ?>
