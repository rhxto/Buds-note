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
    $query = $conn->prepare("SELECT * FROM dept WHERE (name LIKE :dept_name) AND (code  LIKE :id) ORDER BY code");
    $query->bindParam(':dept_name', $name);
    $query->bindParam(':id', $id);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = $query->fetchAll();
  } catch(PDOException $e) {
    if (PDOError($e)) {
      return "internalError";
    }
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
    $query = $conn->prepare("SELECT * FROM subj WHERE (name LIKE :subj_name) AND (code LIKE :id) ORDER BY code");
    $query->bindParam(':subj_name', $name);
    $query->bindParam(':id', $id);
    $query->execute();
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $result = $query->fetchAll();
  } catch(PDOException $e) {
    if (PDOError($e)) {
      return "internalError";
    }
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
    if (PDOError($e)) {
      return "internalError";
    }
  } finally {
    $conn = null;
  }
  $results = array();

  //Ora ho matrice [<Cardinalità di dept>][2 (ovvero name e code)]
  //results [0]=> stdClass Object([username]=<username> [pw]=<password> [mail]=<mail> [acc_lvl]=<livello accesso> [fail_acc]=<accessi falliti> [last_log]=<ultimo login>)

  return $results;
}
  function searchNote($conn, $title, $dir, $user, $subj, $year, $dept, $teacher, $datefrom, $dateto, $order, $v) {
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
    if ($datefrom == NULL) {
      $datefrom = "%";
    } else {
      //$datefrom = str_replace("/", "-", $datefrom) . "0:0:0";
    }
    if ($dateto == NULL) {
      $dateto = date("Y-m-d H:i:s");
    } else {
      //$dateto = str_replace("/", "-", $dateto) . "0:0:0";
    }
    if ($order == NULL) {
      $order = "date";
    }
    if ($v == NULL) {
      $v = "DESC";
    } elseif ($v == "discendente") {
      $v = "DESC";
    } else {
      $v = "ASC";
    }

    try {
      $query = $conn->prepare("SELECT * FROM note WHERE (title LIKE :ttl) AND (dir LIKE :dir) AND (user LIKE :usr) AND (subj LIKE :subj) AND (year LIKE :year) AND (dept LIKE :dept) AND (teacher LIKE :teacher) AND (date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
      //ci serve ORDER BY date DESC per avere le note dalla piú recente
      $title = str_replace(" ", "_", $title);
      $query->bindParam(":ttl", $title);
      $query->bindParam(":dir", $dir);
      $query->bindParam(":usr", $user);
      $query->bindParam(":subj", $subj);
      $query->bindParam(":year", $year);
      $query->bindParam(":dept", $dept);
      $query->bindParam(":teacher", $teacher);
      $query->bindParam(":datefrom", $datefrom);
      $query->bindParam(":dateto", $dateto);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      $i = 0;
      foreach ($result as $row) {
        array_push($results, array());
        //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
        $results[$i]["title"] = str_replace("_", " ", $row["title"]);
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
      if (PDOError($e)) {
        return "internalError";
      }
    } finally {
      $conn = null;
    }
  }
  function writeNote($conn, String $title, String $user, String $subj, String $dept, String $content) {
    //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
    $title = str_replace(" ", "_", $title);
    $dir = "/notedb/$user/$title.txt";
    $year = date("Y");
    $teacher = $user;
    $date = date("Y-m-d H:i:s");
    try {
      $query = $conn->prepare("INSERT INTO note VALUES (:ttl, :dir, :user, :subj, :year, :dept, :teacher, :date)");
      $query->bindParam(":ttl", $title);
      $query->bindParam(":dir", $dir);
      $query->bindParam(":user", $user);
      $query->bindParam(":subj", $subj);
      $query->bindParam(":year", $year);
      $query->bindParam(":dept", $dept);
      $query->bindParam(":teacher", $teacher);
      $query->bindParam(":date", $date);
      $noteFile = fopen("../notedb/$user/$title.txt", "w+");
      if ($noteFile == false) {
        die();
      }
      fwrite($noteFile, $content);
      fclose($noteFile);
      $query->execute();
      return true;
    } catch (PDOException $e) {
      PDOError($e);
      return false;
    } finally {
      $conn = null;
    }
  }
  function delNote($conn, String $title) {
    //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
    $title = str_replace(" ", "_", $title);
    error_log("Deleting: $title");
    try {
      $query = $conn->prepare("SELECT dir FROM note WHERE title = :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $dir = $query->fetchAll();
      $dir = $dir[0]["dir"];
      exec("rm ..$dir");
      $query = $conn->prepare("DELETE FROM note WHERE title = :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();

      return true;
    } catch (PDOException $e) {
      PDOError($e);
      return false;
    } finally {
      $conn = null;
    }
  }
?>
