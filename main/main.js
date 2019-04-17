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
error(err) {
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
