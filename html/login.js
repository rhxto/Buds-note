var errThrown = false;
function submitform() {
  document.forms["form"].submit();
}
function hasWhiteSpace(s) {
  return s.indexOf(' ') >= 0;
}
function testInput() {
  var usr = $("#Username").val();
  var pswd = $("#Password").val();

  if(usr == null || pswd == null|| usr == undefined || pswd == undefined || usr == "" || pswd == "") {
    if (errThrown == false) {
      $("#Warning").html("Inserire dati validi!");
      errThrown = true;
    }
  } else if (hasWhiteSpace(usr) == false && hasWhiteSpace(pswd) == false) {
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
    case "credenziali":
    $("#Warning").html("Username o password non corretti!");
    break;
    default:
    break;
  }
}
