<?php
  require 'funs.php';
  if ($_SERVER["REQUEST_METHOD"] == "POST" && $_SERVER["REMOTE_ADDR"] == "localhost") {
    logD("Ip valido per unban");
    if (empty($_POST["ip"]) || $_POST["ip"] == "") {
      logD("**IP VUOTO MA LA RICHIESTA DI UNBAN É VALIDA**");
      die();
    } else {
      $ip = '"' . $_POST["ip"] . '"';
      mysqlRemoveIp("localhost", "system", "the_best_admin_passwd", $ip);
    }
} else {
  logD("**unbanIp.php VISITATA SENZA AUTORIZZAZIONE NECESSARIA**");
  die("<h1>You aren't authorized to visit this page, this incident will be reported.</h1>");
}
?>
