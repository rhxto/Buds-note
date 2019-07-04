var errThrown = false;
function submitform() {
    var username = $("#Username").val();
    var password = $("#Password").val();
    var mail = $("#Email").val();
    var ajaxurl = '../php/write.php',
    data =  {'Username': username,
             'Password' : password,
             'Mail' : mail};
    $.post(ajaxurl, data, function (response) {
    if (response == "passed") {
      window.location.href = "../login/";
    } else {
      errore(response);
    }
  });
}
function hasWhiteSpace(s) {
  return s.indexOf(' ') >= 0;
}
function testMail(m){
  var at = 0;
  var dot = 0;
  for(var i = 0; i <= m.length; i++){
    if(m.charAt(i)=="@"){
      at++;
    }else if(m.charAt(i)=="."){
      dot++;
    }

  }
  if(hasWhiteSpace(m) == false && at==1 && dot>=1){
    return true;
  }
}
function testInput() {
  var usr = $("#Username").val();
  var pswd = $("#Password").val();
  var rpswd = $("#Conferma_Password").val();
  var mail = $("#Email").val();

  if(usr == null || pswd == null|| rpswd == null || mail == null || usr == undefined || pswd == undefined || rpswd == undefined || mail == undefined || usr == "" || pswd == "" || rpswd == "" || mail == "" || mail.endsWith(".") || mail.endsWith(" ") || mail.endsWith("@") || mail.endsWith("'") || usr.length > 30 || pswd.length > 30 || mail.length > 50)  {
    if (errThrown == false) {
      $("#Warning").html("Inserire dati validi!");
      errThrown = true;
    }
  } else if (hasWhiteSpace(usr) == false && hasWhiteSpace(pswd) == false && testMail(mail) && pswd == rpswd) {
    submitform();
  } else {
    if (errThrown == false) {
      $("#Warning").html("Inserire dati validi!");
      errThrown = true;
    }
  }
}
function errore(err) {
  switch (err) {
    case "usernameEsiste":
    $("#Warning").html("Username giá esistente!");
      break;
    case "nonAN":
    $("#Warning").html("Username o password non validi (niente caratteri speciali)!");
      break;
    default:
    $("#Warning").html("Errore (riferisci questo messaggio agli amministratori)" + " Codice: " + err);
    break;
  }
}
