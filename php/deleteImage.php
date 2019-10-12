<?php session_start();
  require_once 'core.php';
  require_once 'funs.php';
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

  //Verifica che l'utente che vuole modificare la foto sia loggato, e sia chi dice di essere, se ci sono problemi di identità risponde IMGDNL
  if (empty(($user = $_SESSION["username"])) || !isset($_SESSION["username"]) || $_SESSION["logged_in"] === "0") {
    echo json_encode(["status"=>"IMGDNL"]);
  } else if (($id = test_input($_POST["id"])) === $_POST["id"]) {
    $note = str_replace(" ", "_", test_input($_POST["note"]));
    $note = str_replace("'", "sc-a", $note);
    $note = str_replace('"', "sc-q", $note);
    error_log("User: " . $user . " deleting: " . $note);
    //Concediamo la modifica solo se user è il creatore della nota o se è un admin
    //Dopo aver modificatola funzione removeImage bisogna anche modificare i case, togliendo quelli che non vengono mai sollevati

    switch (removeImage(connectDb(), $note, $id, $_SESSION["username"])) {
      case "done":
        echo json_encode(["status"=>"success"]);
        break;
      case "notAuthorized":
        echo json_encode(["status"=>"IMGDNA"]); //non autorizzato
        error_log("**l'utente $user ha provato a cancellare l'immagine numero $id senza autorizzazione: ip: " . $_SERVER["REMOTE_ADDR"] . "**");
        break;
      case "imgNotFound":
        echo json_encode(["status"=>"IMGNTFND"]); //immagine on trovata per la nota data
        break;
      case "interalError":
        echo json_encode(["status"=>"IMGDIE"]); //errore mysql
        break;
      case "illegalDeletion":
        echo json_encode(["status"=>"IMGDID"]); //immagine non associata alla nota
        break;
      default:
        echo json_encode(["status"=>"IMGDUE"]);
        break;
    }
  } else {
    echo json_encode(["status"=>"IMGDVNV"]); //valori non validi
  }
  //NOTE: abbiamo IMGDUE per le eccezioni non supportate che per ora non tiriamo
 ?>
