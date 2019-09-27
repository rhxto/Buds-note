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
  $note = str_replace("'", "sc-a", $_POST["note"]);
  $note = str_replace('"', "sc-q", $note);
  $note = str_replace(" ", "_", $note);
  $note = test_input($note);
  if (!isNoteOwner(connectDb(), $note, $_SESSION["username"])) {
    die(json_encode(["status"=>"IMGUNO"]));
  }
  $noteCheck = str_replace("'", "sc-a", $_POST["note"]);
  $noteCheck = str_replace('"', "sc-q", $noteCheck);
  $noteCheck = str_replace(" ", "_", $noteCheck);
  if ($noteCheck !== $note) {
    die(json_encode(["status"=>"IMGUVNV"]));
  }
  if (empty($_SESSION["username"]) || !isset($_SESSION["username"]) || $_SESSION["logged_in"] === "0") {
    echo json_encode(["status"=>"IMGUNL"]);
  } else if (empty($note) || $note === "" || test_input($_FILES["uploadImage"]["name"]) !== $_FILES["uploadImage"]["name"]) {
    echo json_encode(["status"=>"IMGUVNV"]);
  } else if (($user = test_input($_SESSION["username"])) === $_SESSION["username"]) {
    if (!checkNote(connectDb(), $note)) {
      die(json_encode(["status"=>"IMGUNEN"]));
    }
    if (checkDotIteration($_FILES["uploadImage"]["name"])) {
      $file = str_replace(" ", "_", "../notedb/" . $user . "/uploads" . "/" . basename($note . "_" . $user . "_" . $_FILES["uploadImage"]["name"]));
      $file = str_replace("'", "sc-a", $file);
      $file = str_replace('"', "sc-q", $file);
      //prendi l'estensione e mettila tutta lowercase
      $imageExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
      $check = getimagesize($_FILES["uploadImage"]["tmp_name"]);
      if($check !== false /* se il file é un'immagine..*/) {
        if (file_exists($file)) {
          echo json_encode(["status"=>"IMGUAE"]);
        } else {
          //non accettiamo immagini troppo pesanti max = 5MB
          if ($_FILES["uploadImage"]["size"] > 10000000) {
            echo json_encode(["status"=>"IMGUFTB"]);
          } else {
            //controlliamo che sia un immagine
            if($imageExtension != "jpg" && $imageExtension != "jpeg" && $imageExtension != "png" && $imageExtension != "gif" ) {
              echo json_encode(["status"=>"IMGUFNS"]);
            } else {
              if (($status = newImageEntry($note, $imageExtension, $file, $_FILES["uploadImage"]["name"])) === "done") {
                if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $file)) {
                  echo json_encode(["status"=>"success", "img_tag"=>'<img style="width: 50%;" src="' . $file . '">']);
                } else {
                  echo json_encode(["status"=>"IMGUIE"]);
                }
              } else if  ($status === "non-existentNote") {
                echo json_encode(["status" => "IMGUMNEN"]);
              } else if ($status === "invalidFormat") {
                echo json_encode(["status"=>"IMGUFNS"]);
              } else if ($status === "internalError"){
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
