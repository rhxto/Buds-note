<?php
  require_once 'core.php';
  require_once 'query_funs.php';
  $response = searchNote(connectDb(), "Nota di prova", NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, "date", "desc");
  print_r($response);
 ?>
