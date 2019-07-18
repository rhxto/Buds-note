<?php session_start();
  require_once 'funs.php';
  if (isset($_SESSION["username"]) && getAcclvl($_SESSION["username"]) == 1) {
    function test_input($data) {
      $data = trim($data);
      $data = stripslashes($data);
      $data = htmlspecialchars($data);
      if ($data == "") {
        die(json_encode("IEMAN"));
      }
      return $data;
    }
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      if (empty($_POST["valman"]) || $_POST["valman"] == "") {
              die(json_encode("IEMAN"));
      } else {
        $manval = test_input($_POST["valman"]);
        if ($_POST["valman"] == "true") {
          $set = true;
        } else {
          $set = false;
        }
        $r = setManStatus($set);
        if ($r == "done") {
          echo json_encode("done");
        } elseif ($r == "MANAA") {
          echo json_encode("MANAA");
        } elseif ($r == "MANAT") {
          echo json_encode("MANAT");
        } else {
          echo json_encode("IEMANS");
        }
      }
    }
  } else {
    die(json_encode("NOMAN"));
    error_log("**PAGINA DI MANUTENZIONE TRIGGERATA SENZA PRIVILEGI NECESSARI** ip: " + $_SERVER["REMOTE_ADDR"]);
  }
 ?>
