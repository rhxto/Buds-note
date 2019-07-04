<?php session_start();
  header("Expires: 0");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Buds_note</title>
    <script src="jquery/jquery.min.js"></script>
    <script type="text/javascript" src="jquery/jquery-ui/jquery-ui.js"></script>
    <link rel="stylesheet/less" type="text/css" href="main/stylesheets/main.less" />
	   <script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ></script>
    <script src="main/main.js"></script>
	  <script src="main/search.js"></script>
    <link rel="stylesheet" type="text/css" href="main/stylesheets/datePicker.css" />
    <link rel="stylesheet" type="text/css" href="main/stylesheets/positions.css" />
  </head>
  <body id="Body">
  <div class="overlay" id="SearchDiv">
    <div><br/><br/><br/></div>
    <div class='search' id="Search">
      <a onclick="hideSearch()"class="icon absolute left80" style="z-index:3;color:whitesmoke">D</a>
      <input id="search" type="text" class="search_text"/>
      <span class="search_checkbox top25 left40 a" hidden>
        <input type="checkbox" class="a" id="deptNum" >Indirizzo per numero</input>
      </span>
      <span class="search_checkbox top35 left40" hidden>
        <input type="checkbox" id="deptName">Indirizzo per nome</input>
      </span>
      <span class="search_checkbox top45 left40 a" hidden>
        <input type="checkbox" class="a" id="subjNum">Materia per numero</input>
      </span>
      <span class="search_checkbox top55 left40" hidden>
        <input type="checkbox" id="subjName">Materia per nome</input>
      </span>
      <span class="search_checkbox top65 left40" hidden>
        <input type="checkbox" id="noteTtl">Appunti per titolo</input>
      </span>
      <span class="top20 left35 filtro">
        Filtra per utente: <input id="filtroUtente" style="color:black" placeholder="Nome utente..." /><br/>
        Filtra per materia: <select class="opzM" id="filtroMateria">
          <option class="opz" value="Tutto">Tutto</option>
        <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = subj(connectDb(), NULL, NULL);
          foreach ($r[1] as $res) {
            if (strpos($res, "'") !== false) {
              $res = str_replace("'", "&#39", $res);
            }
            echo "<option class='opz' value='" . $res . "'>$res</option>";
          }
         ?>
       </select><br/>
        Filtra per indirizzo: <select class='opzM' id="filtroIndirizzo"><br/>
          <option class="opz" value="Tutto">Tutto</option>
        <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = dept(connectDb(), NULL, NULL);
          foreach ($r[1] as $res) {
            echo "<option class='opz' value='" . $res . "'>$res</option>";
          }
         ?>
       </select><br/>
        Filtra per anno: <input type="number" id="filtroAnno"/><br/>
        Data d'inizio: <input id="filtroDatefrom" /><br/>
        Data di fine:  <input id="filtroDateto"/><br/>
        <script type="text/javascript">
          $('#filtroDatefrom').datepicker({
              constrainInput: true,   // prevent letters in the input field
              dateFormat: 'yy-mm-dd',  // Date Format used
              firstDay: 1 // Start with Monday
          });
          $('#filtroDateto').datepicker({
              constrainInput: true,   // prevent letters in the input field
              dateFormat: 'yy-mm-dd',  // Date Format used
              firstDay: 1 // Start with Monday
          });
        </script>
        Ordina per: <input list="tipoOrdine" id="filtroOrderBy"/><br/>
        <datalist id="tipoOrdine">
          <option value="Titolo">
          <option value="Username">
          <option value="Materia">
          <option value="Anno">
          <option value="Indirizzo">
          <option value="Insegnante">
          <option value="Data">
        </datalist>
        Ordine: <input list="ordini" id="filtroOrdine"/>
        <datalist id="ordini">
          <option value="ascendente">
          <option value="discendente">
        </datalist>
      </span>
      <span>
        <button class="search_button top25 left15" onclick="getDepts();">Indirizzi</button><br/>
        <button class="search_button top45 left15" onclick="getSubjs();">Materie</button><br/>
        <button class="search_button top65 left15" onclick="getNotes();">Appunti</button>
      </span>
      <button onclick="cerca()" class="search_button top110 left48">Cerca</button>
    </div>
  </div>
  <div id="warn" class="warn" style="display:none">
  </div>
    <div class="navbar" id="navbar">
      <a href="" class="navbar-left">HOME</a>
	    <a id="SearchLens" onclick="openSearch()" class="navbar-left buttonIcon">A</a>
      <a id="SearchLensMoz" onclick="openSearch()" class="navbar-left searchLensMoz" style="padding:5px 0px 0px 0px">
        <img src="main/searchlens.png">
      </a>
      <a href="login/" class="navbar-right log">LOGIN</a>
      <a href="register/" class="navbar-right log">REGISTER</a>
      <a id="logout" onclick="logout()"  class="navbar-right logout">LOGOUT</a>
    </div>
      <span id="greet"></span>
    <div id="everythingAboutNote">
      <div id="risultati" class="risultati">
      </div>
      <div class="scriviNota" style="display:none;padding:5px 0px 5px 0px;">
        Titolo: <textarea rows="1" cols="100" id="writeNoteTitle" class="scriviNotaText"></textarea><br/>
        Materia: <select class="opzM" id="writeNoteSubj"><br/>
        <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = subj(connectDb(), NULL, NULL);
          foreach ($r[1] as $res) {
            if (strpos($res, "'") !== false) {
              $res = str_replace("'", "&#39", $res);
            }
            echo "<option class='opz' value='" . $res . "'>$res</option>";
          }
         ?>
       </select>
        Indirizzo: <select class='opzM' id="writeNoteDept"><br/><br/>
        <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = dept(connectDb(), NULL, NULL);
          foreach ($r[1] as $res) {
            echo "<option class='opz' value='" . $res . "'>$res</option>";
          }
         ?>
       </select><br/>
        <textarea rows="25" cols="100" id="writeNoteContent" class="scriviNotaTextArea"></textarea><br/>
        <button id="submitNote" class="btn" onclick="submitNote()">Pubblica</button>
      </div>
    </div>
  <div class="navbar adminTools" style="position:fixed;bottom:5px;display:none">
    <a onclick="man('on')" class="navbar-left">Avvia manutenzione</a>
    <a onclick="man('off')" class="navbar-left">Termina manutenzione</a>
    <a href="php/errorLog.php" class="navbar-left">Log errori</a>
  </div>
  <button id="scriviNotaBtn" class="noteButton" onclick="mostraSpazioNote();" style="display: none;">Scrivi una nota</button>
  <div class="delNote" style="display: none;">
    <input id="delNoteTtl" class="textInput"/>
    <button id="delNoteConfirm" onclick="delNote()" class="delNoteButton" style="position:absolute;left:330px;">Conferma</button>
  </div>
  <div><br/><br/><br/></div>
  </body>
  <script>
  var FIREFOX = navigator.userAgent.toLowerCase().indexOf('firefox') > -1;

  if (FIREFOX) {
    console.log("firefox");
    $("#SearchLens").hide();
    $("#SearchLensMoz").show();
  } else  {
    $("#SearchLens").show();
    $("#SearchLensMoz").hide();
  }
   if(navigator.userAgent.toLowerCase().indexOf('firefox') > -1){
     error("FIREFOX");
   }

  </script>
</html>
<?php
  require_once "php/funs.php";
  if(gettype($m = getManStatus()) === "string") {
    echo "<script>error($m);</script>";
  } elseif ($m == true) {
    echo "<script>error('man');</script>";
  }
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] === '1') {
      echo "<script>$('.log').attr('hidden', true); $('#scriviNotaBtn').show();</script>";
      if(getAcclvl($_SESSION["username"]) === "1") {
        echo "<script>$('.adminTools').show();</script>";
      }
      echo "<script>$('#greet').html('Benvenuto,  " . $_SESSION['username'] . "');</script>";
    } else {
      session_unset();  //quando si esegue il logout logged_in é settato e != da 1 quindi sappiamo che é stato eseguito il logout
      session_destroy();
      echo "<script>$('.logout').attr('hidden', true);</script>";
    }
  } else {
    echo "<script>$('.logout').attr('hidden', true);</script>";
    //se chiudiamo la sessione anche quando uno non é loggato, non riusciamo a settare logged_in a 1
  }
 ?>
