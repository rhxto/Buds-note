function openSearch() {
  $(".scriviNota").hide();
  $("#scriviNotaBtn").show();
  $(".searchUser").hide();
  $(".homePage").hide();
  document.getElementById("Search").style.display = "block";
  document.getElementById("SearchDiv").style.display = "block";
}
function closeSearch() {
  document.getElementById("Search").style.display = "none";
  document.getElementById("SearchDiv").style.display = "none";
  $(".searchUser").hide();
}
function openUserSearch() {
  document.getElementById("Search").style.display = "none"; //lascia lo sfondo di ricerca
  $(".searchUser").show();
}
