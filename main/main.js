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
}
function error(err) {
  switch (err) {
    case "sessione":
      $("#warn").show();
      $("#warn").html("Errore nel logout, se hai visto questo messaggio riferiscilo agli amministratori.");
    break;
    default:
    $("#warn").show();
    $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break;
  }
  setTimeout(function(){$("#warn").hide();}, 10000);
}
function cerca() {
  document.getElementById("Search").style.display = "none";
  document.getElementById("SearchDiv").style.display = "none";
  var arg = $("#search").val();
  var ajaxurl = "../php/research.php";
  if (arg == "") {
    $("#risultati").html("Inserisci una ricerca valida!");
    type = null;
  } else if ($("#deptNum").prop("checked") == true) {
    type = "deptNum";
  } else if ($("#deptName").prop("checked") == true) {
    type = "deptName";
  } else if ($("#subjName").prop("checked") == true) {
    type = "subjName";
  } else if ($("#subjNum").prop("checked") == true) {
    type = "subjNum";
  } else if ($("#noteTtl").prop("checked") == true) {
    type = "noteTtl";
  } else {
    $("#risultati").html("Inserisci un criterio di ricerca!");
    type = null;
  }
  if (type != null && arg != null) {
    data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      $("#risultati").html(response);
      response = null;
    });
  }
  arg = null;
  type = null;
}
$(document).ready(function(){
  document.getElementById("search").addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
     event.preventDefault();
     cerca();
    }
  });
});
