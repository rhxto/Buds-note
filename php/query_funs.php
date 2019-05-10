<?php

//La libreria contiene funzioni che eseguono query

//La funzione apre una connessione e ritorna un'oggetto PDO


//La funzione ritorna una matrice con nome e id del dept, se $conn è null allora ritorna -1
function dept($conn, $name, $id){
  if($conn == "null"){
    return -1;
  }
  if($name == NULL){
    $name = '%';
  }
  if($id == NULL){
    $id = "%";
  }
  try {
    $query = $conn->prepare("SELECT * FROM dept WHERE (name LIKE :dept_name) AND (code  LIKE :id)");
    $query->bindParam(':dept_name', $name);
    $query->bindParam(':id', $id);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = $query->fetchAll();
  } catch(PDOException $e) {
    PDOError($e);
  } finally {
    $conn = null;
  }
  $results = array(array(), array());
  foreach ($result as $row){
    array_push($results[0], $row["code"]);
    array_push($results[1], $row["name"]);
  }
  //Ora il mio risultato è una matrice dove nella riga [0] ho i codici e nella riga [1] i nomi
  return $results;
}

function subj($conn, $name, $id){
  unset($results);
  unset($result);
  if($conn == "null"){
    return -1;
  }
  if($name == NULL){
    $name = '%';
  }
  if($id == NULL){
    $id = "%";
  }
  try {
    $query = $conn->prepare("SELECT * FROM subj WHERE (name LIKE :subj_name) AND (code LIKE :id)");
    $query->bindParam(':subj_name', $name);
    $query->bindParam(':id', $id);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = $query->fetchAll();
  } catch(PDOException $e) {
    PDOError($e);
  } finally {
    $conn = null;
  }
  $results = array(array(), array());
  foreach ($result as $row){
    array_push($results[0], $row["code"]);
    array_push($results[1], $row["name"]);
  }
  //Ora il mio risultato è una matrice dove nella riga [0] ho i codici e nella riga [1] i nomi
  return $results;
}



function user(PDOObject $conn, String $username, String $mail, int $acc_lvl_max, String $fail_acc, String $last_log_from, String $last_log_to){

  if($conn == NULL){
    return -1;
  }
  if($username == ""){
    $username = '%';
  }
  if($mail == ""){
    $mail = '%';
  }
  if($acc_lvl == NULL){
    $acc_lvl = TRUE;
  }
  if($last_log_from == ""){
    $last_log_from = TRUE;
  }
  if($last_log_to == ""){
    $last_log_to = TRUE;
  }
    try {
    $query = $conn->prepare("SELECT * FROM user WHERE (username LIKE :usrn) AND (mail LIKE :email) AND (acc_lvl = :acclvl) AND (fail_acc = :failacc) AND (last_log BETWEEN :lastlogfrom AND :lastlogto)");
    $query->bindParam(':usrn', $username);
    $query->bindParam(':email', $mail);
    $query->bindParam(':acclvl', $acc_lvl);
    $query->bindParam(':failacc', $fail_acc);
    $query->bindParam(':lastlogfrom', $last_log_from);
    $query->bindParam(':lastlogto', $last_log_to);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = $query->fetchAll();
  } catch(PDOException $e) {
    PDOError($e);
  } finally {
    $conn = null;
  }
  $results = array();

  //Ora ho matrice [<Cardinalità di dept>][2 (ovvero name e code)]
  //results [0]=> stdClass Object([username]=<username> [pw]=<password> [mail]=<mail> [acc_lvl]=<livello accesso> [fail_acc]=<accessi falliti> [last_log]=<ultimo login>)

  return $results;
}
  function searchNote($conn, $title, $dir, $user, $subj, $year, $dept, $teacher, $date, $order, $v) {
    if ($title == NULL) {
      $title = "%";
    }
    if ($dir == NULL) {
      $dir = "%";
    }
    if ($user == NULL) {
      $user = "%";
    }
    if ($subj == NULL) {
      $subj = "%";
    }
    if ($year == NULL) {
      $year = "%";
    }
    if ($dept == NULL) {
      $dept = "%";
    }
    if ($teacher == NULL) {
      $teacher = "%";
    }
    if ($date == NULL) {
      $date = "%";
    }
    if ($order == NULL) {
      return NULL;
    }
    if ($v == NULL) {
      $v = "DESC";
    } elseif ($v == "asc") {
      $v = "ASC";
    } else {
      $v = "DESC";
    }
    try {
      $query = $conn->prepare("SELECT * FROM note WHERE (title LIKE :ttl) AND (dir LIKE :dir) AND (user LIKE :usr) AND (subj LIKE :subj) AND (year LIKE :year) AND (dept LIKE :dept) AND (teacher LIKE :teacher) AND (date LIKE :date) ORDER BY :order :v");
      //ci serve ORDER BY date DESC per avere le note dalla piú recente
      $query->bindParam(":ttl", $title);
      $query->bindParam(":dir", $dir);
      $query->bindParam(":usr", $user);
      $query->bindParam(":subj", $subj);
      $query->bindParam(":year", $year);
      $query->bindParam(":dept", $dept);
      $query->bindParam(":teacher", $teacher);
      $query->bindParam(":date", $date);
      $query->bindParam(":order", $order);
      $query->bindParam(":v", $v);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      $i = 0;
      foreach ($result as $row){
        array_push($results, array());
        $results[$i]["title"] = $row["title"];
        $results[$i]["dir"] = $row["dir"];
        $results[$i]["user"] = $row["user"];
        $results[$i]["subj"] = $row["subj"];
        $results[$i]["year"] = $row["year"];
        $results[$i]["dept"] = $row["dept"];
        $results[$i]["teacher"] = $row["teacher"];
        $results[$i]["date"] = $row["date"];
        $i++;
      }
      return $results;
    } catch(PDOException $e) {
      PDOError($e);
    } finally {
      $conn = null;
    }
  }
?>
