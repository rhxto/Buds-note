<?php session_start();
if (isset($_POST['action']) && $_POST['action'] == "logout") {
  if ($_SESSION['logged_in'] = '1') {
    $_SESSION['logged_in'] = '0';
    echo true;
  } else {
    $ipL = $_SERVER['REMOTE_ADDR'];
    error_log("**LOGOUT ESEGUITO SENZA ESSERSI LOGGATO** IP:$ipL");
    echo false;
  }
} else {
  $ipL = $_SERVER['REMOTE_ADDR'];
  error_log("**LOGOUT ESEGUITO SENZA ESSERSI LOGGATO** IP:$ipL");
  echo false;
}
?>
