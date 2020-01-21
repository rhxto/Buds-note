function selectSubj(which) {
  $("#Subject").html($(which).html());
}

function selectDept(which) {
  $("#Dept").html($(which).html());
}

function submitNote() {
  var ajaxurl = "../php/noteManager.php";
  var title = $("#Title").val();
  var subj = $("#Subject").html();
  var dept = $("#Dept").html();
  for (var i = 1; i <= 5; i++) {
    if ($("#year_"+i).prop("checked")) {
      var year = i;
      break;
    }
  }
  var content = $("#Content").val();

  if (title.trim() == "" || content.trim() == "") {
    localError("cntNv");
  } else if (![1, 2, 3, 4, 5].includes(year)) {
    localError("yNv");
  } else if (subj.trim() == "Subject") {
    localError("sNv");
  } else if (dept.trim() == "Department") {
    localError("dNv");
  } else {
    startAnimation();
    data = {
      'title': title,
      'content': content,
      'subj': subj,
      'dept': dept,
      'year': year,
      'type': 'write'
    }
    $.post(ajaxurl, data, function(response) {
      response = JSON.parse(response);
      console.log(response);
      if (response["status"] == "done") {
        localStorage.setItem("noteId", response["id"]);
        if ($("#uploadImage").val() !== '') {
          uploadImage();
        } else {
          setTimeout(function(){
            window.location.href = "https://budsnote.ddns.net/viewNote/?id=" + localStorage.getItem("noteId");
          }, 1000);
        }
      } else {
        error(response["status"]);
        setTimeout(function(){
          $("#animation").hide();
          $(".pageContainer").show();
        }, 3000);
      }
    });
  }
}

function uploadImage() {
  if ($("#uploadImage").val() !== '') {
    var image = document.getElementById("uploadImage").files[0];
    var image_name = image.name;
    var image_extension = image_name.split('.').pop().toLowerCase();
    if (image_name.split('.').length > 2) {
      alert("Formato immagine non supportato! (Mantenere solo l'estensione originale)");
    } else {
      if (jQuery.inArray(image_extension, ["gif", "png", "jpg", "jpeg"]) == -1) {
        alert("Formato non supportato!");
      } else {
        var image_size = image.size;
        if (image_size > 22000000) {
          alert("La dimensione massima per un'immagine é di 22MB!");
        } else {
          var data = new FormData();
          data.append("uploadImage", image);
          data.append("noteId", localStorage.getItem("noteId"));
          $.ajax({
            url:"../php/uploadImg.php",
            method:"POST",
            data:data,
            contentType:false,
            cahe:false,
            processData:false,
            beforeSend:function(){
              $("#warn").html("<br/><div class='progress'><div id='imageUploadProgress' class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width: 0%'></div></div>");
              $("#warn").attr("style", "background-color: white;");
              $("#warn").show();
              localStorage.setItem("uploadStatus", "time_wait");
            },
            xhr:function(){
              var xhr = new window.XMLHttpRequest();
              xhr.upload.addEventListener("progress", function(event) {
                if (localStorage.getItem("uploadStatus") !== "done") {
                  $("#imageUploadProgress").attr("style", "width: " + (event.loaded/event.total*100) + "%");
                }
              }, false);
              xhr.addEventListener("load", function(event) {
                localStorage.setItem("uploadStatus", "done");
                  setTimeout(function(){
                    window.location.href = "https://budsnote.ddns.net/viewNote/?id=" + localStorage.getItem("noteId");
                  }, 1000);
              }, false);
              return xhr;
            },
            success:function(response){
              response = JSON.parse(response);
              if (response["status"] !== "success") {
                localStorage.setItem("uploadStatus", "success");
                error(response["status"]);
              } else {
                localStorage.setItem("uploadStatus", "failure");
              }
            }
          });
        }
      }
    }
  }
}

$(document).ready(function() {
  var check = false;
  if (check) {
    localError("mobile");
  }
  $(".custom-file-input").on("change", function() {
    var fileName = $(this).val().split("\\").pop();
    $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
  });

  $("#uploadImage").on("change", function(){
    var image = document.getElementById("uploadImage").files[0];
    var image_name = image.name;
    var image_extension = image_name.split('.').pop().toLowerCase();
    if (image_name.split('.').length > 2) {
      alert("Formato immagine non supportato! (Mantenere solo l'estensione originale)");
    } else {
      if (jQuery.inArray(image_extension, ["gif", "png", "jpg", "jpeg"]) == -1) {
        alert("Formato non supportato!");
      } else {
        if (image.size > 22000000) {
          alert("La dimensione massima per un'immagine é di 22MB!");
        }
      }
    }
  });
});

function startAnimation() {
  $("#animation").html("<span class='back'><span>L</span><span>o</span><span>a</span><span>d</span><span>i</span><span>n</span><span>g</span></span><br/><span id='warn'></span>");
  $("#animation").show();
  $(".pageContainer").hide();
}

function localError(err) {
  $("#localWarn").show();
  switch (err) {
    case "cntNv":
      txt = "Il titolo o il contenuto non possono essere solamente composti da spazi.";
      break;
    case "yNv":
      txt = "Selezionare un anno.";
      break;
    case "dNv":
      txt = "Selezionare un indirizzo.";
      break;
    case "sNv":
      txt = "Selezionare una materia.";
      break;
    case "mobile":
      txt = "La pagina é instabile su broswers per telefoni, ne sconsigliamo l'uso fino alla rimozione di questo avviso.";
      break;
  }
  $("#localWarn").html(txt);
  setTimeout(function(){
    $("#localWarn").hide();
  }, 5000);
}
