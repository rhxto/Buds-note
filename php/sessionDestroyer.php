<?php session_start();
if (isset($_POST['action']) && $_POST['action'] == "logout") {
  $_SESSION['logged_in'] = '0';
  echo "fatto";
} else {
  echo "sessione inesistente";
}

?>
