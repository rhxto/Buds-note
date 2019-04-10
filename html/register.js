var errThrown = false;
function submitform() {
  document.forms["form"].submit();
}
function hasWhiteSpace(s) {
  return s.indexOf(' ') >= 0;
}
function testMail(m){
  var at = 0;
  var dot = 0;
  for(var i = 0, i <= m.lenght, i++){
    if(m.charAt(i)==@){
      at++;
    }else if(m.charAt(i)==.){
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

  if(usr == null || pswd == null|| rpswd == null || mail == null || usr == undefined || pswd == undefined || rpswd == undefined || mail == undefined || usr == "" || pswd == "" || rpswd == "" || mail == "") {
    if (errThrown == false) {
      $("#Warning").append("Inserire dati validi!");
      errThrown = true;
    }
  } else if (hasWhiteSpace(usr) == false && hasWhiteSpace(pswd) == false && testMail(mail) && pswd == rpswd) {
    submitform();
  } else {
    if (errThrown == false) {
      $("#Warning").append("Inserire dati validi!");
      errThrown = true;
    }
  }
}
