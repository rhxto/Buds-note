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
    <meta charset="utf-8"/>
    <title>Buds-note</title>
    <script src="../jquery/jquery.min.js"></script>
    <script type="text/javascript" src="../main/viewNote.js"></script>
    <link rel="stylesheet/less"  type="text/css" href="../main/stylesheets/main.less"></link>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/less.js/3.9.0/less.min.js" ></script>
  </head>
  <body>
    <p style="position:sticky;top:0px;width:100%">
      <div class="navbar" id="navbar">
        <a href="../" class="navbar-left">HOME</a>
        <a href="../login/" class="navbar-right log">LOGIN</a>
        <a href="../register/" class="navbar-right log">REGISTER</a>
        <a id="logout" onclick="logout()"  class="navbar-right logout">LOGOUT</a>
      </div>
      <span id="greet"></span>
    </p>
    <div id="everythingAboutNote">
      <div class="noteInfoDisplay" hidden>
        <span class="noteHeaderTtl">
          <h2>Titolo</h2>
        </span>
        <span class="noteHeaderUser">User</span>
        <span class="noteHeaderDept">Indirizzo</span>
        <span class="noteHeaderSubj">Materia</span>
        <span class="noteHeaderYear">Anno</span>
        <span class="noteHeaderDate">Data</span>
        <span class="noteContent">Contenuto</span>
      </div>
      <div class="comments">
        <span>Commenti</span><br/>
        <div class="localSpawn"></div>
        <?php
          require_once 'core.php';
          require_once 'query_funs.php';
          $comments = searchRevw(connectDb(), NULL, NULL, $_GET["title"], NULL, NULL, NULL, NULL, NULL);
          foreach ($comments as $comment) {
            echo "<script>$('.comments').append('<span id=" . $comment["id"] . ">" . $comment['review'] . "<button class=delCommentBtn onclick=delComment(" . $comment["id"] . ");>Elimina commento</button></span><br/>');</script>";
          }
          echo "<script>$('.comments').append('<br/>');</script>";
         ?>
        <textarea rows="1" cols="100" placeholder="Inserisci un commento..." id="commentText"></textarea>
        <button onclick="postComment()">Pubblica</button>
      </div>
      <button id="modifyNoteBtn" onclick="showNoteEditor()" style="display: none;">Modifica nota</button>
      <button id="modifyNoteConfirm" onclick="modifyNote()" style="display: none;">Salva</button>
    </div>
    <div id="warn" class="warn" style="display:none">
    </div>
    <div class="navbar adminTools" style="position:absolute;bottom:5px;padding:10px 15px 10px 15px;display:none">
      <a onclick="man('on')" class="navbar-left">Avvia manutenzione</a>
      <a onclick="man('off')" class="navbar-left">Termina manutenzione</a>
      <a onclick="deleteNote()" class="navbar-right">Rimuovi nota</a>
      <a onclick="delCommentShow()" class="navbar-right">Rimuovi commento</a>
    </div>
    <div class="delNote" style="display: none;">
      <input id="delNoteTtl" class="textInput"/>
      <button id="delNoteConfirm" onclick="delNote()" class="delNoteButton" style="position:absolute;left:330px;">Conferma</button>
    </div>
  </body>
</html>
<?php

  require_once 'core.php';
  require_once "funs.php";
  require_once 'query_funs.php';
  function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($data == "") {
      die(json_encode("NOTESNV"));
    }
    return $data;
  }

  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (empty($_GET["title"])) {
      die(json_encode("NOTESUT"));
    } else {
      $title = test_input($_GET["title"]);
      $note = searchNote(connectDb(), $title, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);
      if (empty($note[0]['title'])) {
        echo "<script>$('.noteInfoDisplay').hide();</script>";
        echo "<h6 style='font-size: 35px;'>Nota non trovata ):</h6>";
      } else {
        echo "<script>$('.noteHeaderTtl').append('<span class=spawnTtl>" . $title . "</span><br/>'); localStorage.setItem('title', '" . $title . "');</script>";
        echo "<script>$('.noteHeaderUser').append('<br/>" . $note[0]["user"] . "<br/>');</script>";
        echo "<script>$('.noteHeaderDept').append('<br/>" . $note[0]["dept"] . "<br/>');</script>";
        echo "<script>$('.noteHeaderSubj').append('<br/>" . $note[0]["subj"] . "<br/>');</script>";
        echo "<script>$('.noteHeaderYear').append('<br/>" . $note[0]["year"] . "<br/>');</script>";
        echo "<script>$('.noteHeaderDate').append('<br/>" . $note[0]["date"] . "<br/>');</script>";
        echo "<script>$('.noteContent').append('<br/><span class=spawnContent>" . getNote(connectDb(), $title) . "</span><br/>');</script>";
      }
    }
  }
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
    if (isNoteOwner(connectDb(), $title, $_SESSION["username"])) {
      echo "<script>$('#modifyNoteBtn').show();</script>";
    }

   ?>
