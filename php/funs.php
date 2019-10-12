<?php
  require_once "core.php";

  /*
   * Funzione per creare delle entry nel journalctl (log live per debug)
   *
   * @param $s stringa da inserire
  */
  function logD(String $s) {
    shell_exec("logger $s");
  }

/*
 * Funzione per verificare se l'utente non ha superato i 5 failed access
 *
 * @param $usr Lo username dell'utente da ricercare
 * @param $conn La connessione che sto usando per comunicare con il DB
 *
 * @return true Se l'utente ha 5 o meno di 5 failed access
 * @return false Se l'utente ha più di 5 failed access
 */
  function accLimit($usr, $conn){
    $query = $conn->prepare("SELECT fail_acc FROM user WHERE username = :username");
    $query->bindParam(":username", $usr);
    $query->setFetchMode(PDO::FETCH_ASSOC);
    $query->execute();
    $acc = $query->fetchAll();
    if($acc[0]["fail_acc"] <= 5){
      return true;
    } else {
      return false;
    }
  }

  /*
   * Aggiunge un utente con i parametri passati
   *
   * @param $username Lo username che si vuole dare al nuovo user
   * @param $password La password che si vuole dare al nuovo user
   * @param $email La mail che si vuole collegare al nuovo user
   * @param $acc_lvl L'acc_lvl che si vuole assegnare al nuovo user
   * @param $fail_acc Il numero di fail_acc che si vogliono attribuire al nuovo user
   * @param $last_log La data dell'ultimo log dell'utente
   *
   * @return "passed" Se tutto è andato bene
   * @return "internalError" Se manca username o password o email o se viene sollevato una PDOException durente il binding o quando viene lanciata la query
   */
  function mysqlWriteCrd(String $username, String $password, String $email, int $acc_lvl, int $fail_acc, String $last_log) {
    //$email = '"'.$email.'"';
    if(($username == " ") || ($password == " ") || ($email == " ")){
	    return "internalError";
    }
    if(($fail_acc<0) || ($fail_acc>5)){
	    $fail_acc = 0;
    }
    try {
      $conn = connectDb();
      $query = $conn->prepare("INSERT INTO user (username, pw, mail, acc_lvl, fail_acc, last_log) VALUES (:username, :password, :email, :acc_lvl, :fail_acc, :last_log)");
      $query->bindParam(":username", $username);
      $query->bindParam(":password", $password);
      $query->bindParam(":email", $email);
      $query->bindParam(":acc_lvl", $acc_lvl);
      $query->bindParam(":fail_acc", $fail_acc);
      $query->bindParam(":last_log", $last_log);
      $query->execute();
      return "passed";
    } catch(PDOException $e) {
      if (PDOError($e)) {
        return "ge";
      } else {
        return "internalError";
      }
    } finally {
      $conn = null;
    }
  }

  /*
   * Esegue il login e restituisce una stringa con il feedback e se sbagliata la pw aggiorna il fail_acc
   *
   * @param $cnfUsr Lo username dello username da testare
   * @param $cnfPw La password hashata da testare
   *
   * @return "true" Se esiste lo username e la pw corrisponde (fail_acc azzerati)
   * @return "false" Se esiste lo username ma la password è sbagliata, o se non esiste lo username
   * @return "bannato" Se uno ha raggiunto il fail_acc limite e viene bannato
   * @return "internalError" Se c'é stata una PDOException
   */
  function login(String $cnfUsr, String $cnfPw) : String {
    logD("Logging: $cnfUsr, $cnfPw");
    require_once 'ips.php';
    try {
      $conn = connectDb();
      $query = $conn->prepare("SELECT * FROM user WHERE username = :username");
      $query->bindParam(":username", $cnfUsr);
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $query->execute();
      $userinfo = $query->fetchAll();
      if (!empty($userinfo[0]['username'])) {
        if(accLimit($userinfo[0]["username"], $conn)) {
          if ($userinfo[0]["pw"] == $cnfPw) {
            $query = $conn->prepare("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
            return "true";
          } else {
            $query = $conn->prepare("UPDATE user SET fail_acc = fail_acc+1 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
            return "false";
          }
        } else {
          if ($userinfo[0]["pw"] == $cnfPw) {
            $query = $conn->prepare("UPDATE user SET last_log = NOW(), fail_acc = 0 WHERE username = :username");
            $query->bindParam(":username", $cnfUsr);
            $query->execute();
	          return "true";
	        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
            blockIp($ip, $conn, $cnfUsr);
            return 'bannato';
          }
        }
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
        if(blockIpTmp($ip, $conn)) {
          return 'bannato';
        } else {
          return "false";
        }
      }
    } catch(PDOException $e) {
      PDOError($e);
      return "internalError";
    } finally {
      $conn = null;
    }
  }

  /*
   * La funzione serve a verificare che uno user con il dato username sia presente nel DB
   *
   * @param $username Lo username che deve avere lo user nel DB
   *
   * @return true Se il dato username è contenuto nel DB
   * @return false Se il dato username non è presente nel DB
   */
  function mysqlChckUsr(String $username) : bool {
    try {
      $conn = connectDb();
      $query = $conn->prepare("SELECT username FROM user WHERE username LIKE :username");
      $query->bindParam(":username", $username);
      $query->execute();
      $users = $query->fetchAll();
      if (!empty($users)) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      PDOError($e);
    } finally {
      $conn = null;
    }
  }

  /*
   * La funzione ritorna il livello di accesso di un utente
   *
   * @param $user Lo username dell'utente del quale si vuole sapere l'acc_lvl
   *
   * @return Il numero corrispondente all'acc_lvl
   */
  function getAcclvl($user) {
    try {
      $conn = connectDb();
      $getLvl = $conn->prepare("SELECT acc_lvl FROM user WHERE username = :usr");
      $getLvl->bindParam(":usr", $user);
      $getLvl->execute();
      $result = $getLvl->fetchAll();
      return $result[0]["acc_lvl"];
    } catch(PDOException $e) {
      PDOError($e);
      return "IEAG";
    } finally {
      $conn = null;
    }
  }

  /*
   * Serve ad attivare o disattivare lo stato manutenzione
   *
   * @param $val Il valore a cui voglio settare manutenzione (TRUE per attivata, FALSE per disattivata)
   *
   * @return "done" Se la query di modifica è andata a buon $fine
   * @return "MANAA" Se lo stato era già attivato
   * @return "MANAT" Se lo stato era già disattivato
   * @return "IEMANS" Se viene sollevato un'PDOException
   */
  function setManStatus(bool $val) {
    if (getManStatus() && $val == true) {
      return "MANAA";
    } elseif (!getManStatus() && $val != true) {
      return "MANAT";
    } else {
      try {
        $conn = connectDb();
        $query = $conn->prepare("UPDATE manutenzione SET val = :val");
        $val = (int)$val;
        $query->bindParam(':val', $val);
        $query->execute();
        return "done";
      } catch(PDOException $e) {
        PDOError($e);
        return "IEMANS";
      } finally {
        $conn = null;
      }
    }
  }

  /*
   * Serve a ritornare il valore manutenzione
   *
   * @return true Se è attivata la manutenzione
   * @return false Se è disattivata la manutenzione
   */
  function getManStatus() {
    try {
      $conn = connectDb();
      $query = $conn->query("SELECT val FROM manutenzione");
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      if ($result[0]["val"] == 1) {
        return true;
      } else {
        return false;
      }
    } catch(PDOException $e) {
      PDOError($e);
      return "IEMANR";
    } finally {
      $conn = null;
    }
  }

  /*
   * Prende due date sotto forma di stringhe e restituisce la differenza
   *
   * @param inizio La data iniziale
   * @param fine La data finale
   *
   * @return La differenza fra due date riportate come int
   */
  function differenzaData($inizio, $fine){
    $inizio = strtotime($inizio);
    $fine = strtotime($fine);

    return ($fine - $inizio);
  }


  /*
   * La funzione fa una ricerca dei dept con il nome e/o il codice e poi ritorna una matrice con le informazioni, se si lascia NULL un parametro verrà considerato %
   *
   * @param $conn La connessione che stiamo usando
   * @param $name Il nome del dept che vogliamo ricercare
   * @param $id L'id del dept che vogliamo ricercare
   *
   * @return
   * @return "internalError" Se viene sollevata una eccezione PDOException
   */
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
    return $results;
  }

  /*
   * La funzione fa una ricerca delle subj con il nome e/o il codice e poi ritorna una matrice con le informazioni, se si lascia NULL un parametro verrà considerato %
   *
   * @param $conn La connessione che stiamo usando
   * @param $name Il nome della subj che vogliamo ricercare
   * @param $id L'id della subj che vogliamo ricercare
   *
   * @return
   * @return "internalError" Se viene sollevata una eccezione PDOException
   */
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
    return $results;
  }


  /*
   * La funzione serve a ricercare uno user dentro la tabella user inserrendo vari parametri, se alcuni di essi vengono lasciati NULL veranno considerati nella query come %, verranno poi restituite una o più tuple con gli elementi che rispettano i parametri
   *
   * @param $conn La connessione con la quale stiamo lavorando
   * @param $username Lo username dell'utente di cui vogliamo le informazioni'(Se è "" diventa % nella query)
   * @param $mail La mail dello user di cui vogliamo le informazioni (Se è "" diventa % nella query)
   * @param $acc_lvl Il grado di accesso dell'utente di cui vogliamo le informazioni (Se è NULL diventa TRUE nella query)
   * @param $fail_acc Il numero di failed access dell'utente di cui vogliamo le informazioni
   * @param last_log_from La data minima dell'ultimo login dell'utente di cui vogliamo le informazioni (Se è "" diventa TRUE nella query)
   * @param last_log_to La data massima dell'ultimo login dell'utente di cui vogliamo le informazioni (Se è "" diventa TRUE nella query)
   *
   * @return $result[x]["yyy"] Un array nel quale ci sono tutti gli user che rispettano i parametri ineriti dove x è l'ordine di sorting nella query (parte da 0) e yyy è il nome dell'attributo che vogliamo visualizzare
   * @return "internalError" Se viene sollevata una PDOException
   */
  function user($conn, $username, $mail, $acc_lvl, $fail_acc, $last_log_from, $last_log_to){

    if($conn == NULL){
      return "internalError";
    }
    if($username == ""){
      $username = '%';
    }
    if($mail == ""){
      $mail = '%';
    }
    if($acc_lvl == NULL){
      $acc_lvl = "%";
    }
    if ($fail_acc == NULL) {
      $fail_acc = "%";
    }
    if($last_log_from == ""){
      $last_log_from = "%";
    }
    if($last_log_to == ""){
      $last_log_to = date("Y-m-d H:i:s");
    }
    try {
      $query = $conn->prepare("SELECT * FROM user WHERE (username LIKE :usrn) AND (mail LIKE :email) AND (acc_lvl LIKE :acclvl) AND (fail_acc LIKE :failacc) AND (last_log BETWEEN :lastlogfrom AND :lastlogto)");
      $query->bindParam(':usrn', $username);
      $query->bindParam(':email', $mail);
      $query->bindParam(':acclvl', $acc_lvl);
      $query->bindParam(':failacc', $fail_acc);
      $query->bindParam(':lastlogfrom', $last_log_from);
      $query->bindParam(':lastlogto', $last_log_to);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $result = $query->fetchAll();
      $results = array();
      foreach ($result as $row){
        array_push($results, array(
          "username"=>$row["username"],
          "last_log"=>$row["last_log"]
        ));
      }
      return $results;
    } catch(PDOException $e) {
      return "internalError";
    } finally {
      $conn = null;
    }
  }

    /*
     * La funzione ritorna una nota che rispetta i parametri inseriti
     *
     *  @param $conn La connessione con la quale stiamo lavorando
     * @param $title Il titolo della nota cercare
     * @param $dir La directpry in cui si trova la nota da cercare
     * @param $user L'utente che ha scritto la nota da carcare
     * @param $subj La materia a cui appartiene la nota da cercare
     * @param $years L'array con gli anni selezionati dall'utente dove true o false indicano se l'anno year[i] è stato selezionato (quindi true) o meno (quindi false)
     * @param $dept Il dipartimento a cui appartiene la nota
     * @param $datefrom La data minima di creazione della nota
     * @param $dateto La data massima di creazione della nota
     * @param $order Inserire il nome dell'attributo secondo cui si vuole ordinare il risultato della query
     * @param $v Il verso di ordinamento dei risultati ("DESC" o "ASC")
     *
     * @return
     * @return "internalError" Se vengono sollevate delle PDOException
     */
    function searchNote($conn, $title, $dir, $user, $subj, $years, $dept, $datefrom, $dateto, $order, $v) {
      if ($title == NULL) {
        $title = "%";
      }
      if ($dir == NULL) {
        $dir = "%";
      }
      if ($user == NULL) {
        $user = "%";
      }
      if ($subj == NULL || $subj == "Tutto") {
        $subj = "%";
      }
      if ($years[0] === "true") {
        $year1 = 1;
      } else {
        $year1 = 0;
      }
      if ($years[1] === "true") {
        $year2 = 2;
      } else {
        $year2 = 0;
      }
      if ($years[2] === "true") {
        $year3 = 3;
      } else {
        $year3 = 0;
      }
      if ($years[3] === "true") {
        $year4 = 4;
      } else {
        $year4 = 0;
      }
      if ($years[4] === "true") {
        $year5 = 5;
      } else {
        $year5 = 0;
      }
      if ($dept == NULL || $dept == "Tutto") {
        $dept = "%";
      }
      if ($datefrom == NULL) {
        $datefrom = "%";
      }
      if ($dateto == NULL) {
        $dateto = date("Y-m-d H:i:s");
      }
      if ($order == NULL) {
        $order = "date";
      }
      if ($v == NULL) {
        $v = "DESC";
      } elseif ($v == "decrescente") {
        $v = "DESC";
      } else {
        $v = "ASC";
      }

      try {
        $query = $conn->prepare("SELECT * FROM note WHERE (title LIKE :ttl) AND (dir LIKE :dir) AND (user LIKE :usr) AND (subj LIKE :subj) AND ((year LIKE :year1) OR (year LIKE :year2) OR (year LIKE :year3) OR (year LIKE :year4) OR (year LIKE :year5)) AND (dept LIKE :dept) AND (date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
        $title = str_replace(" ", "_", $title);
        $query->bindParam(":ttl", $title);
        $query->bindParam(":dir", $dir);
        $query->bindParam(":usr", $user);
        $query->bindParam(":subj", $subj);
        $query->bindParam(":year1", $year1);
        $query->bindParam(":year2", $year2);
        $query->bindParam(":year3", $year3);
        $query->bindParam(":year4", $year4);
        $query->bindParam(":year5", $year5);
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

    /*
     * La funzione inserisce una nuova nota nella table note
     *
     * @param $conn La connessione che stiamo usando
     * @param $title Il titolo della nota che vogliamo inserire
     * @param $user L'utente che sta creando la nota
     * @param $subj La materia a cui si riferisce l'appunto
     * @param $dept Il dept a cui si riferisce la dept
     * @param $year La classe alla quale si riferisce la nota che si sta scrivendo [1/2/3/4/5]
     * @param $content Il testo contenuto nella nota
     *
     * @return true Se tutto va come deve e la nota viene caricata senza problemi
     */
    function writeNote($conn, String $title, String $user, String $subj, String $dept, int $year, String $content) {
      $title = str_replace(" ", "_", $title);
      $dir = "/notedb/$user/$title.txt";
      $date = date("Y-m-d H:i:s");
      if ($year < 1 || $year > 5) {
        return "yearOutBound";
      }
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
          die(json_encode("NOTEW"));
        }
        error_log("noteFile: " . $noteFile);
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

    /*
     * La funzione cancella una nota dato il suo titolo
     *
     * @param $conn La connessione che stiamo usando
     * @param $title Il titolo della nota da cancellare
     *
     * @return true Se tutto va come deve e viene cancellata
     * @return false Se viene sollevata una PDOException
     */
    function delNote($conn, String $title) {
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

    /*
     * La funzione dice se è presente la nota con il titolo $title fra le note
     *
     * @param $conn La connessione che vogliamo usare
     * @param $title Il titolo della nota di cui vogliamo verificare la presenza
     *
     * @return true Se una nota con titolo $title è già presente
     * @return false Se non c'è nessuna nota con quel titolo o se è stato sollevata una PDOException
     */
    function checkNote($conn, String $title) {
      $title = str_replace(" ", "_", $title);
      try {
        $query = $conn->prepare("SELECT user FROM note WHERE title = :ttl");
        $query->bindParam(":ttl", $title);
        $query->execute();
        $result = $query->fetchAll();
        if (empty($result[0]["user"])) {
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

    /*
     * La funzione ritorna la nota sotto forma di array in cui in ogni elemento c'è una riga diversa del file comporeso il \n
     * @param $conn La connessione che stiamo usando
     * @param $title Il titolo della nota di cui vogliamo leggere il contenuto
     *
     * @return Un array in cui in ogni elemento c'è una riga del file seguito ovviamente dal suo \n
     * @return false Se viene sollevata una PDOException
     */
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

    /*
     *
     * @deprecated Non viene più usata la tabella mark, è stata sotituita da like
     *
     * La funzione mi permette di ricercare una nota ed ottenere il solito array in cui in ogni elemento son poi contenuti tramite un subarray associativo i nomi degli attributi
     *
     * @param $conn La connessione che stiamo usando
     * @param $id L'id del voto che stiamo cercando
     * @param $user L'utente che ha caricato il voto
     * @param $title Il titolo della nota che ha ricevuto il voto
     * @param $mark Il voto vche è stato dato nella tupla mark (ovvero il valore della valutazione)
     * @param $datefrom La data minima in cui deve essere stato caricato il voto
     * @param $dateto La data massima entro la quale deve essere stato caricato il voto
     * @param $code Il nome dell'attributo tramite il quale dobbiamo ordinare i risultati della query
     *
     * @return Un array[x]['yyy'] nella x va il numer del campo in ordine di sorting della query, su yyy ci va il campo che voglio leggere dall'elemento x
     * @return ''internalError' Se viene sollevata una PDOException
     */
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

    /*
     * La funzione ricerca un report dando i seguenti parametri come filtri
     *
     * @param $conn La connessione che stiamo usando
     * @param $id L'id del report che stiamo cercando
     * @param $user L'utente che ha caricato la report che stiamo cercando
     * @param $title Il tiolo della nota sulla quale deve essere stato fatto il report
     * @param $text Il testo che deve essere scritto dentro la nota
     * @param $datefrom La data minima in cui deve essere stata scritta la nota
     * @param $dateto La data massima entro la quale deve essere stata scritta la nota
     * @param $code L'attributo secondo cui dobbiamo ordinare i risultati della query
     *
     * @return Un array[x]['yyy'] In cui su x deve andare il numero di sorting della tupla nella query e su yyy ci va il nome dell'attributo di cui ogliamo conoscere il contenuto per la tupla numero x
     * @return "internalError" Se viene sollevata una PDOException
     */
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
    /*
     * Serve a cercare un commento fra tutto quelli nel database che rispetti i parametri che inseriamo come filtri
     *
     * @param $conn La connessione che stiam usando
     * @param $id L'id del commento che stiamo cercando
     * @param $user L'utent che ha creato il commento che stiamo cercando
     * @param $title Il titolo della note di cui stiamo cercando il commento
     * @param $review Il contenuto del commento che stiamo cercando
     * @param $datefrom La data minima entro cui deve essere stata scritto il commento
     * @param $dateto La data entro la quale deve essere stata scritta la nota
     * @param $order Il nome dell'attributo con il quale voglio ordinare il sorting order della query
     * @param $v Se voglio ordinare il modo ascendente o discendente (ASC o DESC)
     *
     * @return Il solito array[x]['yyy'] In cui x è l'ordine di sorting in cui la tupla è stata ordinata e yyy l'attributo che vogliamo leggere della tupla x
     * @return "internalError" Se viene sollevata una PDOException
     */
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
    /*
     * La funzione aggiunge al comemnto alla tabella revw che ha come attributi i vari parametri della funzione
     *
     * @param $conn La connessione che vogliamo usare
     * @param $user L'utente che ha caricato il commento
     * @param $title Il titolo della nota sulla quale è stato caricato il commento
     * @param $content Il contenuto del commento
     *
     * @return Ritorna un'array associativo dove dentro [state] c'è true se l' operazione è andata a abuon fine altrimenti false, dentro [id] c'è l'id del commento
     * @return false Se uno dei parametri è nullo
     * @return "internalError" Se viene sollevata una PDOException
     */
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
        //dobbiamo ritornare anche l'id per la cancellazione dei commenti postati nella pagina senza che sia ricaricata
        $query = $conn->prepare("SELECT id, date FROM revw WHERE (user = :user) AND (review LIKE :content)");
        $query->bindParam(":user", $user);
        $query->bindParam(":content", $content);
        $query->execute();
        $commentId = ($result = $query->fetchAll())[0]["id"];
        $date = $result[0]["date"];
        return ["state"=> true, "id"=> $commentId, "date"=>$date];
      } catch(PDOException $e) {
        if (PDOError($e)) {
          return "internalError";
        }
      } finally {
        $conn = null;
      }
    }

    /*
     * La funzione serve ad eliminare un commento dato il suo id
     *
     * @param $conn La connesione che stiamo usando
     * @param $id L'id della nota che vogliamo cancellare
     *
     * @return false se l'id non è valido
     * @return "internalError" Se viene sollevata una PDOException
     * @return true Se tutto viene cancellato correttamente
     */
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

    /*
     * Dice se un utente è il creatore della nota dati i parametri
     *
     * @param $conn La connessione che stiamo usando
     * @param $title Il titolo della nota di cui dobbiamo verificare il proprietario
     * @param $user L'utente che deve corrispondere al possessore della note per esserne il creatore
     *
     * @return false Se il titolo o il nome dell'utente della nota non è valido o se non è lui il possessore
     * @return true Se $user è il creatore della nota $title
     * @return "itnernalError" Se viene sollevata una PDOException
     */
    function isNoteOwner($conn, $title, $user) {
      if ($conn == "null" || $conn == NULL || $title == NULL || $user == NULL) {
        return false;
      }
      try {
        $title = str_replace(" ", "_", $title);
        $query = $conn->prepare("SELECT user FROM note WHERE title LIKE :ttl");
        $query->bindParam(":ttl", $title);
        $query->execute();
        if (null !== ($result = $query->fetchAll()[0]["user"]) && !empty($result)) {
          if ($result === $user) {
            return true;
          } else {
            return false;
          }
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

    /*
     * La funzione viene usata per aggiornare i parametri inseriti nella nota
     *
     * @param $conn La connessione che stiamo usando
     * @param $user Il nome dell'utente che ha creato la nota
     * @param $title Il vecchio titolo della nota da cambiare
     * @param $newTitle Il nuovo titolo della nota da aggiornare
     * @param $newContent Il nuovo contenuto della nota se vogliamo aggiorarlo
     *
     * @return true Se la nota viene aggiornata con successo
     * @return false Se per qualche ragione non viene aperto il file o se viene sollevata una PDOException
     */
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

    /*
     * La funzione inserisce il rating di una nota se l'utente non l'aveva già inserito o se il rating che vuole inserire ora è diverso da quelle che aveva inserito in passato
     *
     * @param $username Lo username che vuole inserire la nota
     * @param $title Il titolo della nota della quale si vuole inserire il rating
     * @param $rating Il rating che si vuole isnerire (true per Mi piace e false per Non mi piace)
     *
     * @return true Se il rating viene aggiunto o aggiornato senza problemi
     * @return "internalError" Se è stata sollevta una PDOexception
     * @return false Se il rating che si vuole inserire era già presente
     */
    function rateNote(String $username, String $title, bool $rating) {
      $title = str_replace(" ", "_", $title);
      if ($rating) {
        $rating = 1;
      } else {
        $rating = 0;
      }
      try {
        if(alreadyRated($username, $title) == 0) {
           $conn = connectDb();
           $query = $conn->prepare("INSERT INTO rate (user, note, rate, date)  VALUES (:username, :title, :rating, NOW())");
           $query->bindParam(":username", $username);
           $query->bindParam(":title", $title);
           $query->bindParam(":rating", $rating);
           $query->execute();
           return true;
         } elseif((alreadyRated($username, $title) == 1) && (getRate($username, $title) != -1) && (getRate($username, $title) != $rating)) {
           $conn = connectDb();
           $query = $conn->prepare("UPDATE rate SET rate = :rating  WHERE (user = :username) AND (note = :title)");
           $query->bindParam(":rating", $rating);
           $query->bindParam(":username", $username);
           $query->bindParam(":title", $title);
           $query->execute();
           return true;
         } else {
           return false;
         }
      } catch(PDOException $e) {
        PDOError($e);
        return "internalError";
      } finally {
        $conn = null;
      }
    }

    /*
     * La funzione serve a verificare se l'utente ha già inserito un rating per la nota, ritorna il numero di rating già inseriti (dovrebbe essere fra 1 e 0)
     *
     * @param $username Lo username dell'utente di cui si vuole controllare il rate
     * @param title La nota sulla quale si cerca il possibile rate dell'utente
     *
     * @return Il numero di rate messi dall'utente alla nota $title (dovrebbe essere fra 0 e 1)
     * @return -1 Se è stata sollevata una eccezzione PDOException
     */
    function alreadyRated(String $username, String $title) {
      $title = str_replace(" ", "_", $title);
      try {
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (user = :username) AND (note = :title)");
        $query->bindParam(":username", $username);
        $query->bindParam(":title", $title);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $result = $query->fetchAll();
        return $result[0]["num"];
      } catch(PDOException $e) {
        PDOError($e);
        return -1;
      } finally {
        $conn = null;
      }
    }

    /*
     * Restituisce il rate dato a una nota da un utente se c'è il rate, altrimenti restituisce -1
     *
     * @param $username Lo username del quale si vuole fare la ricerca
     * @param $title Il titolo della nota sulla quale si deve cercare il rate
     *
     * @return -1 Se non c'è alcun rate su quella nota da parte dell'utente o se è stata sollevata una PDOException
     * @return 1 Se il rate è TRUE
     * @return 0 Se il rate è FALSE
     */
    function getRate(String $username, String $title){
      $title = str_replace(" ", "_", $title);
      try {
        if (alreadyRated($username, $title) != 1) {
          return -1;
        } else {
          $conn = connectDb();
          $query = $conn->prepare("SELECT rate FROM rate WHERE (user = :username) AND (note = :title)");
          $query->bindParam(":username", $username);
          $query->bindParam(":title", $title);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $result = $query->fetchAll();
          return $result[0]["rate"];
        }
      } catch(PDOException $e) {
        PDOError($e);
        return -1;
      } finally {
        $conn = null;
      }
    }

    /*
     * La funzione ritorna il numero di rate positivi sulla nota $note
     *
     * @param $note Il nome della nota nella quale cercare i rate
     *
     * @return Il numero di rate positivi
     * @return false In caso di errore se viene sollevata una PDOException
     */
    function getLikes($note){
      $note = str_replace(" ", "_", $note);
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (note = :note) AND (rate = 1)");
        $query->bindParam(":note", $note);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $result = $query->fetchAll();
        return $result[0]["num"];
      }catch(PDOException $e){
        PDOError($e);
        return false;
      }finally{
        $conn = null;
      }
    }

    /*
     * La funzione ritorna il numero di rate negativi sulla nota $note
     *
     * @param $note Il nome della nota nella quale cercare i rate
     *
     * @return Il numero di rate negativi
     * @return false In caso di errore se viene sollevata una PDOException
     */
    function getDislikes(String $note){
      $note = str_replace(" ", "_", $note);
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (note = :note) AND (rate = 0)");
        $query->bindParam(":note", $note);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $result = $query->fetchAll();
        return $result[0]["num"];
      }catch(PDOException $e){
        PDOError($e);
        return false;
      }finally{
        $conn = null;
      }
    }

    /*
     * La funzione ritorna il numero totale dei rate lasciati dall'utente $user sotto tutte le note del DB
     *
     * @param $user Il nome dell'utente del quale cercare il numero dei rate
     *
     * @return Il numero di rate lasciati dall'utente (non i rate che gli altri lasciano sotto le sue note ma quelli che lui lascia in tutte le note del database)
     * @return false In caso di errore se viene sollevata una PDOException
     * @return "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
     */
    function getLeftRate(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $conn = connectDb();
          $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE user = :user");
          $query->bindParam(":user", $user);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $result = $query->fetchAll();
          return $result[0]["num"];
        }catch(PDOException $e){
          PDOError($e);
          return false;
        }finally{
          $conn = null;
        }
      } else {
        return "Utente non esistente";
      }
    }

    /*
     * La funziona ritorna la data e l'ora dell'ultimo login eseguito con successo dall'utente
     *
     * @param $user Il nome dell'utente di cui stiamo cercando l'ultimo login
     *
     * @return La data e l'ora dell'ultimo log secondo il formato in cui esso è salvato dentro il database
     * @return false Se viene sollevata una PDOException di qualche tipo
     * @return "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
     */
    function getLastLog(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $conn = connectDb();
          $query = $conn->prepare("SELECT last_log FROM user WHERE username = :user");
          $query->bindParam(":user", $user);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $result = $query->fetchAll();
          return $result[0]["last_log"];
        }catch(PDOException $e){
          PDOError($e);
          return false;
        }finally{
          $conn = null;
        }
      } else {
        return "Utente non esistente";
      }
    }

    /*
     * La funzione ritorna il numero totale dei commenti lasciati dall'utente $user sotto tutte le note del DB
     *
     * @param $user Il nome dell'utente del quale cercare il numero demçi commenti
     *
     * @return Il numero di commenti lasciati dall'utente (non i commenti che gli altri lasciano sotto le sue note ma quelli che lui lascia in tutte le note del database)
     * @return false In caso di errore se viene sollevata una PDOException
     * @return "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
     */
    function getLeftComm(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $conn = connectDb();
          $query = $conn->prepare("SELECT COUNT(*) as num FROM revw WHERE user = :user");
          $query->bindParam(":user", $user);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $result = $query->fetchAll();
          return $result[0]["num"];
        }catch(PDOException $e){
          PDOError($e);
          return false;
        }finally{
          $conn = null;
        }
      } else {
        return "Utente non esistente";
      }
    }

    /*
     * La funzione ritorna il numero totale delle note create dall'utente $user
     *
     * @param $user Il nome dell'utente del quale contare il nnumero di note
     *
     * @return Il numero di note create dall'utente e ancora presenti nel database
     * @return false In caso di errore se viene sollevata una PDOException
     * @return "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
     */
    function getNoteNum(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $conn = connectDb();
          $query = $conn->prepare("SELECT COUNT(*) as num FROM note WHERE user = :user");
          $query->bindParam(":user", $user);
          $query->execute();
          $query->setFetchMode(PDO::FETCH_ASSOC);
          $result = $query->fetchAll();
          return $result[0]["num"];
        }catch(PDOException $e){
          PDOError($e);
          return false;
        }finally{
          $conn = null;
        }
      } else {
        return "Utente non esistente";
      }
    }

    /*
     * La funzione ritorna il numero totale dei like e dislike ricevuto dall'utente
     *
     * @param $user Il nome dell'utente del quale contare il nnumero di rates
     *
     * @return Il numero totale di rate ricevuti
     * @return false In caso di errore se viene sollevata una PDOException
     * @return "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
     */
    function getReceivedRate(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $tot_rates = 0;
          $list = searchNote(connectDb(), NULL, NULL, $user, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
          foreach($list as $element){
            $tot_rates += (getLikes($element["title"]) + getDislikes($element["title"]));
          }
          return $tot_rates;
        }catch(PDOException $e){
          PDOError($e);
          return false;
        }finally{
          $conn = null;
        }
      } else {
        return "Utente non esistente";
      }
    }

  /*
   * La funzione inserisce un'immagine nel DB verificando che il formato fornito sia tra quelli ammessi e verificando che esista la nota (non verifica l'autorizzazione o meno dell'utente)
   *
   * @param $note Il nome della nota sulla quale aggiungere un immagine
   * @param $format Il formato dell'immagine
   * @param $dir La directory del server dentro cui abbiamo messo l'immagine
   * @param $picName Il nome dell'immagine
   *
   * @return "done" Se tutto è andato bene e se non sono state sollevate eccezioni
   * @return "internalError" Se è stata sollevata un'eccezione PDOException
   * @return "invalidFormat" Se il formato non è tra quelli concessi (scritti nell'array $formats)
   * @return "non_existentNote" Se la nota sulla quale si sta cercando di inserire l'immagine non esiste
   */
  function newImageEntry($note, $format, $dir, $picName) {
    if (checkNote(connectDb(), $note)) {
      $formats = ["png", "gif", "jpg", "jpeg"];
      if (in_array($format, $formats)) {
        $note = str_replace(" ", "_", $note);
        $note = str_replace("'", "sc-a", $note);
        $note = str_replace('"', "sc-q", $note);
        $picName = str_replace("'", "sc-a", $picName);
        $picName = str_replace('"', "sc-q", $picName);
        try {
          $conn = connectDb();
          $query = $conn->prepare("INSERT INTO pict (date, note, format, dir, name) VALUES (NOW(), :note, :format, :dir, :pic_name)");
          $query->bindParam(":note", $note);
          $query->bindParam(":format", $format);
          $query->bindParam(":dir", $dir);
          $query->bindParam(":pic_name", $picName);
          $query->execute();
          return "done";
        } catch(PDOException $e){
          PDOError($e);
          return "internalError";
        } finally {
          $conn = null;
        }
      } else {
        return "invalidFormat";
      }
    } else {
      return "non-existentNote";
    }
  }

  /*
   * La funzione ritorna in una matrice la dir e l'id di tutte le foto di una nota
   *
   * @param $note La nota di cui cercare i dati delle immagini
   *
   * @return Non ritorna nulla se non è stata definita alcuna nota
   * @return matrix[x]["dir"/"id"] una matrice dove x è il numero dell'immagine (in base al sorting sql) e come seconda dimensione si può scegliere "dir" per la directoryoppure "id" per l'id
   * @return "internalError" Se è stata sollevata una PDOException
   */
  function getPicsPathsAndIds($note) {
    if ($note == NULL) {
      return;
    }
    $note = str_replace(" ", "_", $note);
    try {
      $query = connectDb()->prepare("SELECT dir,id FROM pict WHERE note = :note");
      $query->bindParam(":note", $note);
      $query->execute();
      $query->setFetchMode(PDO::FETCH_ASSOC);
      $pics = $query->fetchAll();
      return $pics;
    } catch(PDOException $e){
      PDOError($e);
      return "internalError";
    } finally {
      $conn = null;
    }
  }

  /*
   * Controlla che il punto all' interno di una foto sia ripetuto solo una volta in modo da evitare di salvare dentro al server file del tipo "Foto.php.png" che può sembrare png ma in realtà è php
   *
   * @param $path Il nome del file da controllare
   *
   * @return true Se il punto è presente nel nome del file una volta sola
   * @return false Se il punto è presente più di una volta o non è proprio presente
   */
  function checkDotIteration($path) {
    if (substr_count($path, ".") === 1) {
      return true;
    } else {
      return false;
    }
  }

  /*
   * La funzione rimuove un immagine dal DB e anche dalla sua directory nel server e prima di farlo verifica che l'immagine esista e che l'utente che ne chiede la rimozione sia autorizzato
   *
   * @param $conn La connessione che stiamo usando
   * @param $note Il titolo della nota a cui deve appartenere l'immagine
   * @param $id L'id dell'immagine da cancellare
   * @param $user L'utente che chiede la cancellazione della foto (per essere autorizzato deve essere admin o il creatore della nota)
   *
   * @return "imgNotFound" Nel caso in cui l'immagine con $id dentro al $note non sia stata trovata
   * @return "notAuthorized" Nel caso in cui l'utente provi a cancellare un'immagine senza autorizzazione (no admin e no creatore nota)
   * @return "illegalDeletion" Se l'utente non è admin o creatore della nota e se non esiste immagine $id relativa a $note
   * @return "illegalError" Se viene sollevata una PDOException, quindi errore tra la comunicazione con db (spesso errori nelle query)
   * @return "done" Se tutto va come deve e non vengono sollevati errori
   */
  function removeImage($conn, $note, $id, $user) {
    $note = str_replace(" ", "_", test_input($_POST["note"]));
    $note = str_replace("'", "sc-a", $note);
    $note = str_replace('"', "sc-q", $note);
    $dir = null;
    $authBypass = false;
    $picOnDb = false;
    if (getAcclvl($_SESSION["username"]) === "1") {
      $authBypass = true;
    }
    try{
      if(isNoteOwner($conn, $note, $user)){
        $authBypass = true;
      }
      $findPic = $conn->prepare("SELECT COUNT(*) AS val FROM pict INNER JOIN note ON pict.note = note.title WHERE pict.id =:id AND note.title = :note");
      $findPic->bindParam(":id", $id);
      $findPic->bindParam(":note", $note);
      $findPic->execute();
      $result = $findPic->fetchAll();
      if($result[0]["val"] == 1){
        $picOnDb = true;
      }
      if(($authBypass == true) && ($picOnDb == true)){
            $getDir = $conn->prepare("SELECT pict.dir FROM pict INNER JOIN note ON note.title = pict.note WHERE id= :id");
            $getDir->bindParam(":id", $id);
            $getDir->execute();
            $result = $getDir->fetchAll();
            $dir = result[0]["dir"];
            $removeImg = $conn->prepare("DELETE FROM pict WHERE dir = :id");
            $removeImg->bindParam(":id", $id);
            $removeImg.execute();
            exec("rm " . $dir);
            //Questa cosa qui sopra non può sollevare errori, non sarebbe meglio mettere un catch anche per lei?
          }elseif(($authBypass == true) && ($picOnDb == false)){
            return "imgNotFound";
          }elseif(($authBypass == false) && ($picOnDb == true)){
            return "notAuthorized";
          }else{
            return "illegalDeletion";
          }
      }catch(PDOException $e){
      PDOError($e);
      return "internalError";
    }finally{
      $conn = null;
    }
    return "done";
  }

?>
