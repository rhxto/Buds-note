$(document).ready(function() {
  var ajaxurl = "../../php/research.php";
  var type = "noteDept";
  arg = "Liceo scientifico";
  data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      response = JSON.parse(response);
      if (response !== "Nrt") {
        for (var i = 0; i < response.length; i++) {
          response[i]["title"] = response[i]["title"].replace(/sc-a/g, "&apos;");
          response[i]["title"] = response[i]["title"].replace(/sc-q/g, "&quot;");
          $("#risultati").append("<a href='../../php/viewNote.php?noteId=" + response[i]["id"] + "'>" + response[i]["title"] + " Autore: " + response[i]["user"] + " Data: " + response[i]["date"] + "</a><br/>");
        }
      } else {
        error("Nrt");
      }
      response = null;
    });
});
function logout() {
  var clickBtnValue = "logout";
  var ajaxurl = '../../php/sessionDestroyer.php',
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
}
function error(err) {
  $("#warn").show();
  switch (err) {
    case "sessione":
      $("#warn").html("Errore nel logout, se hai visto questo messaggio riferiscilo agli amministratori.");
    break;
    case "IES":
      $("#warn").html("Abbiamo riscontrato un errore nella ricerca, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "Nrt":
      $("#warn").html("Non sono state trovate note per questo indirizzo, se vedi che alcune ne fanno parte ma non sono listate riferisci il messaggio agli amministratori.");
      break;
    case "FIREFOX":
      $("#warn").html("A causa di errori nel broswer, alcuni elementi del sito potrebbero non funzionare correttamente in Firefox (consigliamo Chrome o Edge). Vedi: https://support.mozilla.org/en-US/questions/1191898 <button class='mozErrorDeactivation' onclick='mozShown()'>Ok</button>");
      break;
    default:
    $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break;
  }
  setTimeout(function(){$("#warn").hide();}, 10000);
}
