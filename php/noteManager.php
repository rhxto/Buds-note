<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = str_replace("/", "", $data);
    if ($data == "") {
      error_log("Nota non valida test_input");
      die(json_encode("NOTENV"));
    }
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1' && isset($_POST["type"])) {
    $type = test_input($_POST["type"]);
    if ((empty($_POST["title"]) || empty($_POST["content"]) || empty($_POST["subj"]) || empty($_POST["dept"]) || test_input($_POST["title"]) !== $_POST["title"]) && $type == "write")  {
      error_log("Nota non valida write");
      die(json_encode("NOTENV"));
    } elseif ($type == "write") {
      $title = test_input($_POST["title"]);
      if (checkNote(connectDb(), $title)) {
        die(json_encode("NOTEWAE"));
      }
      $content = test_input($_POST["content"]);
      $subj = test_input($_POST["subj"]);
      $dept = test_input($_POST["dept"]);
    }
    if ((isNoteOwner(connectDb(), $_POST["title"], $_SESSION["username"])) && $type == "update") {
      if ((empty($_POST["title"]) || empty($_POST["newTitle"]) || empty($_POST["newContent"])) && $type == "update") {
        die(json_encode("NOTEUNV"));
      } else {
        $title = test_input($_POST["title"]);
        $newTitle = test_input($_POST["newTitle"]);
        $newContent = test_input($_POST["newContent"]);
      }
    } elseif ($type == "update") {
      error_log("**TENTATIVO DI AGGIORNAMENTO NOTA NON AUTORIZZATO DA: " . $_SERVER["REMOTE_ADDR"] . "**");
      die(json_encode("NOTEUNA"));
    }
    if ((empty($_POST["type"]) || empty($_POST["title"])) && $type == "delete") {
      error_log("Nota non valida delete");
      die(json_encode("NOTENV"));
    } elseif ($type == "delete") {
      $title = test_input($_POST["title"]);
    }
    if ((empty($_POST["title"]) || empty($_POST["rating"])) && $type == "rate") {
      logD("nota non valida rate");
      die(json_encode("NOTERNV"));
    } elseif ($type == "rate") {
      $title = test_input($_POST["title"]);
      $rating = test_input($_POST["rating"]);
    }
    switch ($type) {
      case 'write':
        if (strpos($title, ".") !== false || strpos($title, "/") !== false) {
          die(json_encode("NOTESC"));
        } else {
          if(writeNote(connectDb(), $title, $_SESSION["username"], $subj, $dept, $content)) {
            echo json_encode("done");
          } else {
            die(json_encode("NOTEW"));
          }
        }
        break;
      case 'update':
        if (checkNote(connectDb(), $title)) {
          if (updateNote(connectDb(), $_SESSION["username"], $title, $newTitle, $newContent)) {
            echo json_encode("done");
          } else {
            die(json_encode("NOTEUUF"));
          }
        } else {
          die(json_encode("NOTEUNE"));
        }
        break;
      case 'delete':
        if (getAcclvl($_SESSION["username"]) == 1) {
	         if (checkNote(connectDb(), $title)) {
             foreach (getPicsPaths($title) as $pic) {
              exec("rm " . $pic["dir"]);
             }
	           if (delNote(connectDb(), $title)) {
               echo json_encode("done");
             } else {
               echo json_encode("NOTEDE");
             }
	         } else {
	           die(json_encode("NOTEDNF"));
	         }
      } else {
        error_log("**TENTATIVO DI CANCELLARE UNA NOTA NON AUTORIZZATO** ip: " . $_SERVER["REMOTE_ADDR"]);
        die(json_encode("NOTEDNA"));
      }
      break;
      case "rate":
        if (checkNote(connectDb(), $title)) {
          if ($rating == "true") {
            $rating = true;
          } else {
            $rating = false;
          }
          if ($type = alreadyRated($_SESSION["username"], $title) === "1") {
            $type = "modify";
          } else {
            $type = "new";
          }
          if ($response = rateNote($_SESSION["username"], $title, $rating)) {
            echo json_encode(["status"=> "done", "type"=>$type]);
          } elseif ($response === "internalError") {
            die(json_encode("NOTERWIE"));
          } else {
            die(json_encode("NOTERAE"));
          }
        } else {
          die(json_encode("NOTERNE"));
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
