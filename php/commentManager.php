<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die(json_encode("COMMENTNV"));
    }
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1') {
    $type = test_input($_POST["type"]);
    if ((empty($_POST["content"]) || empty($_POST["noteId"])) && $type == "write")  {
      die(json_encode("COMMENTNV"));
    } elseif ($type == "write") {
      $content = test_input($_POST["content"]);
      $noteId = test_input($_POST["noteId"]);
    }
    if (empty($_POST["id"]) && $type == "delete") {
      die(json_encode("COMMENTNV"));
    } elseif ($type == "delete") {
      $id = test_input($_POST["id"]);
    }
    switch ($type) {
      case 'write':
      $postResult = postComment(connectDb(), $_SESSION["username"], $noteId, $content);
        if ($postResult["state"]) {
          $response = array();
          $response["state"] = "done";
          $response["id"] = $postResult["id"];
          $response["username"] = $_SESSION["username"];
          $response["date"] = $postResult["date"];
          echo json_encode($response);
        } else {
          die(json_encode("COMMENTW"));
        }
        break;
      case 'update':
        //updateNote($title, $content)
        break;
      case 'delete':
        if (getAcclvl($_SESSION["username"]) == 1) {
          if (delComment(connectDb(), $id)) {
            echo json_encode("done");
          } else {
            die(json_encode("COMMENTDE"));
          }
        } else {
          error_log("**TENTATIVO DI CANCELLARE UN COMMENTO NON AUTORIZZATO** ip: " . $_SERVER["REMOTE_ADDR"]);
          die(json_encode("COMMENTDNA"));
        }
        break;
      default:
          die(json_encode("COMMENTANV"));
        break;
    }
  } else {
    die(json_encode("COMMENTNL"));
  }
 ?>
