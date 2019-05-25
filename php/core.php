<?php
  function connectDb(){
    $DBHOST = "localhost";
    $DBNAME = "Buds_db";
    $DBUSRN = "system";
    $DBPW = "the_best_admin_passwd";

    try {
      $conn = new PDO("mysql:host=$DBHOST;dbname=$DBNAME", $DBUSRN, $DBPW);
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $conn->exec("USE Buds_db;");
    } catch(PDOException $e) {            //Sistemare esteticamente le prossime 10 righe (fai una function)
      PDOError($e);
    } finally {
      return $conn;
      $conn = null;
    }
  }
  function PDOError($e) {
    require 'exceptions.php';
    $exist = err_handler($e->getCode(), $e->getMessage());
    if (!$exist) {
      return false;
    } else {
      return true;
    }
  }
?>
