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
