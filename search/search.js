function search() {
  var title = $("#Title").val();
  var user = $("#User").val();
  var subj = $("#Subject").val();
  var years = [];
  for (var i = 0; i < 5; i++) {
    if ($("#inputyear_" + (i + 1)).prop("checked")) {
      years[i] = true;
    } else {
      years[i] = false;
    }
  }
  var dept = $("#filtroIndirizzo").val();
  var teacher = user;
  var datefrom = $("#filtroDatefrom").val();
  var dateto = $("#filtroDateto").val();
  var orderby = $("#filtroOrderBy").val();
  var order = $("#filtroOrdine").val();

  data = {
    "type": type,
    "title": title,
    "user": user,
    "subj": subj,
    "years": years,
    "dept": dept,
    "teacher": teacher,
    "datefrom": datefrom,
    "dateto": dateto,
    "orderby": orderby,
    "order": order
  }
  $.post(ajaxurl, data, function (response) {
    $("#risultati").empty();
    var response = JSON.parse(response);
    console.log(response);
    // if (response == "Nrt") {
    //   $("#risultati").html("Nessun risultato trovato");
    // } else if (response == "IES" || response == "IE" || response == "NOTESYNV") {
    //   error(response);
    // } else {
    //   for (i = 0; i < response.length; i++) {
    //     $("#risultati").append("<a href='php/viewNote.php?noteId=" + response[i]["id"] + "'>" + response[i]["title"] + " Autore: " + response[i]["user"] + " Data: " + response[i]["date"] + "</a><br/>");
    //   }
    // }
  });
}

function selectSubj(which) {
  $("#Subject").html($(which).html());
}

function selectDept(which) {
  $("#Dept").html($(which).html());
}
