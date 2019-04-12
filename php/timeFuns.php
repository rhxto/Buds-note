<?php
function differenzaData($inzio, $fine){
  $inzio = strtotime($inzio);
  $fine = strtotime($fine);

  return ($fine - $inzio);
}
 ?>
