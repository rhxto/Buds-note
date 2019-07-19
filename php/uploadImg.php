<?php session_start();
  require_once "core.php";
  require_once "funs.php";
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
  }

  if (empty($_SESSION["username"]) || !isset($_SESSION["username"]) || $_SESSION["logged_in"] === "0") {
    echo json_encode("IMGUNL");
  } else if (empty(($note = $_POST["note"])) || $note === "") {
    echo json_encode("IMGUVNV");
  } else if (($user = test_input($_SESSION["username"])) === $_SESSION["username"]) {
    if (!checkNote(connectDb(), $note)) {
      logD($note);
      die(json_encode("IMGUNEN"));
    }
    $file = "../notedb/" . $user . "/uploads" . "/" . basename($note . "_" . $user . "_" . $_FILES["uploadImage"]["name"]);
    //prendi l'estensione e mettila tutta lowercase
    $imageExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
    $check = getimagesize($_FILES["uploadImage"]["tmp_name"]);
    if($check !== false /* se il file é un'immagine..*/) {
      if (file_exists($file)) {
        echo json_encode("IMGUAE");
      } else {
        //non accettiamo immagini troppo pesanti max = 5MB
        if ($_FILES["uploadImage"]["size"] > 5000000) {
          echo json_encode("IMGUFTB");
        } else {
          //controlliamo che sia un immagine
          if($imageExtension != "jpg" && $imageExtension != "jpeg" && $imageExtension != "png" && $imageExtension != "gif" ) {
            echo json_encode("IMGUFNS");
          } else {
            if (($status = newImageEntry($note, $imageExtension, $file, $_FILES["uploadImage"]["name"])) === "done") {
              if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $file)) {
                echo json_encode("success");
              } else {
                echo json_encode("IMGUIE");
              }
            } else if  ($status === "non-existentNote") {
              echo json_encode("IMGUMNEN");
            } else if ($status === "invalidFormat") {
              echo json_encode("IMGUFNS");
            } else if ($status === "internalError"){
              echo json_encode("IMGUMIE");
            } else {
              error_log("**ECCEZIONE INASPETTATA IN UPLOADIMG.PHP** status: " . $status);
              echo json_encode("IMGUUIE");
            }
          }
        }
      }
    } else {
      echo json_encode("IMGUFNI");
    }
  } else {
    error_log("****ECCEZIONE NON SUPPORTATA IN UPLOADIMG.PHP, CODE: IMGUUE****");
    logD("****ECCEZIONE NON SUPPORTATA IN UPLOADIMG.PHP, CODE: IMGUUE****");
    die(json_encode("IMGUUE"));
  }
?>
