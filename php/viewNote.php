<?php

  require_once 'core.php';
  require_once 'query_funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    //il controllo del . e dello / server perché se uno si chiama tipo ../ e crea/modifica/etc.. qualcosa come le note la costruzione del percorso si screwa
    if ($data == "") {
      die(json_encode("NOTESNV"));
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (empty($_GET["title"])) {
      die(json_encode("NOTESUT"));
    } else {
      $title = test_input($_GET["title"]);
      echo "Titolo: " . $title . "<br/>";
      echo "Contenuto: " . getNote(connectDb(), $title);
    }
  }

 ?>
