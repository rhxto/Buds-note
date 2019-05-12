<?php session_start();
  require_once 'core.php';
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
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1') {
    if (empty($_POST["title"]) || empty($_POST["content"]) || empty($_POST["subj"]) || empty($_POST["dept"])) {
            die(json_encode("NOTENV"));
    } else {
      $title = test_input($_POST["title"]);
      $content = test_input($_POST["content"]);
      $subj = test_input($_POST["subj"]);
      $dept = test_input($_POST["dept"]);
      $type = test_input($_POST["type"]);
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
          //delNote($title);
          break;
        default:
            die(json_encode("NOTEANV"));
          break;
        echo "done";
      }
    }
  } else {
    die(json_encode("NOTENL"));
  }
 ?>
