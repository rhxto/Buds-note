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
    <script src="main/main.js"></script>
	  <script src="main/search.js"></script>
    <link rel="stylesheet" type="text/css" href="main/stylesheets/positions.css" />
    <link rel="stylesheet" type="text/css" href="main/stylesheets/main.css" />
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
	      Filtro per materia: <input id="filtroMateria" list="materie" placeholder="Materia..."/><br/>
	      <datalist id="materie">
	        <option value="Italiano">
	        <option value="Matematica">
	        <option value="Inglese">
	        <option value="Scienze">
	        <option value="Informatica">
    	    <option value="Latino">
    	    <option value="Religione">
    	    <option value="Storia">
     	    <option value="Geografia">
    	    <option value="Ed. fisica">
    	    <option value="Storia dell'arte">
    	    <option value="Disegno tecnico">
    	    <option value="Fisica">
    	    <option value="Filosofia">
	      </datalist>
        Filtra per indirizzo: <input id="filtroIndirizzo" list="Indirizzi" placeholder="Indirizzo..." /><br/>
        <datalist id="Indirizzi">
          <option value="Liceo scientifico">
          <option value="Liceo scientifico opz. scienze applicate">
          <option value="Liceo linguistico">
          <option value="Liceo scienze umane">
          <option value="Liceo classico">
        </datalist>
        Filtra per utente: <input id="filtroUtente" placeholder="Nome utente..." />
      </span>
      <span>
        <button onclick="getDepts();">Indirizzi</button><br/>
        <button onclick="getSubjs();">Materie</button><br/>
        <button onclick="getNotes();">Appunti</button>
      </span>
      <button onclick="cerca()" class="search_button top75 left48">Cerca</button>
    </div>
  </div>
  <div id="warn" class="warn" style="display:none">
  </div>
  <p style="position:sticky;top:0px;width:100%">
    <div class="navbar" id="navbar">
      <a href="" class="navbar-left">HOME</a>
	    <button onclick="openSearch()" class="navbar-left"> A</button>
      <a href="login/" class="navbar-right log">LOGIN</a>
      <a href="register/" class="navbar-right log">REGISTER</a>
      <a id="logout" onclick="logout()"  class="navbar-right logout">LOGOUT</a>
    </div>
  </p>
  <div id="risultati">
  </div>
  <div class="adminTools">
    <button onclick="man('on')">Avvia manutenzione</button>
    <button onclick="man('off')">Termina manutenzione</button>
  </div>
  </body>
</html>
<?php
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true);</script>";
      /*require_once "php/funs.php";
      if(getAcclvl($_SESSION["username"]) == 1) {
        echo "<script>$('.a').show();</script>";
      } per ora possiamo tenere tutti i tipi di richerche, che fastidio danno?*/
      echo "<greet>Benvenuto, " . $_SESSION["username"] . "</greet>";
    } else {
      session_unset();  //quando si esegue il logout logged_in é settato e != da 1 quindi sappiamo che é stato eseguito il logout
      session_destroy();
      echo "<script>$('.logout').attr('hidden', true);</script>";
    }
  } else {
    echo "<script>$('.logout').attr('hidden', true);</script>";
    //se chiudiamo la sessione anche quando uno non é loggato, non riusciamo a settare logged_in a 1
  }
  require_once "php/funs.php";
  $s = getManStatus();
  if($s == "true") {
    echo "<script>error('man')</script>";
  } elseif ($s == "false") {

  } else {
    $s = "'" . $s . "'";
    echo "<script>error($s);</script>";
  }
 ?>
