<?php
  require_once "core.php";

  /**
   * Funzione per creare delle entry nel journalctl (log live per debug)
   *
   * @param $s stringa da inserire
  */
  function logD(String $s) {
    shell_exec("logger $s");
  }

/**
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

  /**
   * Aggiunge un utente con i parametri passati
   *
   * @param $username Lo username che si vuole dare al nuovo user
   * @param $password La password che si vuole dare al nuovo user
   * @param $email La mail che si vuole collegare al nuovo user
   * @param $acc_lvl L'acc_lvl che si vuole assegnare al nuovo user
   * @param $fail_acc Il numero di fail_acc che si vogliono attribuire al nuovo user
   * @param $last_log La data dell'ultimo log dell'utente
   *
   * @return string "passed" Se tutto è andato bene
   * @return string "internalError" Se manca username o password o email o se viene sollevato una PDOException durente il binding o quando viene lanciata la query
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

  /**
   * Esegue il login e restituisce una stringa con il feedback e se sbagliata la pw aggiorna il fail_acc
   *
   * @param $cnfUsr Lo username dello username da testare
   * @param $cnfPw La password hashata da testare
   *
   * @return string "true" Se esiste lo username e la pw corrisponde (fail_acc azzerati)
   * @return string "false" Se esiste lo username ma la password è sbagliata, o se non esiste lo username
   * @return string "bannato" Se uno ha raggiunto il fail_acc limite e viene bannato
   * @return string "internalError" Se c'é stata una PDOException
   */
  function login(String $cnfUsr, String $cnfPw) : String {
    logD("Logging: $cnfUsr, $cnfPw");
    require_once 'ips.php';
    try {
      $conn = connectDb();
      $query = $conn->prepare("SELECT * FROM user WHERE BINARY username = :username");
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

  /**
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

  /**
   * La funzione ritorna il livello di accesso di un utente
   *
   * @param $user Lo username dell'utente del quale si vuole sapere l'acc_lvl
   *
   * @return int numero corrispondente all'acc_lvl
   * @return string "IEAG" Se veiene sollevata una PDOException
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

  /**
   * Serve ad attivare o disattivare lo stato manutenzione
   *
   * @param $val Il valore a cui voglio settare manutenzione (TRUE per attivata, FALSE per disattivata)
   *
   * @return string "done" Se la query di modifica è andata a buon $fine
   * @return string "MANAA" Se lo stato era già attivato
   * @return string "MANAT" Se lo stato era già disattivato
   * @return string "IEMANS" Se viene sollevato un'PDOException
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

  /**
   * Serve a ritornare il valore manutenzione
   *
   * @return true Se è attivata la manutenzione
   * @return false Se è disattivata la manutenzione
   * @return  aggiungere return dopo aver deciso cosa tornare definitivamente
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

  /**
   * Prende due date sotto forma di stringhe e restituisce la differenza
   *
   * @param $inizio La data iniziale
   * @param $fine La data finale
   *
   * @return int differenza fra due date riportate in secondi
   */
  function differenzaData($inizio, $fine){
    $inizio = strtotime($inizio);
    $fine = strtotime($fine);

    return ($fine - $inizio);
  }


  /**
   * La funzione fa una ricerca dei dept con il nome e/o il codice e poi ritorna una matrice con le informazioni, se si lascia NULL un parametro verrà considerato %
   *
   * @param $conn La connessione che stiamo usando
   * @param $name Il nome del dept che vogliamo ricercare
   * @param $id L'id del dept che vogliamo ricercare
   *
   * @return array[][] Nella prima parenti va il numero n di sorting del dept e nella seconda il campo che vogliamo conoscere dell'elemento n ("name", "code"...)
   * @return string "internalError" Se viene sollevata una eccezione PDOException
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

  /**
   * La funzione fa una ricerca delle subj con il nome e/o il codice e poi ritorna una matrice con le informazioni, se si lascia NULL un parametro verrà considerato %
   *
   * @param $conn La connessione che stiamo usando
   * @param $name Il nome della subj che vogliamo ricercare
   * @param $id L'id della subj che vogliamo ricercare
   *
   * @return array[][] Nella prima parenti va il numero n di sorting della subj e nella seconda il campo che vogliamo conoscere dell'elemento n ("name", "code"...)
   * @return string "internalError" Se viene sollevata una eccezione PDOException
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
      $query = $conn->prepare("SELECT * FROM subj WHERE (name LIKE :subj_name) AND (code LIKE :id) ORDER BY name");
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


  /**
   * La funzione serve a ricercare uno user dentro la tabella user inserendo vari parametri, se alcuni di essi vengono lasciati NULL veranno considerati nella query come %, verranno poi restituite una o più tuple con gli elementi che rispettano i parametri
   *
   * @param $conn La connessione con la quale stiamo lavorando
   * @param $username Lo username dell'utente di cui vogliamo le informazioni'(Se è "" diventa % nella query)
   * @param $mail La mail dello user di cui vogliamo le informazioni (Se è "" diventa % nella query)
   * @param $acc_lvl Il grado di accesso dell'utente di cui vogliamo le informazioni (Se è NULL diventa TRUE nella query)
   * @param $fail_acc Il numero di failed access dell'utente di cui vogliamo le informazioni
   * @param $last_log_from La data minima dell'ultimo login dell'utente di cui vogliamo le informazioni (Se è "" diventa TRUE nella query)
   * @param $last_log_to La data massima dell'ultimo login dell'utente di cui vogliamo le informazioni (Se è "" diventa TRUE nella query)
   *
   * @return array[x]["yyy"] Un array nel quale ci sono tutti gli user che rispettano i parametri ineriti dove x è l'ordine di sorting nella query (parte da 0) e yyy è il nome dell'attributo che vogliamo visualizzare
   * @return string "internalError" Se viene sollevata una PDOException o se non esiste la connesione $conn
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

    /**
     * La funzione ritorna una nota che rispetta i parametri inseriti
     *
     * @param $conn La connessione con la quale stiamo lavorando
     * @param $title Il titolo della nota cercare
     * @param $noteId L'id della nota da cercare
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
     * @return array[][] Dove nel primo capo ci va il numero n dell'ordine di sorting delle note (nel caso di più note) e nel secondo paramentro ci va il nome del campo che vogliamo leggere della nota n
     * @return string "internalError" Se vengono sollevate delle PDOException
     */
    function searchNote($conn, $title, $noteId, $dir, $user, $subj, $years, $dept, $datefrom, $dateto, $order, $v) {
      if ($title == NULL) {
        $title = "%";
      }
      if ($noteId == NULL) {
        $noteId = "%";
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
      if ($v === NULL || $v === "Decrescente" || $v = "DESC") {
        $v = "DESC";
      }else{
        $v = "ASC";
      }
      try {
        $query = connectDb()->prepare("SHOW COLUMNS FROM note");
        $query->execute();
        $result = $query->fetchAll();
        $allowed_codes = array_column($result, 'Field');
        $valid_codes = array();
        $i = 0;
        foreach ($allowed_codes as $code) {
          array_push($valid_codes, $allowed_codes[$i]);
          $i++;
        }
        if(!in_array($order, $valid_codes)){
          $order = "date";
        }
        $query = $conn->prepare("SELECT * FROM note WHERE (id LIKE :id) AND (title LIKE :ttl) AND (dir LIKE :dir) AND (user LIKE :usr) AND (subj LIKE :subj) AND ((year LIKE :year1) OR (year LIKE :year2) OR (year LIKE :year3) OR (year LIKE :year4) OR (year LIKE :year5)) AND (dept LIKE :dept) AND (date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
        $title = str_replace(" ", "_", $title);
        $query->bindParam(":id", $noteId);
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
          $results[$i]["id"] = $row["id"];
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

    /**
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
      $date = date("Y-m-d H:i:s");
      if ($year < 1 || $year > 5) {
        return "yearOutBound";
      }
      try {
        $query = $conn->prepare("INSERT INTO note (title, dir, user, subj, year, dept, date) VALUES (:ttl, :dir, :user, :subj, :year, :dept, :date)");
        $query->bindParam(":ttl", $title);
        $query->bindParam(":dir", $dir);
        $query->bindParam(":user", $user);
        $query->bindParam(":subj", $subj);
        $query->bindParam(":year", $year);
        $query->bindParam(":dept", $dept);
        $query->bindParam(":date", $date);
        $query->execute();
        $noteId = getNoteId(connectDb(), $title, $user, $date);

        $dir = "/notedb/$user/$noteId.txt";
        $noteFile = fopen("../notedb/$user/$noteId.txt", "w+");
        if ($noteFile == false) {
          return ["status"=>"NOTEW"];
        } else {
          $query= $conn->prepare("UPDATE note SET dir = :dir WHERE id = :id");
          $query->bindParam(":dir", $dir);
          $query->bindParam(":id", $noteId);
          $query->execute();

          error_log("noteFile: " . $noteFile);
          fwrite($noteFile, $content);
          fclose($noteFile);

          return ["status"=>"done", "date"=>$date];
        }
      } catch (PDOException $e) {
        PDOError($e);
        error_log("CASO IN FUNS ERRORE PDO");
        die(json_encode(["status"=> "NOTEW"]));
      } finally {
        $conn = null;
      }
    }

    /**
     * La funzione cancella una nota dato il suo id
     *
     * @param $conn La connessione che stiamo usando
     * @param $noteId L'id della nota da cancellare
     *
     * @return true Se tutto va come deve e viene cancellata
     * @return false Se viene sollevata una PDOException
     */
    function delNote($conn, String $noteId) {
      error_log("Deleting: $noteId");
      try {
        $query = $conn->prepare("SELECT dir FROM note WHERE id = :id");
        $query->bindParam(":id", $noteId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $dir = $query->fetchAll();
        $dir = $dir[0]["dir"];
        exec("rm ..$dir");
        $query = $conn->prepare("DELETE FROM note WHERE id = :id");
        $query->bindParam(":id", $noteId);
        $query->execute();

        return true;
      } catch (PDOException $e) {
        PDOError($e);
        return false;
      } finally {
        $conn = null;
      }
    }

    /**
     * La funzione dice se è presente la nota con id $noteId fra le note
     *
     * @param $conn La connessione che vogliamo usare
     * @param $noteId L'id della nota di cui vogliamo verificare la presenza
     *
     * @return true Se una nota id $noteId è già presente
     * @return false Se non c'è nessuna nota con quell'id o se è stato sollevata una PDOException
     */
    function checkNote($conn, String $noteId) {
      try {
        $query = $conn->prepare("SELECT COUNT(*) as num FROM note WHERE id=:noteId");
        $query->bindParam(":noteId", $noteId);
        $query->execute();
        $result = $query->fetchAll();
        if ($result[0]["num"] != 1) {
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

    /**
     * La funzione ritorna la nota sotto forma di array in cui in ogni elemento c'è una riga diversa del file compreso il \n
     * @param $conn La connessione che stiamo usando
     * @param $noteId L'id della nota di cui vogliamo leggere il contenuto
     *
     * @return array[] array in cui in ogni elemento c'è una riga del file seguito ovviamente dal suo \n
     * @return false Se viene sollevata una PDOException
     */
    function getNote($conn, String $noteId) {
      try {
        $query = $conn->prepare("SELECT dir FROM note WHERE id = :noteId");
        $query->bindParam(":noteId", $noteId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $dir = $query->fetchAll();
        $dir = $dir[0]["dir"];
        return file("..$dir");
      } catch (PDOException $e) {
        PDOError($e);
        return false;
      } finally {
        $conn = null;
      }
    }


    /**
     * La funzione ricerca un report dando i seguenti parametri come filtri
     *
     * @param $conn La connessione che stiamo usando
     * @param $id L'id del report che stiamo cercando
     * @param $user L'utente che ha caricato la report che stiamo cercando
     * @param $noteId L'ID della nota della quale deve essere fatto il report
     * @param $text Il testo che deve essere scritto dentro la nota
     * @param $datefrom La data minima in cui deve essere stata scritta la nota
     * @param $dateto La data massima entro la quale deve essere stata scritta la nota
     * @param $order L'attributo secondo cui dobbiamo ordinare i risultati della query
     *
     * @return array[x]['yyy'] In cui su x deve andare il numero di sorting della tupla nella query e su yyy ci va il nome dell'attributo di cui ogliamo conoscere il contenuto per la tupla numero x
     * @return string "internalError" Se viene sollevata una PDOException
     */
    function searchRepo($conn, $id, $user, $noteId, $text, $datefrom, $dateto, $order){

      if($conn == "null"){
        return -1;
      }
      if($id == NULL){
        $id = "%";
      }
      if($user == NULL){
        $user = '%';
      }
      if($noteId == NULL){
        $noteId = "%";
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
      try {
        $query = connectDb()->prepare("SHOW COLUMNS FROM repo");
        $query->execute();
        $result = $query->fetchAll();
        $allowed_codes = array_column($result, 'Field');
        $valid_codes = array();
        $i = 0;
        foreach ($allowed_codes as $code) {
          array_push($valid_codes, $allowed_codes[$i]);
          $i++;
        }
        if(!in_array($order, $valid_codes)){
          $order = "date";
        }
        $query = $conn->prepare("SELECT * FROM repo WHERE (id LIKE :id) AND (user LIKE :user) AND (note = :note) AND (text LIKE :text) AND(date BETWEEN :datefrom AND :dateto) ORDER BY $order");
        $query->bindParam(':id', $id);
        $query->bindParam(':user', $user);
        $query->bindParam(':note', $noteId);
        $query->bindParam(':text', $text);
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
          $results[$i]["note"] = $row["note"];
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
    /**
     * Serve a cercare un commento fra tutto quelli nel database che rispetti i parametri che inseriamo come filtri
     *
     * @param $conn La connessione che stiam usando
     * @param $id L'id del commento che stiamo cercando
     * @param $user L'utent che ha creato il commento che stiamo cercando
     * @param $noteId L'id della nora di cui ricercare il commento
     * @param $review Il contenuto del commento che stiamo cercando
     * @param $datefrom La data minima entro cui deve essere stata scritto il commento
     * @param $dateto La data entro la quale deve essere stata scritta la nota
     * @param $order Il nome dell'attributo con il quale voglio ordinare il sorting order della query ("ascendente" o "discendente")
     * @param $v Se voglio ordinare il modo ascendente o discendente (ASC o DESC)
     *
     * @return array[x]['yyy'] In cui x è l'ordine di sorting in cui la tupla è stata ordinata e yyy l'attributo che vogliamo leggere della tupla x
     * @return string "internalError" Se viene sollevata una PDOException
     */
    function searchRevw($conn, $id, $user, $noteId, $review, $datefrom, $dateto, $order, $v){

      if($conn == "null"){
        return -1;
      }
      if($id == NULL){
        $id = "%";
      }
      if($user == NULL){
        $user = '%';
      }
      if($noteId == NULL){
        $noteId = "%";
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
      if ($v == NULL) {
        $v = "DESC";
      } elseif ($v == "ascendente") {
        $v = "ASC";
      } else {
        $v = "DESC";
      }
      try {
        $query = connectDb()->prepare("SHOW COLUMNS FROM revw");
        $query->execute();
        $result = $query->fetchAll();
        $allowed_codes = array_column($result, 'Field');
        $valid_codes = array();
        $i = 0;
        foreach ($allowed_codes as $code) {
          array_push($valid_codes, $allowed_codes[$i]);
          $i++;
        }
        if(!in_array($order, $valid_codes)){
          $order = "date";
        }
        $query = $conn->prepare("SELECT * FROM revw WHERE (id LIKE :id) AND (user LIKE :user) AND (note LIKE :noteId) AND (review LIKE :review) AND(date BETWEEN :datefrom AND :dateto) ORDER BY $order $v");
        $query->bindParam(':id', $id);
        $query->bindParam(':user', $user);
        $query->bindParam(':noteId', $noteId);
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
          $results[$i]["note"] = $row["note"];
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
    /**
     * La funzione aggiunge al comemnto alla tabella revw che ha come attributi i vari parametri della funzione
     *
     * @param $conn La connessione che vogliamo usare
     * @param $user L'utente che ha caricato il commento
     * @param $noteId L'id della nota sulla quale è stato caricato il commento
     * @param $content Il contenuto del commento
     *
     * @return array[] dove posso scegliere fra tre colonne: "state" che è true se è andata a buon fine o false se non è stato così, "id" l'id che è stato associato al commento, "date" la data di pubblicazione del commento
     * @return false Se uno dei parametri è nullo
     * @return string "internalError" Se viene sollevata una PDOException
     */
    function postComment($conn, String $user, int $noteId, String $content) {
      if ($user == NULL || $content == NULL || $conn == "null" || $conn == NULL || $noteId == NULL) {
        return false;
      }
      try {
        $query = $conn->prepare("INSERT INTO revw (user, note, review, date) VALUES (:user, :noteId, :review, NOW())");
        $query->bindParam(":user", $user);
        $query->bindParam(":noteId", $noteId);
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

    /**
     * La funzione serve ad eliminare un commento dato il suo id
     *
     * @param $conn La connesione che stiamo usando
     * @param $id L'id della nota che vogliamo cancellare
     *
     * @return false se l'id non è valido
     * @return string "internalError" Se viene sollevata una PDOException
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

    /**
     * Dice se un utente è il creatore della nota dati i parametri
     *
     * @param $conn La connessione che stiamo usando
     * @param $noteId L'id della nota di cui dobbiamo verificare il proprietario
     * @param $user L'utente che deve corrispondere al possessore della note per esserne il creatore
     *
     * @return false Se il titolo o il nome dell'utente della nota non è valido o se non è lui il possessore
     * @return true Se $user è il creatore della nota $title
     * @return string "itnernalError" Se viene sollevata una PDOException
     */
    function isNoteOwner($conn, $noteId, $user) {
      if ($conn == "null" || $conn == NULL || $noteId == NULL || $user == NULL) {
        return false;
      }
      try {
        $query = $conn->prepare("SELECT user FROM note WHERE id = :id");
        $query->bindParam(":id", $noteId);
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

    /**
     * La funzione viene usata per aggiornare i parametri inseriti nella nota
     *
     * @param $conn La connessione che stiamo usando
     * @param $user Il nome dell'utente che ha creato la nota
     * @param $noteId L'id della nota di cui modificare il titolo
     * @param $newTitle Il nuovo titolo della nota da aggiornare
     * @param $newContent Il nuovo contenuto della nota se vogliamo aggiorarlo
     *
     * @return true Se la nota viene aggiornata con successo
     * @return false Se per qualche ragione non viene aperto il file o se viene sollevata una PDOException
     */
    function updateNote($conn, $user, $noteId, $newTitle, $newContent) {
      $newTitle = str_replace(" ", "_", $newTitle);
      try {
        $query = $conn->prepare("SELECT dir FROM note WHERE id = :noteId");
        $query->bindParam(":noteId", $noteId);
        $query->execute();
        $query->setFetchMode(PDO::FETCH_ASSOC);
        $dir = $query->fetchAll();
        $dir = $dir[0]["dir"];
        if ($content = fopen("..$dir", "w+")) {
          //se usiamo r+ non possiamo eliminare caratteri, con w+ il file viene distrutto e riscritto.
          fwrite($content, $newContent);
          fclose($content);
          $query = $conn->prepare("UPDATE note SET title = :newTtl WHERE id = :noteId");
          $query->bindParam(":newTtl", $newTitle);
          $query->bindParam(":noteId", $noteId);
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

    /**
     * La funzione inserisce il rating di una nota se l'utente non l'aveva già inserito o se il rating che vuole inserire ora è diverso da quelle che aveva inserito in passato
     *
     * @param $username Lo username che vuole inserire la nota
     * @param $noteId L'id della nota della quale si vuole inserire il rating
     * @param $rating Il rating che si vuole isnerire (true per Mi piace e false per Non mi piace)
     *
     * @return true Se il rating viene aggiunto o aggiornato senza problemi
     * @return string "internalError" Se è stata sollevta una PDOexception
     * @return false Se il rating che si vuole inserire era già presente
     */
    function rateNote(String $username, String $noteId, bool $rating) {
      if ($rating) {
        $rating = 1;
      } else {
        $rating = 0;
      }
      try {
        if(alreadyRated($username, $noteId) == 0) {
           $conn = connectDb();
           $query = $conn->prepare("INSERT INTO rate (user, note, rate, date)  VALUES (:username, :noteId, :rating, NOW())");
           $query->bindParam(":username", $username);
           $query->bindParam(":noteId", $noteId);
           $query->bindParam(":rating", $rating);
           $query->execute();
           return true;
         } elseif((alreadyRated($username, $noteId) == 1) && (getRate($username, $noteId) != -1) && (getRate($username, $noteId) != $rating)) {
           $conn = connectDb();
           $query = $conn->prepare("UPDATE rate SET rate = :rating  WHERE (user = :username) AND (note = :noteId)");
           $query->bindParam(":rating", $rating);
           $query->bindParam(":username", $username);
           $query->bindParam(":noteId", $noteId);
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

    /**
     * La funzione serve a verificare se l'utente ha già inserito un rating per la nota, ritorna il numero di rating già inseriti (dovrebbe essere fra 1 e 0)
     *
     * @param $username Lo username dell'utente di cui si vuole controllare il rate
     * @param $noteId L'id della nota sulla quale si cerca il possibile rate dell'utente
     *
     * @return int Il numero di rate messi dall'utente alla nota $noteId (dovrebbe essere fra 0 e 1)
     * @return int -1 Se è stata sollevata una eccezzione PDOException
     */
    function alreadyRated(String $username, String $noteId) {
      try {
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (user = :username) AND (note = :id)");
        $query->bindParam(":username", $username);
        $query->bindParam(":id", $noteId);
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

    /**
     * Restituisce il rate dato a una nota da un utente se c'è il rate, altrimenti restituisce -1
     *
     * @param $username Lo username del quale si vuole fare la ricerca
     * @param $noteId L'id della nota sulla quale si deve cercare il rate
     *
     * @return int -1 Se non c'è alcun rate su quella nota da parte dell'utente o se è stata sollevata una PDOException
     * @return int 1 Se il rate è TRUE
     * @return int 0 Se il rate è FALSE
     */
    function getRate(String $username, String $noteId){
      try {
        if (alreadyRated($username, $noteId) != 1) {
          return -1;
        } else {
          $conn = connectDb();
          $query = $conn->prepare("SELECT rate FROM rate WHERE (user = :username) AND (note = :noteId)");
          $query->bindParam(":username", $username);
          $query->bindParam(":noteId", $noteId);
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

    /**
     * La funzione ritorna il numero di rate positivi sulla nota $noteId
     *
     * @param $noteId L'id della nota nella quale cercare i rate
     *
     * @return int Il numero di rate positivi
     * @return false In caso di errore se viene sollevata una PDOException
     */
    function getLikes($noteId){
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (note = :note) AND (rate = 1)");
        $query->bindParam(":note", $noteId);
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

    /**
     * La funzione ritorna il numero di rate negativi sulla nota $noteId
     *
     * @param $noteId L'id della nota nella quale cercare i rate
     *
     * @return int Il numero di rate negativi
     * @return false In caso di errore se viene sollevata una PDOException
     */
    function getDislikes($noteId){
      try{
        $conn = connectDb();
        $query = $conn->prepare("SELECT COUNT(*) as num FROM rate WHERE (note = :note) AND (rate = 0)");
        $query->bindParam(":note", $noteId);
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

    /**
     * La funzione ritorna il numero totale dei rate lasciati dall'utente $user sotto tutte le note del DB
     *
     * @param $user Il nome dell'utente del quale cercare il numero dei rate
     *
     * @return int Il numero di rate lasciati dall'utente (non i rate che gli altri lasciano sotto le sue note ma quelli che lui lascia in tutte le note del database)
     * @return false In caso di errore se viene sollevata una PDOException
     * @return string "Utente non esistente" Returniamo questa stringa perché se stampiamo direttamente quello che la funzione ci restituisce non dobbiamo usare qualche switch o altra roba per displayare gli errori
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

    /**
     * La funziona ritorna la data e l'ora dell'ultimo login eseguito con successo dall'utente
     *
     * @param $user Il nome dell'utente di cui stiamo cercando l'ultimo login
     *
     * @return datetime La data e l'ora dell'ultimo log secondo il formato in cui esso è salvato dentro il database
     * @return false Se viene sollevata una PDOException di qualche tipo
     * @return string "Utente non esistente" Nel caso in cui l'utente non sia presente nel database
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

    /**
     * La funzione ritorna il numero totale dei commenti lasciati dall'utente $user sotto tutte le note del DB
     *
     * @param $user Il nome dell'utente del quale cercare il numero demçi commenti
     *
     * @return int Il numero di commenti lasciati dall'utente (non i commenti che gli altri lasciano sotto le sue note ma quelli che lui lascia in tutte le note del database)
     * @return false In caso di errore se viene sollevata una PDOException
     * @return string "Utente non esistente" Nel caso in cui l'utente non sia presente in database
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

    /**
     * La funzione ritorna il numero totale delle note create dall'utente $user
     *
     * @param $user Il nome dell'utente del quale contare il nnumero di note
     *
     * @return int Il numero di note create dall'utente e ancora presenti nel database
     * @return false In caso di errore se viene sollevata una PDOException
     * @return string "Utente non esistente" Nel caso in cui l'utente non sia presente in database-
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

    /**
     * La funzione ritorna il numero totale dei like e dislike ricevuto dall'utente
     *
     * @param $user Il nome dell'utente del quale contare il nnumero di rates
     *
     * @return int Il numero totale di rate ricevuti
     * @return false In caso di errore se viene sollevata una PDOException
     * @return string "Utente non esistente" Nel caso in cui l'utente non sia presente in database
     */
    function getReceivedRate(String $user){
      if (mysqlChckUsr($user)) {
        try{
          $tot_rates = 0;
          $list = searchNote(connectDb(), NULL, NULL, NULL, $user, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
          foreach($list as $element){
            $tot_rates += (getLikes($element["id"]) + getDislikes($element["id"]));
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

  /**
   * La funzione inserisce un'immagine nel DB verificando che il formato fornito sia tra quelli ammessi e verificando che esista la nota (non verifica l'autorizzazione o meno dell'utente)
   *
   * @param $noteId L'id della nota sulla quale aggiungere un immagine
   * @param $format Il formato dell'immagine
   * @param $dir La directory del server dentro cui abbiamo messo l'immagine
   * @param $picName Il nome dell'immagine
   *
   * @return string "done" Se tutto è andato bene e se non sono state sollevate eccezioni
   * @return string "internalError" Se è stata sollevata un'eccezione PDOException (array associativo in cui la stringa è sotto il campo "status")
   * @return string "invalidFormat" Se il formato non è tra quelli concessi (scritti nell'array $formats) (array associativo in cui la stringa è sotto il campo "status")
   * @return string "non_existentNote" Se la nota sulla quale si sta cercando di inserire l'immagine non esiste (array associativo in cui la stringa è sotto il campo "status")
   */
  function newImageEntry($noteId, $format, $picName, $user) {
    if (checkNote(connectDb(), $noteId)) {
      $formats = ["png", "gif", "jpg", "jpeg"];
      if (in_array($format, $formats)) {
        $picName = str_replace("'", "sc-a", $picName);
        $picName = str_replace('"', "sc-q", $picName);
        $imageExtension = strtolower(pathinfo($picName, PATHINFO_EXTENSION));
        try {
          $date = date("Y-m-d H:i:s");
          $conn = connectDb();
          $query = $conn->prepare("INSERT INTO pict (date, note, format, name) VALUES (:date, :note, :format, :pic_name)");
          $query->bindParam(":note", $noteId);
          $query->bindParam(":format", $format);
          $query->bindParam(":pic_name", $picName);
          $query->bindParam(":date", $date);
          $query->execute();
          $getPicId = connectDb()->prepare("SELECT id FROM pict WHERE date = :date");
          $getPicId->bindParam(":date", $date);
          $getPicId->execute();
          $id = $getPicId->fetchAll()[0]["id"];
          $imageDir = "../notedb/$user/uploads/$id." . $imageExtension;
          $query = $conn->prepare("UPDATE pict SET dir = :dir WHERE id = :id");
          $query->bindParam(":id", $id);
          $query->bindParam(":dir", $imageDir);
          $query->execute();
          return ["status"=>"done", "id"=>$id];
        } catch(PDOException $e){
          PDOError($e);
          return ["status"=>"internalError"];
        } finally {
          $conn = null;
        }
      } else {
        return ["status"=>"invalidFormat"];
      }
    } else {
      return ["status"=>"non-existentNote"];
    }
  }

  /**
   * La funzione ritorna in una matrice la dir e l'id di tutte le foto di una nota, non ritorna nulla se non è stata definita alcuna nota
   *
   * @param $noteId L'id di cui cercare i dati delle immagini
   *
   * @return array[x]["dir"/"id"] una matrice dove x è il numero dell'immagine (in base al sorting sql) e come seconda dimensione si può scegliere "dir" per la directory oppure "id" per l'id
   * @return string "internalError" Se è stata sollevata una PDOException
   */
  function getPicsPathsAndIds($noteId) {
    if ($noteId == NULL) {
      return;
    }
    try {
      $query = connectDb()->prepare("SELECT dir,id FROM pict WHERE note = :id");
      $query->bindParam(":id", $noteId);
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

  /**
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

  /**
   * La funzione rimuove un immagine dal DB e anche dalla sua directory nel server e prima di farlo verifica che l'immagine esista e che l'utente che ne chiede la rimozione sia autorizzato
   *
   * @param $conn La connessione che stiamo usando
   * @param $noteId L'id della nota a cui deve appartenere l'immagine
   * @param $id L'id dell'immagine da cancellare
   * @param $user L'utente che chiede la cancellazione della foto (per essere autorizzato deve essere admin o il creatore della nota)
   *
   * @return string "imgNotFound" Nel caso in cui l'immagine con $id dentro al $noteId non sia stata trovata
   * @return string "notAuthorized" Nel caso in cui l'utente provi a cancellare un'immagine senza autorizzazione (no admin e no creatore nota)
   * @return string "illegalDeletion" Se l'utente non è admin o creatore della nota e se non esiste immagine $id relativa a $noteId
   * @return string "illegalError" Se viene sollevata una PDOException, quindi errore tra la comunicazione con db (spesso errori nelle query)
   * @return string "done" Se tutto va come deve e non vengono sollevati errori
   */
  function removeImage($conn, $noteId, $id, $user) {
    $dir = null;
    $authorized = false;
    $picOnDb = false;
    if (getAcclvl($_SESSION["username"]) === "1") {
      $authorized = true;
    }
    try{
      if(isNoteOwner(connectDb(), $noteId, $user)){
        $authorized = true;
      }
      $findPic = $conn->prepare("SELECT COUNT(*) AS val FROM pict INNER JOIN note ON pict.note = note.id WHERE pict.id =:id AND note.id = :note");
      $findPic->bindParam(":id", $id);
      $findPic->bindParam(":note", $noteId);
      $findPic->execute();
      $result = $findPic->fetchAll();
      if($result[0]["val"] == 1){
        $picOnDb = true;
      }
      if(($authorized == true) && ($picOnDb == true)){
        $getDir = $conn->prepare("SELECT pict.dir FROM pict INNER JOIN note ON note.id = pict.note WHERE pict.id = :id");
        $getDir->bindParam(":id", $id);
        $getDir->execute();
        $result = $getDir->fetchAll();
        $dir = $result[0]["dir"];
        $removeImg = $conn->prepare("DELETE FROM pict WHERE id = :id");
        $removeImg->bindParam(":id", $id);
        $removeImg->execute();
        exec("rm " . $dir);
        return "done";
      }elseif(($authorized == true) && ($picOnDb == false)){
        return "imgNotFound";
      }elseif(($authorized == false) && ($picOnDb == true)){
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
  }


  /**
   * @deprecated La funzione da quando non usiamo più il titolo nell'url non viene più usata
   * La funzione ritorna il path leggibile dal browser con la parte del nome dell'immagine codificata secondo la funzione encode()
   *
   * @param $conn La connessione che stiamo usando
   * @param $noteId La nota da cui prendiamo la foto di cui codificare il path
   * @param $pic_id L'id della foto dentro il DB (identificazione univoca)
   *
   * @return string "imgNotExisting" Se non esiste immagine con quell'id relativa alla nota
   * @return string "internalError" Se viene sollevata una PDOException
   * @return string $preImgName.encode($imgName) il path fino al nome dell'immagine in clear e il nome immagine encoded da encode()
   */
   function urlCodec($conn, $noteId, $pic_id) : String {
    try{
      $getPath = $conn->prepare("SELECT dir FROM pict WHERE note = :note AND id = :id");
      $getPath->bindParam(":note", $noteId);
      $getPath->bindParam(":id", $id);
      $getPath->execute();
      $result = $getPath->fetchAll();
      if(empty($result)){
        return "imgNotExisting";
      }else{
        $result = $result[0]["dir"];
      }
    }catch(PDOException $e){
      PDOErrors($e);
      return "internalError";
    }

  $imgName = substr($stringa, 1+strpos($stringa, "/"));
  $preImgName = substr($stringa, 0, 1+strpos($stringa, "/"));



  return $preImgName.urlencode($imgName);

  }

  /**
  * La funzione restituisce l'Id di una nota dato il suo titolo
  *
  * @param $conn La connessione che stiamo usando
  * @param $title Il titolo della nota passato allo stesso modo in cui è scritto nel DB
  *
  * @return int noteId L'ID della nota nel DB
  * @return int -1 Nel caso in cui la query non ritorna nulla
  * @return string "internalError" Se viene sollevata una PDOException
  */
  function getNoteId($conn, $title, $user, $date){
    $title = str_replace(" ", "_", $title);
    $title = str_replace("'", "sc-a", $title);
    $title = str_replace('"', "sc-q", $title);
    try{
      $getId = $conn->prepare("SELECT id FROM note WHERE title = :title AND user LIKE :user AND date LIKE :date");
      $getId->bindParam(":title", $title);
      $getId->bindParam(":user", $user);
      $getId->bindParam(":date", $date);
      $getId->execute();
      $id = $getId->fetchAll();
      if(!empty($id[0]["id"])){
        return $id[0]["id"];
      }else{
        return -1;
      }
    } catch(PDOException $e){
      PDOErrors($e);
      return "internalError";
    }
  }

?>
