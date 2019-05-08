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
  require "query_funs.php";
  $conn = connectDb();
  if ($type == "deptName") {
    $response = dept($conn, $phrase, NULL);
    echo $response[1][0];
  } elseif ($type == "deptNum") {
    $response = dept($conn, NULL, $phrase);
    echo $response[1][0];
  } else {
    die("Invalid search type");
  }
?>
