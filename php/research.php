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
    if (empty($_POST["type"]) || $_POST["type"] == "") {
      //echo 'nonAN';
    } else {
      $type = test_input($_POST["type"]);
    }
  }
  if ((empty($_POST["phrase"]) || $_POST["phrase"] == "") && $type != "subjs" && $type != "notes" && $type != "depts") {
    $phrase = NULL;
  } else {
    $phrase = test_input($_POST["phrase"]);
  }

  if ($type == "note"){
    if ((empty($_POST["title"]) || $_POST["title"] == "") || $_POST["title"] == "undefined") {
      $title =  NULL;
    } else {
      $title = test_input($_POST["title"]);
      if ($_POST["title"] != $title) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["user"]) || $_POST["user"] == "") || $_POST["user"] == "undefined") {
      $user =  NULL;
    } else {
      $user = test_input($_POST["user"]);
      if ($_POST["user"] != $user) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["subj"]) || $_POST["subj"] == "") || $_POST["subj"] == "undefined") {
      $subj =  NULL;
    } else {
      $subj = test_input($_POST["subj"]);
      if ($_POST["subj"] != $subj) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["year"]) || $_POST["year"] == "") || $_POST["year"] == "undefined") {
      $year =  NULL;
    } else {
      $year = test_input($_POST["year"]);
      if ($_POST["year"] != $year) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["dept"]) || $_POST["dept"] == "") || $_POST["dept"] == "undefined") {
      $dept =  NULL;
    } else {
      $dept = test_input($_POST["dept"]);
      if ($_POST["dept"] != $dept) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["teacher"]) || $_POST["teacher"] == "") || $_POST["teacher"] == "undefined") {
      $teacher =  NULL;
    } else {
      $teacher = test_input($_POST["teacher"]);
      if ($_POST["teacher"] != $techer) {
        //echo 'nonAN';
        die();
      }
    }
    if ((empty($_POST["date"]) || $_POST["date"] == "") || $_POST["date"] == "undefined") {
      $date =  NULL;
    } else {
      $date = test_input($_POST["date"]);
      if ($_POST["date"] != $date) {
        //echo 'nonAN';
        die();
      }
    }
  }
  require_once "core.php";
  require_once "query_funs.php";
  require_once "funs.php";
  $conn = connectDb();
  if ($type == "deptName") {
    $response = dept($conn, $phrase, NULL);
    if (empty($response[1][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "deptNum") {
    $response = dept($conn, NULL, $phrase);
    if (empty($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "depts") {
    $response = dept($conn, NULL, NULL);
    if (empty($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
   }  elseif ($type == "subjName") {
    $response = subj($conn, $phrase, NULL);
    if (empty($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "subjNum") {
    $response = subj($conn, NULL, $phrase);
    if (empty($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "subjs") {
    $response = subj($conn, NULL, NULL);
    if (empty($response[0][0])) {
      echo json_encode("Nrt.");
    } else {
        echo json_encode($response);
    }
  } elseif ($type == "noteTtl") {
    $response = searchNote($conn, $phrase, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "date", "desc");
    if (empty($response[0]["title"])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "noteDept") {
    $response = searchNote($conn, NULL, NULL, NULL, NULL, NULL, $phrase, NULL, NULL, "date", "desc");
    if (empty($response[0]["title"])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "notes") {
    $response = searchNote($conn, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "date", "desc");
    if (empty($response[0]["title"])) {
      echo json_encode("Nrt.");
    } else {
      echo json_encode($response);
    }
  } elseif ($type == "note") {
    $response = searchNote($conn, $title, NULL, $user, $subj, $year, $dept, $teacher, $date, "date", "desc");
    logD("Title: ". $title);
    logD("user: ". $user);
    logD("subj: ". $subj);
    logD("year: ". $year);
    logD("dept: ". $dept);
    logD("teacher: ". $teacher);
    logD("date: ". $date);
    logD("Result:" . $response[0]["title"]);
    if (empty($response[0]["title"])) {
      echo json_encode("Nrt");
    } else {
      echo json_encode($response);
    }
  } else {
    die("Invalid search type");
  }
?>
