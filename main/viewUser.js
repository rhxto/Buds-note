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
    default:
      $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
  }
  setTimeout(function(){$("#warn").hide();}, 10000);
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
