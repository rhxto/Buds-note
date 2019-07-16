<!-- in sviluppo -->
<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Buds-note</title>
    <script src="../jquery/jquery.min.js"></script>
    <script src="../main/viewUser.js"></script>
    <link rel="stylesheet/less"  type="text/css" href="../main/stylesheets/main.less"></link>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ></script>
  </head>
  <body>
    <p style="position:sticky;top:0px;width:100%">
      <div class="navbar" id="navbar">
        <a href="../" class="navbar-left">HOME</a>
        <a href="../login/" class="navbar-right log">LOGIN</a>
        <a href="../register/" class="navbar-right log">REGISTER</a>
        <a id="logout" onclick="logout()"  class="navbar-right logout">LOGOUT</a>
      </div>
      <span id="greet">
        <?php
          if (isset($_SESSION["logged_in"])) {
            if ($_SESSION["logged_in"] === "1") {
              echo "Benvenuto, " . $_SESSION["username"];
            }
          }
        ?>
      </span>
    </p>
    <?php
      require_once "funs.php";
      if (($username = test_input($_GET["username"])) !== $_GET["username"]) {
        echo "<h1>Username non valido, controlla i caratteri speciali</h1>";
      } else {
        if (!isset($_SESSION["username"]) || $_SESSION["logged_in"] === "0" || !isset($_SESSION["logged_in"])) {
          echo "<h2>Devi eseguire il login per cercare un utente!</h2>";
          $display = false;
        } else if (isset($_SESSION["username"]) && $_SESSION["logged_in"] === "1" && mysqlChckUsr($username)) {
          $display = true;
        } else {
          echo "<h1>Errore interno sconosciuto! Riferisci questo messaggio agli amministratori immediatamente Codice: USEREMERG</h1>";
          $display = false;
        }
      }
     ?>
     <div id="userInfo">
       Nome utente: <span id="username" class="userInfo">
         <?php
           echo $username;
          ?>
       </span><br/>
       Ultimo accesso: <span id="lastLog" class="userInfo">
         <?php
           echo getLastLog($username);
          ?>
       </span><br/>
       Valutazioni totali lasciate: <?php
           echo getLeftRate($username);
          ?>
       </span><br/>
       Commenti pubblicati: <span id="commenti" class="userInfo">
         <?php
           echo getLeftComm($username);
          ?>
       </span><br/>
       Note pubblicate: <span id="notePubblicate" class="userInfo">
         <?php
           echo getNoteNum($username);
          ?>
       </span>
     </div>
  </body>
</html>
<?php
  if(gettype($m = getManStatus()) === "string") {
    echo "<script>error($m);</script>";
  } elseif ($m == true) {
    echo "<script>error('man');</script>";
  }
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true); $('#scriviNotaBtn').show();</script>";
      /*if(getAcclvl($_SESSION["username"]) == 1) {
        echo "<script>$('.adminTools').show(); $('.admin').show();</script>";
      }*/
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
