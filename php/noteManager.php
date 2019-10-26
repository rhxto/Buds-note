<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = str_replace("\0", "", $data);
    $data = str_replace("0x00", "", $data);
    $data = str_replace("\000", "", $data);
    $data = str_replace("\x00", "", $data);
    $data = str_replace("\z", "", $data);
    $data = str_replace("\u0000", "", $data);
    $data = str_replace("%00", "", $data);
    if ($data == "") {
      error_log("Nota non valida test_input");
      die(json_encode(["status"=>"NOTENV"]));
    }
    return $data;
  }
  if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION["username"]) && $_SESSION['logged_in'] == '1' && isset($_POST["type"])) {
    $type = test_input($_POST["type"]);
    if ((empty($_POST["title"]) || empty($_POST["content"]) || empty($_POST["subj"]) || empty($_POST["dept"]) || str_replace("/", "", $_POST["title"]) !== $_POST["title"]) && $type == "write")  {
      error_log("Nota non valida write");
      die(json_encode(["status"=>"NOTENV"]));
    } elseif ($type == "write") {
      $title = test_input($_POST["title"]);
      $title = str_replace("'", "sc-a", $title);
      $title = str_replace('"', "sc-q", $title);
      $content = test_input($_POST["content"]);
      $subj = test_input($_POST["subj"]);
      $dept = test_input($_POST["dept"]);
    }
    if ($type == "update") {
      if (isNoteOwner(connectDb(), test_input($_POST["noteId"]), $_SESSION["username"])) {
        if ((empty($_POST["noteId"]) || empty($_POST["newTitle"]) || empty($_POST["newContent"])) && $type == "update") {
          die(json_encode("NOTEUNV"));
        } else {
          $noteId = test_input($_POST["noteId"]);
          $newTitle = test_input($_POST["newTitle"]);
          $newContent = test_input($_POST["newContent"]);
        }
      }
    } elseif ($type == "update") {
      error_log("**TENTATIVO DI AGGIORNAMENTO NOTA NON AUTORIZZATO DA: " . $_SERVER["REMOTE_ADDR"] . "**");
      die(json_encode("NOTEUNA"));
    }
    if ((empty($_POST["type"]) || empty($_POST["noteId"])) && $type == "delete") {
      error_log("Nota non valida delete");
      die(json_encode("NOTENV"));
    } elseif ($type == "delete") {
      $noteId = test_input($_POST["noteId"]);
    }
    if ((empty($_POST["noteId"]) || empty($_POST["rating"])) && $type == "rate") {
      error_log("nota non valida rate");
      die(json_encode("NOTERNV"));
    } elseif ($type == "rate") {
      $noteId = test_input($_POST["noteId"]);
      $rating = test_input($_POST["rating"]);
    }
    if ($type === "write") {
      $year = $_POST["year"];
    }
    if (((empty($_POST["year"]) || $_POST["year"] == NULL) || !in_array($year, [1,2,3,4,5])) && $type === "write") {
      error_log("Anno non valido" . $year);
      die(json_encode("NOTEWYNV"));
    }
    switch ($type) {
      case 'write':
        if (strpos($title, ".") !== false || strpos($title, "/") !== false) {
          die(json_encode(["status"=>"NOTESC"]));
        } else {
          if(($result = writeNote(connectDb(), $title, $_SESSION["username"], $subj, $dept, $year, $content))["status"] === "done") {
            if (($id = getNoteId(connectDb(), $title, $_SESSION["username"], $result["date"])) != "internalError") {
              echo json_encode(["status"=>"done", "id"=>$id]);
            } else if ($id === "internalError"){
              echo json_encode(["status"=>"NOTEW"]);
            } else {
              echo json_encode(["status"=>"NOTEWIN"]);
            }
          } else {
            error_log("PRIMO CASO ERRORE");
            die(json_encode(["status"=>"NOTEW"]));
          }
        }
        break;
      case 'update':
        if (checkNote(connectDb(), $noteId)) {
          if (updateNote(connectDb(), $_SESSION["username"], $noteId, $newTitle, $newContent)) {
            echo json_encode(["status"=>"done"]);
          } else {
            die(json_encode(["status"=>"NOTEUUF"]));
          }
        } else {
          die(json_encode(["status"=>"NOTEUNE"]));
        }
        break;
      case 'delete':
        if (getAcclvl($_SESSION["username"]) == 1 || isNoteOwner(connectDb(), $noteId, $_SESSION["username"])) {
	         if (checkNote(connectDb(), $noteId)) {
             foreach (getPicsPathsAndIds($noteId) as $pic) {
              exec("rm " . $pic["dir"]);
             }
	           if (delNote(connectDb(), $noteId)) {
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
        if (checkNote(connectDb(), $noteId)) {
          if ($rating == "true") {
            $rating = true;
          } else {
            $rating = false;
          }
          if ($type = alreadyRated($_SESSION["username"], $noteId) === "1") {
            $type = "modify";
          } else {
            $type = "new";
          }
          if ($response = rateNote($_SESSION["username"], $noteId, $rating)) {
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
