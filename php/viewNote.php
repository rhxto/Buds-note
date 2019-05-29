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
        <div class="noteHeaderTtl">
        </div>
        <div class="noteInfo">
          <br/>
          <div class="noteHeaderUser">Utente: </div>
          <div class="noteHeaderDept">Indirizzo: </div>
          <div class="noteHeaderSubj">Materia: </div>
          <div class="noteHeaderYear">Anno: </div>
          <div class="noteHeaderDate">Data: </div><br/>
          <div class="noteRating"></div>
          <button id="mipiace" onclick="rateNote(true)">Mi piace</button>
          <button id="nonmipiace" onclick="rateNote(false)">Non mi piace</button>
        </div>
        <div class="noteContent"></div>
      </div>
      <div class="comments" style="display: none;">
        <span style="font-weight:bold;font-size:35px">COMMENTI</span><br/>
        <div class="localSpawn"></div>
        <div class="otherComments">
        </div>
        <?php
          require_once 'core.php';
          require_once 'query_funs.php';
          $comments = searchRevw(connectDb(), NULL, NULL, $_GET["title"], NULL, NULL, NULL, NULL, NULL);
          foreach ($comments as $comment) {
            $comment["review"] = str_replace("&lt;br&gt;", "<br>", $comment["review"]);
            echo "<script>$('.otherComments').append('<span id=" . $comment["id"] . ">" . $comment['review'] . " - " . $comment["user"] . " - " . $comment["date"] . "<button class=delCommentBtn onclick=delComment(" . $comment["id"] . ");>Elimina commento</button><br/></span>');</script>";
          }
        ?>
         <br/>
        <textarea rows="1" cols="100" placeholder="Inserisci un commento..." id="commentText" style="display: none;" class="postCommentElms commentTxt"></textarea>
        <button onclick="postComment()" style="display: none;" class="postCommentElms commentBtn">Pubblica</button>
      </div>
    </div>
    <div id="warn" class="warn" style="display:none">
    </div>
    <div class="navbar adminTools" style="/*position:absolute;bottom:5px;padding:10px 15px 10px 15px;*/display:none">
      <a onclick="man('on')" class="navbar-left admin" style="display: none;">Avvia manutenzione</a>
      <a onclick="man('off')" class="navbar-left admin" style="display: none;">Termina manutenzione</a>
      <a id="modifyNoteBtn" onclick="showNoteEditor()" class="navbar-right user" style="display: none;">Modifica nota</a>
      <a id="modifyNoteConfirm" onclick="modifyNote()"class="navbar-right" style="display: none;">Salva</a>
      <a onclick="abortNoteDeletion()" id="abortNoteDeletion" class="navbar-right" style="display: none;">Annulla</a>
      <a onclick="deleteNote()" class="navbar-right admin" id="delNoteBtn" style="display: none;">Rimuovi nota</a>
      <a onclick="delCommentShow()" class="navbar-right admin" id="delCommentBtn" style="display: none;">Rimuovi commento</a>
    </div>
    <div class="delNote" style="display: none;">
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
        echo "<script>$('.noteHeaderUser').append('" . $note[0]["user"] . "');</script>";
        echo "<script>$('.noteHeaderDept').append('" . $note[0]["dept"] . "');</script>";
        echo "<script>$('.noteHeaderSubj').append('" . $note[0]["subj"] . "');</script>";
        echo "<script>$('.noteHeaderYear').append('" . $note[0]["year"] . "');</script>";
        echo "<script>$('.noteHeaderDate').append('" . $note[0]["date"] . "');</script>";
        echo "<script>$('.noteContent').append('<span class=spawnContent></span><br/>');</script>";
        foreach (getNote(connectDb(), $title) as $row) {
          $row = str_replace("\n", "<br />", $row);
          $row = str_replace("'", "&#39;", $row);
          echo "<script>$('.spawnContent').append('" . $row . "');</script>";
        }
      }
    }
  }
  if(gettype($m = getManStatus()) === "string") {
    echo "<script>error($m);</script>";
  } elseif ($m == true) {
    echo "<script>error('man');</script>";
  }
  if (checkNote(connectDb(), $_GET["title"])) {
    echo "<script> $('.comments').show();</script>";
  }
  if (isset($_SESSION['logged_in'])) {
    if ($_SESSION['logged_in'] == '1') {
      echo "<script>$('.log').attr('hidden', true); $('#scriviNotaBtn').show();</script>";
    if(getAcclvl($_SESSION["username"]) == 1) {
          echo "<script>$('.adminTools').show(); $('.admin').show();</script>";
      }
      echo "<script>$('#greet').html('Benvenuto,  " . $_SESSION['username'] . "');</script>";
      if (isNoteOwner(connectDb(), $title, $_SESSION["username"])) {
        echo "<script>$('#modifyNoteBtn').show(); toolbarUser();</script>";
      }
      if (checkNote(connectDb(), $_GET["title"])) {
        echo "<script> $('.postCommentElms').show();</script>";
        if (($likes = getLikes($_GET["title"])) === false || ($dislikes = getDislikes($_GET["title"])) === false) {
          echo "<script>error('NOTEREF'); $('.noteRating').append('Errore nel fetching dei likes e dislikes D:');</script>";
        } else {
          echo "<script>$('.noteRating').append('Likes: $likes Dislikes: $dislikes');</script>";
        }
      }
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
