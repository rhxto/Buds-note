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
    if (!isset($_POST["type"]) || $_POST["type"] == "") {
      die();
    } else {
      $type = test_input($_POST["type"]);
    }
  }
  if ((!isset($_POST["phrase"]) || $_POST["phrase"] == "") && $type != "subjs" && $type != "notes" && $type != "depts") {
    $phrase = NULL;
  } else {
    $phrase = test_input($_POST["phrase"]);
    $phrase = str_replace("'", "sc-a", $phrase);
    $phrase = str_replace('"', "sc-q", $phrase);
  }

  if ($type == "note"){
    if ((!isset($_POST["title"]) || $_POST["title"] == "") || $_POST["title"] == "undefined") {
      $title =  NULL;
    } else {
      $title = test_input($_POST["title"]);
      $title = str_replace("'", "sc-a", $title);
      $title = str_replace('"', "sc-q", $title);
      if ($_POST["title"] != $title) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["user"]) || $_POST["user"] == "") || $_POST["user"] == "undefined") {
      $user =  NULL;
    } else {
      $user = test_input($_POST["user"]);
      if ($_POST["user"] != $user) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["subj"]) || $_POST["subj"] == "") || $_POST["subj"] == "undefined") {
      $subj =  NULL;
    } else {
      $subj = test_input($_POST["subj"]);
      if ($_POST["subj"] != $subj) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["year"]) || $_POST["year"] == "") || $_POST["year"] == "undefined") {
      $year =  NULL;
    } else {
      $year = test_input($_POST["year"]);
      if ($_POST["year"] != $year) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["dept"]) || $_POST["dept"] == "") || $_POST["dept"] == "undefined") {
      $dept =  NULL;
    } else {
      $dept = test_input($_POST["dept"]);
      if ($_POST["dept"] != $dept) {
        //echo 'nonAN';
        die();
      }
    }

    if ((!isset($_POST["datefrom"]) || $_POST["datefrom"] == "") || $_POST["datefrom"] == "undefined") {
      $datefrom =  NULL;
    } else {
      $datefrom = test_input($_POST["datefrom"]);
      if ($_POST["datefrom"] != $datefrom) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["dateto"]) || $_POST["dateto"] == "") || $_POST["dateto"] == "undefined") {
      $dateto =  NULL;
    } else {
      $dateto = test_input($_POST["dateto"]);
      if ($_POST["dateto"] != $dateto) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["order"]) || $_POST["order"] == "") || $_POST["order"] == "undefined") {
      $order =  NULL;
    } else {
      $order = test_input($_POST["order"]);
      if ($_POST["order"] != $order) {
        //echo 'nonAN';
        die();
      }
    }
    if ((!isset($_POST["orderby"]) || $_POST["orderby"] == "") || $_POST["orderby"] == "undefined") {
      $orderby =  NULL;
    } else {
      $orderby = test_input($_POST["orderby"]);
      if ($_POST["orderby"] != $orderby) {
        //echo 'nonAN';
        die();
      }
      if ($orderby == "Titolo") {
        $orderby = "title";
      } elseif ($orderby == "Username") {
        $orderby = "user";
      } elseif ($orderby == "Materia") {
        $orderby = "subj";
      } elseif ($orderby == "Anno") {
        $orderby = "year";
      } elseif ($orderby == "Indirizzo") {
        $orderby = "dept";
      } elseif ($orderby == "Data") {
        $orderby = "date";
      } else {
        $orderby = NULL;
      }
    }
  }
  require_once "core.php";
  require_once "funs.php";
  $conn = connectDb();
  if ($type == "deptName") {
    $response = dept($conn, $phrase, NULL);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[1][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "deptNum") {
    $response = dept($conn, NULL, $phrase);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "depts") {
    $response = dept($conn, NULL, NULL);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
   }  elseif ($type == "subjName") {
    $response = subj($conn, $phrase, NULL);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "subjNum") {
    $response = subj($conn, NULL, $phrase);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "subjs") {
    $response = subj($conn, NULL, NULL);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
        echo json_encode($response);
    }
  } elseif ($type == "noteTtl") {
    $response = searchNote($conn, $phrase, NULL, NULL, NULL, NULL, NULL, NULL, "date", "desc");
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0]["title"])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "noteDept") {
    $response = searchNote($conn, NULL, NULL, NULL, NULL, NULL, $phrase, NULL, NULL, "date", "desc");
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0]["title"])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "notes") {
    $response = searchNote($conn, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "date", "desc");
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0]["title"])) {
      echo json_encode("Nrt");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "note") {
    $response = searchNote($conn, $title, NULL, $user, $subj, $year, $dept, $datefrom, $dateto, $orderby, $order);
    if ($response == "internalError") {
      die(json_encode("IES"));
    }
    if (!isset($response[0]["title"])) {
      echo json_encode("Nrt");
    } else {
      echo json_encode($response);
    }
  } else {
    die(json_encode("Invalid search type"));
  }
?>
