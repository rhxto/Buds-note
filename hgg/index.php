<?php
  echo "ciao <br/>";
  echo "ciao" . " hgg<br/>"; //concatena stringhe con .
  print_r("dd");

  echo "<br/>";
  $a = "ciao"; //le variabili si indicano col $, non si mette il tipo
  echo $a;
  print_r($a);

  echo "<br/>";
  $array = ["ciao", 22, "uu"];
  echo $array[2] . "<br/>"; //@array[n] n é l'elemento che targetti es. questa riga outputta: uu
  print_r($array);  //stampa ti po di variabile e nel caso sottocontenuti outputta: Array ( [0] => ciao [1] => 22 [2] => uu )
  echo "<br/>";
  $a = 22; //si possono sovrascrivere tutte le variabili quante volte vuoi
  $a = true;
  $a = false;

  if (/* condizione */ $a) { //ultima condizione messa = true, quindi si eseguirá l'if
    echo "a é vera";
  }
  if (!$a) { //! inverte true/false
    echo "a é falsa <br/>";
  }
  if ($a == 3) { //entrambe le espressioni sarebbero: contrariodi(a) e da true, oppure se a é uguale a falso, e da true
    echo "a é 3";
  }
  //se devi comparare, qualsiasi cosa, usa ===, perché true significa anche che una variabile non é nulla, invece con === controlli che ogni singolo byte sia uguale
  //una falla dell'== é che se hashi 2 cose e queste cominciano con 0e qualsiasi cosa ci sia dopo ti dice che sono uguali, con === no
  $a = ""; //"" é uguale a NULL ed uguale a false ovvero variabile senza ne tipo ne contenuto
  //infatti true vuol dire che la var non é nulla
  if ($a == false) {
    echo "<br/> vero";
  }
  //NOTE: funzioni
  //BUG: bug
  //DEBUG: debbug
  function nomeFunzione(/* argomenti sempre come variabile */ $nomeArgomento, int $argomentoConTipoSpecifico /* la variabile deve essere una stringa non nulla */ ) {
    //tutto tra graffe
    //int = numero
    //String = stringa
    //boolean = true/false
    //PDOObject oggetto di connessione ad un database etc..
  }
  nomeFunzione($a, 3);
  echo "aaa";
  require "funzioni.php"; //require importa TUTTE le funzioni e non hai bisogno di cose extra, le chiami e basta
  //il require, se ci sono delle istruzioni che non sono funzioni, le esegue in ordine
  funzioneEsterna("<br/>cosa da scrivere");

  //NOTE Ritorno di variabili
  $t = 0;
  function funzioneSenzaRitorno(int $fattore1, int $fattore2) {
    echo "<br/>" . ($fattore1 + $fattore2);
    $t = ($fattore1 + $fattore2); //viene modificata solo per il codice all'interno della funzione
  }
  funzioneSenzaRitorno(3, 5);
  echo "<br/> $t"; //se la stringa é contenuta in "" NON apostrofi, digitando "ciao $var" é uguale a digitare "ciao" . $var

  function funzioneConRitorno(int $fattore1, int $fattore2) {
    return $fattore1 + $fattore2 + 2;
    //return, "restituisce il valore dopo"
  }
  $somma = funzioneConRitorno(3, 5);
  echo "<br/> $somma";
  //operazioni: + piú - meno * per / diviso % resto
  echo "<br/>" . 2**2; //num**num1 num alla potenza num1
  //$var++ = aumenta di 1 es. $a = 5 $a++ ora a = 6
  //$var-- l'opposto
  //$var += 5; corrisponde a $var = $var + 5;
  //stessa cosa per -= *= /=
  //es. $var = 10 $var /= 2; ora $var = 5
  //$var = $var / 2;

  for ($i = 0; $i < 5; $i++) {
    //parti da var $i = a x finché x é minore di 5 esegui le istruzioni all'interno, poi aumenta i di 1, poi ricontrolla se é minore di 5 riesegui, risomma etc;..
    echo "ciao";
  }
 ?>
