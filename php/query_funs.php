<?php

//Definisco una volta per tutte le credenziali di accesso al DB

//La libreria contiene funzioni che eseguono query

//La funzione apre una connessione e ritorna un'oggetto PDO
function connectDb(){
  $DBHOST = "localhost";
  $DBNAME = "Buds_db";
  $DBUSRN = "system";
  $DBPW = "the_best_admin_passwd";

  try {
    $conn = new PDO("mysql:host=$DBHOST;dbname=$DBNAME, $DBUSRN, $DBPW");
    $conn->SetAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("USE Buds_db;");
  } catch(PDOException $e) {            //Sistemare esteticamente le prossime 10 righe (fai una function)
    require 'exceptions.php';
    $exist = err_handler($e->getCode(), $e->getMessage());
    if (!$exist) {
      die("<h1>Errore interno</h1>");
      return true;
    } else {
      return false;
    }
  } finally {
    return $conn;
    $conn = null;
  }

//La funzione ritorna una matrice con nome e id del dept, se $db è null allora ritorna -1
function dept(PDOStatement $db, String $name, int $id){

  if($db == NULL){
    return -1;
  }
  if($name == NULL){
    $name = TRUE;
  }
  if($id == NULL){
    $id = TRUE;
  }

  $query = $db->prepare("SELECT * FROM dept WHERE (name = :dept_name) AND (code = :id)");
  $query->bindParam(':dept_name', $name);
  $query->bindParam(':id', $id);

  $query->execute();

  $result = $query->get_result();

  $results = array();

  while($row = $result->fetch_object()){
    array_push($results, $row);
  }

  //Ora ho matrice [<Cardinalità di dept>][2 (ovvero name e code)]
//results [0]=> stdClass Object([code]=<codice id> [name]=<nome dept>)

  return $results;
}

function subj(PDOStatement $db, String $name, int $id){

  if($db == NULL){
    return -1;
  }
  if($name == NULL){
    $name = TRUE;
  }
  if($id == NULL){
    $id = TRUE;
  }

  $query = $db->prepare("SELECT * FROM subj WHERE (name = :subj_name) AND (code = :id)");
  $query->bindParam(':subj_name', $name);
  $query->bindParam(':id', $id);

  $query->execute();

  $result = $query->get_result();

  $results = array();

  while($row = $result->fetch_object()){
    array_push($results, $row);
  }

  //Ora ho matrice [<Cardinalità di subj>][2 (ovvero name e code)]
//results [0]=> stdClass Object([code]=<codice id> [name]=<nome subj>)

  return $results;
  }

function user(PDOStatement $db, String $username, String $mail, int $acc_lvl_max, String $fail_acc, String $last_log_from, String $last_log_to){

  if($db == NULL){
    return -1;
  }
  if($username == NULL){
    $username = TRUE;
  }
  if($mail == NULL){
    $mail = TRUE;
  }
  if($acc_lvl == NULL){
    $acc_lvl = TRUE;
  }
  if($last_log_from == NULL){
    $last_log_from = TRUE;
  }
  if($last_log_to == NULL){
    $last_log_to = TRUE;
  }


  $name = "%$name%";

  $query = $db->prepare("SELECT * FROM user WHERE (username = :usrn) AND (mail = :email) AND (acc_lvl = :acclvl) AND (fail_acc = :failacc) AND (last_log BETWEEN :lastlogfrom AND :lastlogto)");
  $query->bindParam(':usrn', $username);
  $query->bindParam(':email', $mail);
  $query->bindParam(':acclvl', $acc_lvl);
  $query->bindParam(':failacc', $fail_acc);
  $query->bindParam(':lastlogfrom', $last_log_from);
  $query->bindParam(':lastlogto', $last_log_to);

  $query->execute();

  $result = $query->get_result();

  $results = array();

  while($row = $result->fetch_object()){
    array_push($results, $row);
  }

  //Ora ho matrice [<Cardinalità di dept>][2 (ovvero name e code)]
  //results [0]=> stdClass Object([username]=<username> [pw]=<password> [mail]=<mail> [acc_lvl]=<livello accesso> [fail_acc]=<accessi falliti> [last_log]=<ultimo login>)

  return $results;
}

?>
