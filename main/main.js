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
  if ($("#Materie").val() == "") {
    var filtro = false;
  } else {
    var filtro = true;
  }
  if (arg == "" && !filtro) {
    $("#risultati").html("Inserisci una ricerca valida!");
    type = null;
  } else if ($("#deptNum").prop("checked") == true) {
    type = "deptNum";
    var l = "dept";
  } else if ($("#deptName").prop("checked") == true) {
    type = "deptName";
    var l = "dept";
  } else if ($("#subjName").prop("checked") == true) {
    type = "subjName";
    var l = "subj";
  } else if ($("#subjNum").prop("checked") == true) {
    type = "subjNum";
    var l = "subj";
  } else if ($("#noteTtl").prop("checked") == true) {
    type = "noteTtl";
  } else {
    type = "note";
  }
  if (type != null && arg != null && type != "note") {
    data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      $("#risultati").empty();
      var response = JSON.parse(response);
      if (response == "Nrt") {
        $("#risultati").html("Nessun risultato trovato")
      }
      for (i = 0; i < response[1].length; i++) {
        $("#risultati").append(response[1][i] + "<br/>");
      }
    });
  } else if (type == "note"){
    var title = arg;
    var user = $("#filtroUtente").val();
    var subj = $("#Materie").val();
    var year = $("#Anno").val();
    var dept = $("#Indirizzo").val();
    var teacher = $("#Insegnante").val();
    var date = $("#Data").val();
    data = {
      "type": type,
      "title": title,
      "user": user,
      "subj": subj,
      "year": year,
      "dept": dept,
      "teacher": teacher,
      "date": date
    }
    $.post(ajaxurl, data, function (response) {
      $("#risultati").empty();
      var response = JSON.parse(response);
      if (response == "Nrt") {
        $("#risultati").html("Nessun risultato trovato");
      } else {
        for (i = 0; i < response.length; i++) {
          $("#risultati").append(response[i]["title"] + "<br/>");
        }
      }
    });
  } else {
    $("#risultati").html("Parametri di ricerca non validi");
  }
  arg = null;
  type = null;
}
function hideSearch() {
  document.getElementById("Search").style.display = "none";
  document.getElementById("SearchDiv").style.display = "none";
}
function getSubjs() {
  hideSearch();
  var ajaxurl = "../php/research.php";
  var type = "subjs";
  arg = "";
  data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      $("#risultati").empty();
      var response = JSON.parse(response);
      if (response == "Nrt") {
        $("#risultati").html("Nessun risultato trovato");
      } else {
        for (i = 0; i < response[1].length; i++) {
          $("#risultati").append("<a href='subj/" + i + "/'>" + response[1][i] + "</a><br/>");
        }
      }
      });
}
function getDepts() {
  hideSearch();
  var ajaxurl = "../php/research.php";
  var type = "depts";
  arg = "";
  data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      $("#risultati").empty();
      var response = JSON.parse(response);
      if (response == "Nrt") {
        $("#risultati").html("Nessun risultato trovato");
      } else {
        for (i = 0; i < response[1].length; i++) {
          $("#risultati").append("<a href='dept/" + i + "/'>" + response[1][i] + "</a><br/>");
        }
      }
      });
}
function getNotes() {
  hideSearch();
  var ajaxurl = "../php/research.php";
  var type = "notes";
  arg = "";
  data =  {'phrase': arg,
    'type': type};
    $.post(ajaxurl, data, function (response) {
      var response = JSON.parse(response);
      $("#risultati").empty();
      if (response == "Nrt") {
        $("#risultati").html("Nessun risultato trovato");
      } else {
        for (i = 0; i < response.length; i++) {
          $("#risultati").append(response[i]["title"] + "<br/>");
        }
      }
      });
}
$(document).ready(function(){
  document.getElementById("search").addEventListener("keyup", function(event) {
    if (event.keyCode === 13) {
     event.preventDefault();
     cerca();
    }
  });
});
