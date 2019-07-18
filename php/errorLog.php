<html>
  <head>
    <title>admin/Error log</title>
    <style>
      body {
        font-size: 17px;
        background-color: #4e5759;
        color: white;
      }
      a {
        position: fixed;
        bottom: 0%;
        right: 0%;
        background-color: black;
        color: white;
        font-size: 30px;
        border: 2px solid black;
        border-radius: 20%;
        padding: 3px;
      }
    </style>
  </head>
  <body>
    <a href = "../../">Home</a>
    <?php session_start();
      /* **presa da funs.php, la mettiamo qui perché se no un errore su funs blocca anche questa pagina rendendola inutile
       * La funzione ritorna il livello di accesso di un utente
       *
       * @param $user Lo username dell'utente del quale si vuole sapere l'acc_lvl
       *
       * @return Il numero corrispondente all'acc_lvl
      */
      require_once 'core.php';
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
      if (isset($_SESSION['logged_in'])) {
        if ($_SESSION['logged_in'] == '1') {
          if(getAcclvl($_SESSION["username"]) == 1) {
            $f = array_reverse(file("/var/log/apache2/error.log"));
            foreach ($f as $line) {
              echo $line;
              echo "<br/>";
            }
          } else {
            error_log("**Visita a errorLog.php non autorizzata 1**: Logged_in: " . $_SESSION["logged_in"] . " Username: " . $_SESSION['username'] . " acc_lvl: " . getAcclvl($_SESSION["username"]) . " Ip: " . $_SERVER["REMOTE_ADDR"]);
            die("<h1>Non sei autorizzato a visitare questa pagina, questo incidente é stato registrato.<h1>");
          }
        } else {
          error_log("**Visita a errorLog.php non autorizzata 2**: Logged_in: " . $_SESSION["logged_in"] . " Username: NULL (Il login non é stato eseguito)" . " Ip: " . $_SERVER["REMOTE_ADDR"]);
          die("<h1>Non sei autorizzato a visitare questa pagina, questo incidente é stato registrato.<h1>");
        }
      } else {
        error_log("**Visita a errorLog.php non autorizzata 3**: Logged_in: NULL non é stato loggato Username: NULL non é stato loggato Ip: " . $_SERVER["REMOTE_ADDR"]);
        die("<h1>Non sei autorizzato a visitare questa pagina, questo incidente é stato registrato.<h1>");
      }
     ?>
  </body>
</html>
