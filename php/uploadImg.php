<?php session_start();
  require_once "core.php";
  require_once "funs.php";
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = str_replace("/", "", $data);
    $data = str_replace("\0", "", $data);
    $data = str_replace("0x00", "", $data);
    $data = str_replace("\000", "", $data);
    $data = str_replace("\x00", "", $data);
    $data = str_replace("\z", "", $data);
    $data = str_replace("\u0000", "", $data);
    $data = str_replace("%00", "", $data);

    return $data;
  }
  if (empty($_POST["noteId"])) {
    die(json_encode(["status"=>"IMGUFTB"]));
  }
  $noteId = test_input($_POST["noteId"]);
  if (!isNoteOwner(connectDb(), $noteId, $_SESSION["username"])) {
    die(json_encode(["status"=>"IMGUNO"]));
  }
  if (empty($_SESSION["username"]) || !isset($_SESSION["username"]) || $_SESSION["logged_in"] === "0") {
    echo json_encode(["status"=>"IMGUNL"]);
  } else if (empty($noteId) || $noteId === "" || test_input($_FILES["uploadImage"]["name"]) !== $_FILES["uploadImage"]["name"]) {
    echo json_encode(["status"=>"IMGUVNV"]);
  } else if (($user = test_input($_SESSION["username"])) === $_SESSION["username"]) {
    if (!checkNote(connectDb(), $noteId)) {
      die(json_encode(["status"=>"IMGUNEN"]));
    }
    if (checkDotIteration($_FILES["uploadImage"]["name"])) {
      $file = basename($_FILES["uploadImage"]["name"]);
      //prendi l'estensione e mettila tutta lowercase
      $imageExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      $check = getimagesize($_FILES["uploadImage"]["tmp_name"]);
      if($check !== false /* se il file é un'immagine..*/) {
        if (file_exists($file)) {
          echo json_encode(["status"=>"IMGUAE"]);
        } else {
          //non accettiamo immagini troppo pesanti max = 5MB
          if ($_FILES["uploadImage"]["size"] > 22000000) {
            error_log("size: " + $_FILES["uploadImage"]["size"]);
            echo json_encode(["status"=>"IMGUFTB"]);
          } else {
            //controlliamo che sia un immagine
            if($imageExtension != "jpg" && $imageExtension != "jpeg" && $imageExtension != "png" && $imageExtension != "gif" ) {
              echo json_encode(["status"=>"IMGUFNS"]);
            } else {
              if (($status = newImageEntry($noteId, $imageExtension, $_FILES["uploadImage"]["name"], $_SESSION["username"]))["status"] === "done") {
                $id = $status["id"];
                $imageDir = "../notedb/$user/uploads/$id." . $imageExtension;
                if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $imageDir)) {
                  echo json_encode(["status"=>"success", "img_tag"=>"<img style='width: 100%;' src='" . $imageDir . "'/><button id='" . $id . "' class='removeImage btn' onclick=removeImage('" . $id .  "')>Rimuovi immagine</button>", "id"=>$id]);
                } else {
                  echo json_encode(["status"=>"IMGUIE"]);
                }
              } else if  ($status["status"] === "non-existentNote") {
                echo json_encode(["status" => "IMGUMNEN"]);
              } else if ($status["status"] === "invalidFormat") {
                echo json_encode(["status"=>"IMGUFNS"]);
              } else if ($status["status"] === "internalError"){
                echo json_encode(["status"=>"IMGUMIE"]);
              } else {
                error_log("**ECCEZIONE INASPETTATA IN UPLOADIMG.PHP** status: " . $status);
                echo json_encode(["status"=>"IMGUUIE"]);
              }
            }
          }
        }
      } else {
        echo json_encode(["status"=>"IMGUFNI"]);
      }
    } else {
      echo json_encode(["status"=>"IMGUFNS"]);
    }
  } else {
    error_log("****ECCEZIONE NON SUPPORTATA IN UPLOADIMG.PHP, CODE: IMGUUE****");
    logD("****ECCEZIONE NON SUPPORTATA IN UPLOADIMG.PHP, CODE: IMGUUE****");
    die(json_encode(["status"=>"IMGUUE"]));
  }
?>
