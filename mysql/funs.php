<?php
  function logD(String $s) {
    shell_exec("logger $s");
  }
  function mysqlUsr(String $server, String $username, String $password, String $usernameDb, String $passwordDb) {
    try {
      $conn = new PDO("mysql:host=$server;dbname=Buds_db", $username, $password);
      echo "Connected successfully to mysql!";
      $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      echo "Excepion errmode set!";
      $conn->exec("USE Buds_db;");
      echo "Database selected successfully!";
      $conn->exec("INSERT INTO user (username, pw) VALUES ($usernameDb, $passwordDb)");
      echo "Done!";
    } catch(PDOException $e) {
      echo "Connection failure: " . $e->getMessage();
    } finally {
      $conn = null;
    }
  }
?>
