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
      <span id="greet">
        <?php
          if (isset($_SESSION["logged_in"])) {
            if ($_SESSION["logged_in"] === "1") {
              echo "Benvenuto, " . $_SESSION["username"];
            }
          }
        ?>
      </span>
    </p>
    <div id="everythingAboutNote">
      <div class="noteInfoDisplay" hidden>
        <?php
          require_once 'core.php';
          require_once "funs.php";
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
            }
            if (empty($note[0]['title'])) {
              echo "<script>$('.noteInfoDisplay').hide();</script>";
              echo "<h6 style='font-size: 35px;'>Nota non trovata ):</h6>";
              $display = false;
            } else {
              $display = true;
            }
          }
          ?>
        <div class="noteHeaderTtl">
          <?php
            if ($display) {
              echo "<span class=spawnTtl>" . $title . "</span><br/><script>localStorage.setItem('title', '" . $title . "');</script>";
            }
          ?></div>
        <div class="noteInfo">
          <br/>
          <div class="noteHeaderUser">Utente:
            <?php
              if ($display) {
                echo $note[0]["user"];
              }
            ?></div>
          <div class="noteHeaderDept">Indirizzo:
            <?php
              if ($display) {
                echo $note[0]["dept"];
              }
            ?></div>
          <div class="noteHeaderSubj">Materia:
            <?php
              if ($display) {
                echo $note[0]["subj"];
              }
            ?></div>
          <div class="noteHeaderYear">Anno:
            <?php
              if ($display) {
                echo $note[0]["year"];
              }
            ?></div>
          <div class="noteHeaderDate">Data:
            <?php
              if ($display) {
                echo $note[0]["date"];
              }
            ?></div><br/>
          <div class="noteRating">
            Likes:
            <?php
            if (checkNote(connectDb(), $_GET["title"])) {
                if (($likes = getLikes($_GET["title"])) === false || ($dislikes = getDislikes($_GET["title"])) === false) {
                  echo "<script>error('NOTEREF'); $('.noteRating').html('Errore nel fetching dei likes e dislikes D:');</script>";
                  $displayRate = false;
                } else {
                  echo "<span class='likes'>" . $likes . "</span>";
                  $displayRate = true;
                }
              }
              ?>
            Dislikes: <?php
              if ($displayRate) {
                echo "<span class='dislikes'>" . $dislikes . "</span>";
              }
            ?>
          </div>
          <button id="mipiace" onclick="rateNote(true)">Mi piace</button>
          <button id="nonmipiace" onclick="rateNote(false)">Non mi piace</button>
        </div>
        <div class="noteContent">
          <?php
            foreach (getNote(connectDb(), $title) as $row) {
              $row = str_replace("\n", "<br />", $row);
              $row = str_replace("'", "&#39;", $row);
              echo $row;
            }
           ?>
        </div>
      </div>
      <div class="comments" style="display: none;">
        <span>COMMENTI</span><br/>
        <div class="localSpawn"></div>
        <div class="otherComments">
          <?php
            require_once 'core.php';
            require_once 'funs.php';
            $comments = searchRevw(connectDb(), NULL, NULL, $_GET["title"], NULL, NULL, NULL, NULL, NULL);
            foreach ($comments as $comment) {
              $comment["review"] = str_replace("&lt;br&gt;", "<br>", $comment["review"]);
              $comment = str_replace("'", "&#39", $comment);
              echo "<span id=" . $comment["id"] . "><div class=commentText>" . $comment['review'] . "<button class=delCommentBtn onclick=delComment(" . $comment["id"] . ");>Elimina commento</button></div><div class=commentInfo>" . $comment["user"] . " - " . $comment["date"] . "</div><br/></span>";
            }
          ?>
        </div>
         <br/>
        <textarea rows="1" cols="100" wrap="hard" placeholder="Inserisci un commento..." id="commentText" style="display: none;" class="postCommentElms commentTxt"></textarea>
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
      if (isNoteOwner(connectDb(), $title, $_SESSION["username"])) {
        echo "<script>$('#modifyNoteBtn').show(); toolbarUser();</script>";
      }
      if (checkNote(connectDb(), $_GET["title"])) {
        echo "<script> $('.postCommentElms').show();</script>";
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
