$(document).ready(function(){
    $('#logout').click(function(){
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
    });
});
function error(err) {
  switch (err) {
    case "sessione":
      $("#error").attr("hidden", false);
      $("#error").html("Errore nel logout, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break;
    default:
    $("#error").attr("hidden", false);
    $("#error").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori.");
    break
  }
}
<<<<<<< HEAD
function cerca() {
  var arg = $("#search").val();
  var ajaxurl = "../php/ricerca.php";
  if ($("#deptNum").prop("checked") == true) {
    type = "deptNum";
  } else if ($("#deptName").prop("checked") == true) {
    type = "deptName";
  } else {
    $("#risultati").html("Inserisci un criterio di ricerca!");
  }
  data =  {'phrase': arg,
  'type': type};
  $.post(ajaxurl, data, function (response) {
    $("#risultati").html(response);
  });
}
=======
>>>>>>> 35ff0e909c4c2e7e0dea59b2644026a07772ba25
