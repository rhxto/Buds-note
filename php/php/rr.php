<?php
/*
try {
    $conn = new PDO("mysql:host=$server;dbname=Buds_db", $username, $password);
    echo "Connected successfully to mysql!";
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Excepion errmode set!";
    $conn->exec("USE Buds_db;");
    echo "Database selected successfully!";
    $getUsers = $conn->exec("SELECT * FROM user ORDER BY username");
    $getUsers->setFetchMode(PDO::FETCH_ASSOC);
    $users = $getUsers->fetchAll();
    foreach ($users as $user) {
      echo $user['username'] . '<br />';
    }
    foreach ($users as $pswd) {
      echo $pswd['pw'] . '<br/>';
    }
  }
 */
  echo date("Y-m-d");
?>
