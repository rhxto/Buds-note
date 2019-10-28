<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && ((isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1') || $_POST["type"] === "check")) {
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
    if ((empty(test_input($_POST["noteId"])) || empty($_POST["commentsIds"])) && $type == "check") {
      die(json_encode(["status"=>"COMMENTUVNV"]));
    } else {
      $commentsIds = $_POST["commentsIds"];
      foreach ($commentsIds as $commentId) {
        if ($commentId !== test_input($commentId)) {
          die(json_encode(["status"=>"COMMENTUVNV"]));
        }
      }
      $noteId = test_input($_POST["noteId"]);
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
      case 'check':
        if (checkNote(connectDb(), $noteId)) {
          if ($commentsIds[0] === "null") {
            $result = searchRevw(connectDb(), NULL, NULL, $noteId, NULL, NULL, NULL, NULL, NULL);
            if (empty($result)) {
              echo json_encode(["status"=>"up-to-date"]);
            } else {
              echo json_encode(["status"=>"outdated", "newComments"=>$result]);
            }
          } else {
            $result = searchRevw(connectDb(), NULL, NULL, $noteId, NULL, NULL, NULL, NULL, NULL);
            if (sizeOf($result) > sizeOf($commentsIds)) {
              $newComments = array();
              foreach ($result as $comment) {
                if (!in_array($comment["id"], $commentsIds, true)) {
                  array_push($newComments, $comment);
                }
              }
              if (empty($newComments)) {
                die(json_encode(["status"=>"COMMENTUIE"]));
              } else {
                echo json_encode(["status"=>"outdated", "newComments"=>$newComments]);
              }
            } elseif (sizeOf($result) === sizeOf($commentsIds)) {
              echo json_encode(["status"=>"up-to-date"]);
            } else {
              $currentCommentsIds = array();
              foreach ($result as $comment) {
                array_push($currentCommentsIds, $comment["id"]);
              }
              $deletedCommentsIds = array();
              foreach ($commentsIds as $commentId) {
                if (!in_array($commentId, $currentCommentsIds, true)) {
                  array_push($deletedCommentsIds, $commentId);
                }
              }
              if (empty($deletedCommentsIds)) {
                die(json_encode(["status"=>"COMMENTUIE"]));
              } else {
                echo json_encode(["status"=>"outdated-deletions", "deletedCommentsIds"=>$deletedCommentsIds]);
              }
            }
          }
        } else {
          echo json_encode(["status"=>"deleted"]);
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
