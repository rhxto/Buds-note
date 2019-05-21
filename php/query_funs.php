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
  function searchNote($conn, $title, $dir, $user, $subj, $year, $dept, $datefrom, $dateto, $order, $v) {
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
      $query = $conn->prepare("SELECT * FROM note WHERE (title LIKE :ttl) AND (dir LIKE :dir) AND (user LIKE :usr) AND (subj LIKE :subj) AND (year LIKE :year) AND (dept LIKE :dept) AND (date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
      //ci serve ORDER BY date DESC per avere le note dalla piú recente
      $title = str_replace(" ", "_", $title);
      $query->bindParam(":ttl", $title);
      $query->bindParam(":dir", $dir);
      $query->bindParam(":usr", $user);
      $query->bindParam(":subj", $subj);
      $query->bindParam(":year", $year);
      $query->bindParam(":dept", $dept);
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
    $date = date("Y-m-d H:i:s");
    try {
      $query = $conn->prepare("INSERT INTO note VALUES (:ttl, :dir, :user, :subj, :year, :dept, :date)");
      $query->bindParam(":ttl", $title);
      $query->bindParam(":dir", $dir);
      $query->bindParam(":user", $user);
      $query->bindParam(":subj", $subj);
      $query->bindParam(":year", $year);
      $query->bindParam(":dept", $dept);
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
      die(json_encode("NOTEW"));
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

  function checkNote($conn, String $title) {
    $title = str_replace(" ", "_", $title);
    try {
      $query = $conn->prepare("SELECT user FROM note WHERE title = :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();
      $result = $query->fetchAll();
      $result = $result[0]["user"];
      if (empty($result)) {
        return false;
      } else {
	return true;
      }
    } catch (PDOException $e) {
      PDOError($e);
      return false;
    } finally {
      $conn = null;
    }
  }
  function getNote($conn, String $title) {
    $title = str_replace(" ", "_", $title);
    try {
      $query = $conn->prepare("SELECT dir FROM note WHERE title = :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $dir = $query->fetchAll();
      $dir = $dir[0]["dir"];
      return file("../$dir");
    } catch (PDOException $e) {
      PDOError($e);
      return false;
    } finally {
      $conn = null;
    }
  }
  function searchMark($conn, $id, $user, $title, $mark, $datefrom, $dateto, $code){

    if($conn == "null"){
      return -1;
    }
    if($id == NULL){
      $id = "%";
    }
    if($user == NULL){
      $user = '%';
    }
    if($title == NULL){
      $title = "%";
    } else {
      $title = str_replace(" ", "_", $title);
    }
    if($mark == NULL){
      $mark = "%";
    }elseif($mark<1){
      $mark = 1;
    }elseif($mark>5){
      $mark = 5;
    }
    if ($datefrom == NULL) {
      $datefrom = "%";
    }
    if ($dateto == NULL) {
      $dateto = date("Y-m-d H:i:s");
    }
    if($code == NULL){
      $code = "title";
    }



    try {
      $query = $conn->prepare("SELECT * FROM mark WHERE (id LIKE :id) AND (user LIKE :user) AND (title LIKE :title) AND (mark LIKE :mark) AND(date BETWEEN :datefrom AND :dateto) ORDER BY :code");
      $query->bindParam(':id', $id);
      $query->bindParam(':user', $user);
      $query->bindParam(':title', $title);
      $query->bindParam(':mark', $mark);
      $query->bindParam(':datefrom', $datefrom);
      $query->bindParam(':dateto', $dateto);
      $query->bindParam(':code', $code);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      $i = 0;
      foreach ($result as $row) {
        array_push($results, array());
        //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
        $results[$i]["id"] = $row["id"];
        $results[$i]["user"] = $row["user"];
        $results[$i]["title"] = str_replace("_", " ", $row["title"]);
        $results[$i]["mark"] = $row["mark"];
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

  function searchRepo($conn, $id, $user, $title, $text, $datefrom, $dateto, $code){

    if($conn == "null"){
      return -1;
    }
    if($id == NULL){
      $id = "%";
    }
    if($user == NULL){
      $user = '%';
    }
    if($title == NULL){
      $title = "%";
    } else {
      $title = str_replace(" ", "_", $title);
    }
    if($text == NULL){
      $text = "%";
    }
    if ($datefrom == NULL) {
      $datefrom = "%";
    }
    if ($dateto == NULL) {
      $dateto = date("Y-m-d H:i:s");
    }
    if($code == NULL){
      $code = "title";
    }



    try {
      $query = $conn->prepare("SELECT * FROM repo WHERE (id LIKE :id) AND (user LIKE :user) AND (title LIKE :title) AND (text LIKE :text) AND(date BETWEEN :datefrom AND :dateto) ORDER BY :code");
      $query->bindParam(':id', $id);
      $query->bindParam(':user', $user);
      $query->bindParam(':title', $title);
      $query->bindParam(':text', $text);
      $query->bindParam(':datefrom', $datefrom);
      $query->bindParam(':dateto', $dateto);
      $query->bindParam(':code', $code);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      $i = 0;
      foreach ($result as $row) {
        array_push($results, array());
        //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
        $results[$i]["id"] = $row["id"];
        $results[$i]["user"] = $row["user"];
        $results[$i]["title"] = str_replace("_", " ", $row["title"]);
        $results[$i]["text"] = $row["text"];
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

  function searchRevw($conn, $id, $user, $title, $review, $datefrom, $dateto, $order, $v){

    if($conn == "null"){
      return -1;
    }
    if($id == NULL){
      $id = "%";
    }
    if($user == NULL){
      $user = '%';
    }
    if($title == NULL){
      $title = "%";
    } else {
      $title = str_replace(" ", "_", $title);
    }
    if($review == NULL){
      $review = "%";
    }
    if ($datefrom == NULL) {
      $datefrom = "%";
    }
    if ($dateto == NULL) {
      $dateto = date("Y-m-d H:i:s");
    }
    if($order == NULL){
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
      $query = $conn->prepare("SELECT * FROM revw WHERE (id LIKE :id) AND (user LIKE :user) AND (title LIKE :title) AND (review LIKE :review) AND(date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
      $query->bindParam(':id', $id);
      $query->bindParam(':user', $user);
      $query->bindParam(':title', $title);
      $query->bindParam(':review', $review);
      $query->bindParam(':datefrom', $datefrom);
      $query->bindParam(':dateto', $dateto);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      $i = 0;
      foreach ($result as $row) {
        array_push($results, array());
        //dobbiamo usare il _ perché nel where di delete non funzionerebbe usare spazi
        $results[$i]["id"] = $row["id"];
        $results[$i]["user"] = $row["user"];
        $results[$i]["title"] = str_replace("_", " ", $row["title"]);
        $results[$i]["review"] = $row["review"];
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
  function postComment($conn, String $user, String $title, String $content) {
    if ($user == NULL || $content == NULL || $conn == "null" || $conn == NULL) {
      return false;
    }
    $title = str_replace(" ", "_", $title);
    try {
      $query = $conn->prepare("INSERT INTO revw (user, title, review, date) VALUES (:user, :title, :review, NOW())");
      $query->bindParam(":user", $user);
      $query->bindParam(":title", $title);
      $query->bindParam(":review", $content);
      $query->execute();
      $query = $conn->prepare("SELECT id FROM revw WHERE review LIKE :content");
      $query->bindParam(":content", $content);
      $query->execute();
      $id = $query->fetchAll();
      $response = array();
      $response["state"] = true;
      $response["id"] = $id[0]["id"];
      return $response;
    } catch(PDOException $e) {
      if (PDOError($e)) {
        return "internalError";
      }
    } finally {
      $conn = null;
    }
  }
  function delComment($conn, $id) {
    if ($id == "" || $id == "%" || $id == NULL || $conn == "null" || $conn == NULL) {
      return false;
    }
    try {
      $query = $conn->prepare("DELETE FROM revw WHERE id = :id");
      $query->bindParam(":id", $id);
      $query->execute();
      return true;
    } catch(PDOException $e) {
      if (PDOError($e)) {
        return "internalError";
      }
    } finally {
      $conn = null;
    }
  }
  function isNoteOwner($conn, $title, $user) {
    if ($conn == "null" || $conn == NULL || $title == NULL || $user == NULL) {
      return false;
    }
    try {
      $title = str_replace(" ", "_", $title);
      $query = $conn->prepare("SELECT user FROM note WHERE title LIKE :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();
      if ($query->fetchAll()[0]["user"] == $user) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      if (PDOError($e)) {
        return "internalError";
      }
    } finally {
      $conn = null;
    }
  }
  function updateNote($conn, $user, $title, $newTitle, $newContent) {
    $title = str_replace(" ", "_", $title);
    $newTitle = str_replace(" ", "_", $newTitle);
    $newDir = "/notedb/$user/$newTitle.txt";
    try {
      $query = $conn->prepare("SELECT dir FROM note WHERE title = :ttl");
      $query->bindParam(":ttl", $title);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $dir = $query->fetchAll();
      $dir = $dir[0]["dir"];
      if ($dir !== $newDir) {
        exec("mv ..$dir ..$newDir");
      }
      if ($content = fopen("..$newDir", "w+")) {
        //se usiamo r+ non possiamo eliminare caratteri, con w+ il file viene distrutto e riscritto.
        fwrite($content, $newContent);
        fclose($content);
        $query = $conn->prepare("UPDATE note SET title = :newTtl WHERE title = :ttl");
        $query->bindParam(":newTtl", $newTitle);
        $query->bindParam(":ttl", $title);
        $query->execute();
        $query = $conn->prepare("UPDATE note SET dir = :newDir WHERE title = :newTtl");
        $query->bindParam(":newTtl", $newTitle);
        $query->bindParam(":newDir", $newDir);
        $query->execute();
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      PDOError($e);
      return false;
    } finally {
      $conn = null;
    }
  }
?>
