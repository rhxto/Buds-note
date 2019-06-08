function logout() {
  var clickBtnValue = "logout";
  var ajaxurl = '../php/sessionDestroyer.php',
  data =  {'action': clickBtnValue};
  $.post(ajaxurl, data, function (response) {
  if (response) {
      $('.logout').attr('hidden', true);
      $('.log').attr('hidden', false);
    } else {
      $('.logout').attr('hidden', true);
      $('.log').attr('hidden', false);
      error("sessione");
    }
  });
  $('.adminTools').empty();
  $(".adminTools").hide();
  $("#greet").empty();
  $(".scriviNota").empty();
  $("#scriviNotaBtn").hide();
  localStorage.setItem("logged_in", false);
}
function error(err) {
  $("#warn").show();
  switch (err) {
    case "sessione":
      $("#warn").html("Errore nel logout, se hai visto questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
    case "IES":
      $("#warn").html("Abbiamo riscontrato un errore nella ricerca, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "man":
      $("#warn").html("Il server é in manutenzione, certe funzionalità potrebbero essere bloccate.");
      break;
    case "IEMAN":
      $("#warn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANS":
      $("#warn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANR":
      $("#warn").html("Errore nella lettura dello stato della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "NOMAN":
      $("#warn").html("Non sei autorizzato a modificare lo stato della manutenzione. Questo incidente é stato segnalato.");
      break;
    case "MANAA":
      $("#warn").html("Manutenzione giá attiva!" + " Codice: " + err);
      break;
    case "MANAT":
      $("#warn").html("Manutenzione giá terminata!" + " Codice: " + err);
      break;
    case "NOTENV":
      $("#warn").html("Nota non valida!" + " Codice: " + err);
      break;
    case "NOTEANV":
      $("#warn").html("Tipo di azione non valido (se vedi questo messaggio riferiscilo agli amministratori)!" + " Codice: " + err);
      break;
    case "NOTENL":
      $("#warn").html("Devi essere loggato per scrivere una nota!");
      break;
    case "NOTEW":
      $("#warn").html("Errore nella scrittura della nota, se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "NOTEDNA":
      $("#warn").html("Non sei autorizzato a cancellare le note, questo incidente é stato segnalato");
      break;
    case "NOTEDE":
      $("#warn").html("C'é stato un errore nella rimozione della nota, controlla il log delgi erroi." + " Codice: " + err);
      break;
    case "NOTESC":
      $("#warn").html("Non si possono usare caratteri speciali in una nota! (. e / non supportati)");
      break;
    case "NOTEDNF":
      $("#warn").html("Nota non trovata!");
      break;
    case "NOTEUNV":
      $("#warn").html("Testo della nota non valido" + " Codice: " + err);
      break;
    case "NOTEUNA":
      $("#warn").html("Non sei autorizzato a modificare questa nota, l'incidendte é stato segnalato");
      break;
    case "NOTEUNE":
      $("#warn").html("La nota che volevi aggiornare non é stata trovata, copia le modifiche e prova a ricaricare la pagina. Se il problema persiste contatta gli amministratori." + " Codice: " + err);
      break;
    case "NOTEUUF":
      $("#warn").html("Errore nell'aggiornamento della nota, se vedi questo messaggio contatta gli amministratori." + " Codice: " + err);
      break;
    case "NOTERNE":
      $("#warn").html("La nota che stai provando di valutare non esiste... o.O (riferisci questo messaggio agli amministratori)" + " Codice: " + err);
      break;
    case "NOTERAE":
      $("#warn").html("Hai giá valutato questa nota! " + " Codice " + err);
      break;
    case "NOTENLG":
      $("#warn").html("Prima devi loggarti!");
      break;
    case "NOTERWIE":
      $("#warn").html("C'é stato un errore nella scrittura della valutazione della nota, rifersici questo messaggio agli amministratori." + " Codice: " + err);
      break;
    case "NOTERNV":
      $("#warn").html("Parametri di valutazione non validi!" + " Codice " + err);
      break;
    case "COMMENTNV":
      $("#warn").html("Commento non valido!" + " Codice: " + err);
      break;
    case "COMMENTANV":
      $("#warn").html("Tipo di azione non valido (se vedi questo messaggio riferiscilo agli amministratori)!" + " Codice: " + err);
      break;
    case "COMMENTNL":
      $("#warn").html("Devi essere loggato per scrivere un commento!");
      break;
    case "COMMENTW":
      $("#warn").html("Errore nella scrittura del commento, se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "COMMENTDNA":
      $("#warn").html("Non sei autorizzato a cancellare i commenti, questo incidente é stato segnalato");
      break;
    case "COMMENTDE":
      $("#warn").html("C'é stato un errore nella rimozione del commento, controlla il log delgi erroi.");
      break;
    case "COMMENTDNF":
      $("#warn").html("Commento non trovato!");
      break;
    default:
      $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
  }
  setTimeout(function(){$("#warn").hide();}, 10000);
}
function postComment() {
  var action = "write";
  var title = localStorage.getItem("title");
  var ajaxurl = '../php/commentManager.php',
  data =  {
    'type': action,
    'title': title,
    'content': $("#commentText").val().replace(/\n/g, "<br>")
  };
  $.post(ajaxurl, data, function (response) {
    response = JSON.parse(response);
  if (response["state"] == "done") {
      $(".localSpawn").append('<div id="' + response["id"] +  '"></div>');
      $("#" + response["id"]).html($("#commentText").val() + '<button class="delCommentBtn" onclick="delComment(' + response["id"] + ');">Elimina commento</button>');
      $("#commentText").val("");
    } else {
      error(response);
    }
  });
}
function man(c) {
  if (c == "on") {
   var ajaxurl = "../php/manutenzione.php";
   var manutenzione = "true";
   data = {
     'valman': manutenzione
   }
   $.post(ajaxurl, data, function(response) {
     response = JSON.parse(response);
     if (response == "done") {
       $("#warn").show();
       $("#warn").html("fatto");
       setTimeout(function(){
         $("#warn").hide();
       }, 5000);
     } else {
       error(response);
     }
   });
  } else {
    var ajaxurl = "../php/manutenzione.php";
    var manutenzione = "false";
    data = {
      'valman': manutenzione
    }
    $.post(ajaxurl, data, function(response) {
      response = JSON.parse(response);
      if (response == "done") {
        $("#warn").show();
        $("#warn").html("fatto");
        setTimeout(function(){
          $("#warn").hide();
        }, 5000);
      } else {
        error(response);
      }
    });
  }
}
function deleteNote() {
  $("#delNoteBtn").html("Conferma");
  $("#delNoteBtn").attr("onclick", "delNote()");
  $("#abortNoteDeletion").show();
}
function abortNoteDeletion() {
  $("#delNoteBtn").html("Rimuovi nota");
  $("#delNoteBtn").attr("onclick", "deleteNote()");
  $("#abortNoteDeletion").hide();
}
function delNote() {
  var ajaxurl = "../php/noteManager.php";
  var title = localStorage.getItem("title");
  data = {
    'title': title,
    'type': 'delete'
  }
  $.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if (response == "done") {
      //$(".comments").remove();
      $("#warn").show();
      $("#warn").html("Fatto");
      setTimeout(function(){
        $("#warn").hide();
      }, 5000);
    } else {
      error(response);
    }
  });
  localStorage.removeItem("title");
  $("#delNoteBtn").html("Rimuovi nota");
  $("#delNoteBtn").attr("onclick", "deleteNote()");
}

function delCommentShow() {
  $(".delNote").hide();
  $("#everythingAboutNote").show();
  $(".delCommentBtn").show();
  $("#delCommentBtn").html("Annulla");
  $("#delCommentBtn").attr("onclick", "abortCommentDeletion()");
}
function abortCommentDeletion() {
  $(".delNote").hide();
  $("#everythingAboutNote").show();
  $(".delCommentBtn").hide();
  $("#delCommentBtn").html("Rimuovi commento");
  $("#delCommentBtn").attr("onclick", "delCommentShow()");
}
function delComment(id) {
  var ajaxurl = "../php/commentManager.php";
  id = id.toString(10);
  data = {
    'id': id,
    'type': 'delete'
  }
  $.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if (response == "done") {
      $("#warn").show();
      $("#warn").html("Fatto");
      setTimeout(function(){
        $("#warn").hide();
      }, 5000);
    } else {
      error(response);
    }
    $("#" + id).remove();
    $("#everythingAboutNote").show();
  });
}
function showNoteEditor() {
  $("#modifyNoteBtn").hide();
  $(".spawnTtl").replaceWith("<textarea id='modifyTtlTxtH' rows='1' cols='100'>" + $(".spawnTtl").html() + "</textarea>");
  //senza /.../g js rimpiazza solo il primo match
  $(".spawnContent").replaceWith("<textarea id='modifyContentTxtH' cols='100' rows='10'>" + $(".spawnContent").html().replace(/<br>/g,"\n") + "</textarea>");
  $("#modifyNoteConfirm").show();
}
function modifyNote() {
  $("#modifyNoteConfirm").hide();
  $("#modifyNoteBtn").show();
  $("#modifyTtlTxtH").replaceWith("<span class='spawnTtl'>" + $("#modifyTtlTxtH").val() + "</span>");
  var content = $("#modifyContentTxtH").val();
  $("#modifyContentTxtH").replaceWith("<span class='spawnContent'>" + $("#modifyContentTxtH").val().replace(/\n/g, "<br>") + "</span>");
  var ajaxurl = "../php/noteManager.php";
  data = {
    'title': localStorage.getItem("title"),
    'newTitle': $(".spawnTtl").html(),
    'newContent': content,
    'type': 'update'
}
  $.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if (response == "done") {
      url = $(".spawnTtl").html();
      localStorage.setItem('title', url);
      //in questo modo aggiorniamo il link della pagina senza doverla ricaricare con window.location.href, i primi due parametri della funzione servono ad altre cose
      window.history.pushState("", "", "http://" + location.host + "/php/viewNote.php?title=" + url.replace(" ", "%20"));
    } else {
      error(response);
    }
  });
}
function toolbarUser() {
  $(".adminTools").show();
  $(".user").show();
}
function rateNote(rating) {
  var ajaxurl = "../php/noteManager.php";
  data = {
    'title': localStorage.getItem("title"),
    'rating': rating,
    'type': 'rate'
}
  $.post(ajaxurl, data, function(response) {
    response = JSON.parse(response);
    if (response["status"] == "done") {
      if (rating && response["type"] == "modify") {
        $(".likes").html(parseInt($(".likes").html()) + 1);
        $(".dislikes").html(parseInt($(".dislikes").html()) - 1);
      } else if (!rating && response["type"] == "modify") {
        $(".likes").html(parseInt($(".likes").html()) - 1);
        $(".dislikes").html(parseInt($(".dislikes").html()) + 1);
      } else if (rating) {
        $(".likes").html(parseInt($(".likes").html()) + 1);
      } else {
        $(".dislikes").html(parseInt($(".dislikes").html()) + 1);
      }
    } else {
      error(response);
    }
  });
}
$(document).ready(function() {
  $("#commentText").on("keypress", function (event) {
    var text = $("#commentText").val();
    var lines = text.split("\n");
    var current = this.value.substr(0, this.selectionStart).split("\n").length;
    if (event.keyCode != 13) {
      if (lines[current - 1].length >= ($(this).attr('cols') - 16)) {
        $('textarea').val($('textarea').val() + "\n");
      }
    }
  });
});
