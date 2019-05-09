<?php
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      //echo 'nonAN';
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["phrase"]) || $_POST["phrase"] == "") {
      //echo 'nonAN';
    } else {
      $phrase = test_input($_POST["phrase"]);
    }

    if (empty($_POST["type"]) || $_POST["type"] == "") {
      //echo 'nonAN';
    } else {
      $type = test_input($_POST["type"]);
    }
  }
  if($_POST["phrase"] != $phrase) {
    //echo 'nonAN';
    die();
  }
  if ($_POST["type"] != $type) {
    //echo 'nonAN';
    die();
  }
  require "core.php";
  require "query_funs.php";
  $conn = connectDb();
  if ($type == "deptName") {
    $response = dept($conn, $phrase, NULL);
    if (empty($response[1][0])) {
      echo "Nessun risultato trovato.";
    } else {
      foreach ($response as $attr) {
        echo $attr[0];
        echo "<br/>";
      }
    }
  } elseif ($type == "deptNum") {
    $response = dept($conn, NULL, $phrase);
    if (empty($response[0][0])) {
      echo "Nessun risultato trovato.";
    } else {
      foreach ($response as $attr) {
        echo $attr[0];
        echo "<br/>";
      }
    }
  } elseif ($type == "subjName") {
    $response = subj($conn, $phrase, NULL);
    if (empty($response[0][0])) {
      echo "Nessun risultato trovato.";
    } else {
      foreach ($response as $attr) {
        echo $attr[0];
        echo "<br/>";
      }
    }
  } elseif ($type == "subjNum") {
    $response = subj($conn, NULL, $phrase);
    if (empty($response[0][0])) {
      echo "Nessun risultato trovato.";
    } else {
      foreach ($response as $attr) {
        echo $attr[0];
        echo "<br/>";
      }
    }
  } elseif ($type = "noteTtl") {
    $response = searchNote($conn, $phrase, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
    if (empty($response[0][0])) {
      echo "Nessun risultato trovato.";
    } else {
      foreach ($response as $attr) {
        echo $attr[0];
        echo "<br/>";
      }
    }
  } else {
    die("Invalid search type");
  }
?>
