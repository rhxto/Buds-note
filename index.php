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
      <span class="search_checkbox top20 left40">
        <input type="checkbox" id="deptNum" >Indirizzo per numero</input>
      </span>
      <span class="search_checkbox top30 left40">
        <input type="checkbox" id="deptName">Indirizzo per nome</input>
      </span>
      <span class="search_checkbox top40 left40">
        <input type="checkbox" id="subjNum">Materia per numero</input>
      </span>
      <span class="search_checkbox top50 left40">
        <input type="checkbox" id="subjName">Materia per nome</input>
      </span>
      <span class="search_checkbox top60 left40">
        <input type="checkbox" id="noteTtl">Appunti per titolo</input>
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
  </body>
</html>
<?php
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true);</script>";
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
