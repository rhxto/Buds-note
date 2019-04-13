<?php session_start();
if (isset($_POST['action']) && $_POST['action'] == "logout") {
  $_SESSION['logged_in'] = '0';
}
?>
