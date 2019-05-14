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
    <script src="main/main.js"></script>
	  <script src="main/search.js"></script>
    <link rel="stylesheet" type="text/css" href="main/stylesheets/datePicker.css" />
    <link rel="stylesheet" type="text/css" href="main/stylesheets/positions.css" />
    <link rel="stylesheet/less" type="text/css" href="main/stylesheets/main.less" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ></script>
  </head>
  <body id="Body">
  <div class="overlay" id="SearchDiv">
    <div class='search' id="Search">
      <input id="search" type="text" class="search_text"/>
      <span class="search_checkbox top20 left40 a" hidden>
        <input type="checkbox" class="a" id="deptNum" >Indirizzo per numero</input>
      </span>
      <span class="search_checkbox top30 left40" hidden>
        <input type="checkbox" id="deptName">Indirizzo per nome</input>
      </span>
      <span class="search_checkbox top40 left40 a" hidden>
        <input type="checkbox" class="a" id="subjNum">Materia per numero</input>
      </span>
      <span class="search_checkbox top50 left40" hidden>
        <input type="checkbox" id="subjName">Materia per nome</input>
      </span>
      <span class="search_checkbox top60 left40" hidden>
        <input type="checkbox" id="noteTtl">Appunti per titolo</input>
      </span>
      <span class="top15 left35 filtro">
        Filtra per utente: <input id="filtroUtente" placeholder="Nome utente..." /><br/>
	      Filtro per materia: <input id="filtroMateria" list="materie" placeholder="Materia..."/><br/>
	      <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = subj(connectDb(), NULL, NULL);
          echo "<datalist id='materie'>";
          foreach ($r[1] as $res) {
            echo "<option value='" . $res . "'>";
          }
          echo "</datalist>";
         ?>
        Filtra per indirizzo: <input id="filtroIndirizzo" list="Indirizzi" placeholder="Indirizzo..." /><br/>
        <?php
          require_once 'php/core.php';
          require_once 'php/query_funs.php';
          $r = dept(connectDb(), NULL, NULL);
          echo "<datalist id='Indirizzi'>";
          foreach ($r[1] as $res) {
            echo "<option value='" . $res . "'>";
          }
          echo "</datalist>";
         ?>
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
        <button onclick="getDepts();">Indirizzi</button><br/>
        <button onclick="getSubjs();">Materie</button><br/>
        <button onclick="getNotes();">Appunti</button>
      </span>
      <button onclick="cerca()" class="search_button top95 left48">Cerca</button>
    </div>
  </div>
  <div id="warn" class="warn" style="display:none">
  </div>
  <p style="position:sticky;top:0px;width:100%">
    <div class="navbar" id="navbar">
      <a href="" class="navbar-left">HOME</a>
	    <button onclick="openSearch()" class="buttonIcon navbar-left">A</button>
      <a href="login/" class="navbar-right log">LOGIN</a>
      <a href="register/" class="navbar-right log">REGISTER</a>
      <a id="logout" onclick="logout()"  class="navbar-right logout">LOGOUT</a>
    </div>
      <span id="greet"></span>
    </p>
    <div id="everythingAboutNote">
      <div id="risultati">
      </div>
      <div class="scriviNota" style="display: none;">
        <textarea rows="1" cols="100" id="writeNoteTitle"></textarea>
        Materia: <input id="writeNoteSubj" list="materie" />
        Indizrizzo: <input id="writeNoteDept" list="Indirizzi" /><br />
        <textarea rows="30" cols="100" id="writeNoteContent"></textarea>
        <button id="submitNote" onclick="submitNote()">Pubblica</button>
      </div>
    </div>
  <div class="navbar adminTools" style="position:absolute;bottom:5px;padding:10px 15px 10px 15px;display:none">
    <a onclick="man('on')" class="navbar-left">Avvia manutenzione</a>
    <a onclick="man('off')" class="navbar-left">Termina manutenzione</a>
    <a onclick="deleteNote()" class="navbar-right">Rimuovi nota</a>
  </div>
  <button id="scriviNotaBtn" class="noteButton" onclick="mostraSpazioNote();" style="display: none;">Scrivi una nota</button>
  <div class="delNote" style="display: none;">
    <input id="delNoteTtl" class="textInput"/>
    <button id="delNoteConfirm" onclick="delNote()" class="delNoteButton" style="position:absolute;left:330px;">Conferma</button>
  </div>
  </body>
</html>
<?php
  require_once "php/funs.php";
  $s = getManStatus();
  if($s == "true") {
    echo "<script>error('man');</script>";
  } elseif ($s == "false") {

  } else {
    $s = "'" . $s . "'";
    echo "<script>error($s);</script>";
  }
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true); $('#scriviNotaBtn').show();</script>";
      if(getAcclvl($_SESSION["username"]) == 1) {
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
