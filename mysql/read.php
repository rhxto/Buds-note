<?php

$mysqlServer = "localhost";
$mysqlUser = "Federico";
$mysqlPasswd = "mysqlPassDsk1000";

// Create connection
try {
  $conn = new PDO("mysql:host=$mysqlServer;dbname=Logins", $mysqlUser, $mysqlPasswd);
  echo "Connected successfully to mysql!";
  $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  echo "Excepion errmode set!";
  $conn->exec("USE Logins;");
  echo "Database selected successfully!";
  $getUsers = $conn->exec("SELECT * FROM Logins ORDER BY Username");
  $getUsers->setFetchMode(PDO::FETCH_ASSOC);
  $users = $getUsers->fetchAll();
  foreach ($users as $user) {
    echo $user . '<br />';
  }
} catch(PDOException $e) {
  echo "Connection failure: " . $e->getMessage();
} finally {
  $conn = null;
}

?>
