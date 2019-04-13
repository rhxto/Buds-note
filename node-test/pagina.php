<?php
  if(isset($_POST["data"])) {
    $file = fopen("dati.txt", "w+") or die("Errore");
    fwrite($file, $_POST["data"]);
    fclose($file);
  }
 ?>
