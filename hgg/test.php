<?php
  /*fai 5 funzioni che ti permettono di:
    -sommare 2 numeri
    -dividere 2 numeri
    -sottrarre 2 numeri
    -moltiplicare 2 numeri
    -stampare la "concatenazione di due stringhe"
  */
  function Sommadi2numeri(int $fattore1, int $fattore2) {
  return $fattore1 + $fattore2;
}
$somma = Sommadi2numeri(11, 34);
echo "<br/> $somma";

function Moltiplicazionedi2numeri(int $fattore1a, int $fattore2a) {
return $fattore1a + $fattore2a;
}
$prodotto = Moltiplicazionedi2numeri(29, 12);
echo "<br/> $prodotto";

function Sottrazionedi2numeri(int $fattore1b, int $fattore2b) {
return $fattore1b + $fattore2b;
}
$differenza = Sottrazionedi2numeri(43, 18);
echo "<br/> $differenza";

function Divisionedi2numeri(int $fattore1c, int $fattore2c) {
return $fattore1c + $fattore2c;
}
$quoziente = Divisionedi2numeri(11, 34);
echo "<br/> $quoziente";
 ?>
