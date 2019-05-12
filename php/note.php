<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  require_once 'query_funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die(json_encode("NOTENV"));
    }
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1' && isset($_POST["type"])) {
    $type = test_input($_POST["type"]);
    if ((empty($_POST["title"]) || empty($_POST["content"]) || empty($_POST["subj"]) || empty($_POST["dept"])) && $type == "write")  {
      die(json_encode("NOTENV"));
    } elseif ($type == "write") {
      $title = test_input($_POST["title"]);
      $content = test_input($_POST["content"]);
      $subj = test_input($_POST["subj"]);
      $dept = test_input($_POST["dept"]);
    }
    if ((empty($_POST["type"]) || empty($_POST["title"])) && $type == "delete") {
      die(json_encode("NOTENV"));
    } elseif ($type == "delete") {
      $title = test_input($_POST["title"]);
    }
    switch ($type) {
      case 'write':
        if(writeNote(connectDb(), $title, $_SESSION["username"], $subj, $dept, $content)) {
          echo json_encode("done");
        } else {
          die(json_encode("NOTEW"));
        }
        break;
      case 'update':
        //updateNote($title, $content)
        break;
      case 'delete':
        if (getAcclvl($_SESSION["username"]) == 1) {
          if (delNote(connectDb(), $title, $_SESSION["username"])) {
            echo json_encode("done");
          } else {
            echo json_encode("NOTEDE");
          }
        } else {
          error_log("**TENTATIVO DI CANCELLARE UNA NOTA NON AUTORIZZATO** ip: " . $_SERVER["REMOTE_ADDR"]);
          die(json_encode("NOTEDNA"));
        }
        break;
      default:
          die(json_encode("NOTEANV"));
        break;
    }
  } else {
    die(json_encode("NOTENL"));
  }
 ?>
