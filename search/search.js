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
    $("#results").empty();
    var response = JSON.parse(response);
    console.log(response);
    if (response == "Nrt") {
      $("#results").append("<div class='row'><div class='col-md-12'>Nessun risultato trovato</div></div>");
    } else if (response == "IES" || response == "IE" || response == "NOTESYNV") {
      error(response);
    } else {
      for (i = 0; i < response.length; i++) {
        if (i != 0) {
          $("#results").append("<hr/>");
        }
        $("#results").append("<div class='row'><div class='col-md-6'><a href='php/viewNote.php?noteId=" + response[i]["id"] + "'>" + response[i]["title"] + "</a></div><div class='col-md-6'><a>" + response[i]["user"] + "</a></div></div><div class='row'><div class='col-md-6'><a>" + response[i]["date"] + "</a></div></div>");
      }
    }
  });
}

function selectSubj(which) {
  $("#Subject").html($(which).html());
}

function selectDept(which) {
  $("#Dept").html($(which).html());
}
