$(document).ready(function(){
    $('#logout').click(function(){
        var clickBtnValue = "logout";
        var ajaxurl = '../php/sessionDestroyer.php',
        data =  {'action': clickBtnValue};
        $.post(ajaxurl, data, function (response) {
            alert(response);
        });
    });
});
