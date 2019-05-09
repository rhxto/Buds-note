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
      $("#warn").html("Errore nel logout, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break;
    default:
    $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break;
  }
}
function cerca() {
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
