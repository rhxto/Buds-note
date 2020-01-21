function error(err) {
  $("#warn").show();
  switch (err) {
    case "sessione":
      $("#warn").html("Errore nel logout, se hai visto questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
    case "IES":
      $("#warn").html("Abbiamo riscontrato un errore nella ricerca, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "man":
      $("#warn").html("Il server é in manutenzione, certe funzionalità potrebbero essere bloccate.");
      break;
    case "IEMAN":
      $("#warn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANS":
      $("#warn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANR":
      $("#warn").html("Errore nella lettura dello stato della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "NOMAN":
      $("#warn").html("Non sei autorizzato a modificare lo stato della manutenzione. Questo incidente é stato segnalato.");
      break;
    case "MANAA":
      $("#warn").html("Manutenzione giá attiva!" + " Codice: " + err);
      break;
    case "MANAT":
      $("#warn").html("Manutenzione giá terminata!" + " Codice: " + err);
      break;
    case "NOTENV":
      $("#warn").html("Nota non valida!" + " Codice: " + err);
      break;
    case "NOTEANV":
      $("#warn").html("Tipo di azione non valido (se vedi questo messaggio riferiscilo agli amministratori)!" + " Codice: " + err);
      break;
    case "NOTENL":
      $("#warn").html("Devi eseguire il login per scrivere una nota!");
      break;
    case "NOTEW":
      $("#warn").html("Errore nella scrittura della nota, se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "NOTEWYNV":
      $("#warn").html("Anno non valido! Codice: " + err);
      break;
    case "NOTEDNA":
      $("#warn").html("Non sei autorizzato a cancellare le note, questo incidente é stato segnalato");
      break;
    case "NOTEDE":
      $("#warn").html("C'é stato un errore nella rimozione della nota, controlla il log degli errori.");
      break;
    case "NOTESC":
      $("#warn").html("Non si possono usare caratteri speciali in una nota! (. e / non supportati)");
      break;
    case "NOTESYNV":
      $("#warn").html("Anno della ricerca non valido! Codice: " + err);
      break;
    case "NOTEDNF":
      $("#warn").html("Nota non trovata!");
      break;
    case "NOTEUNV":
      $("#warn").html("Testo della nota non valido");
      break;
    case "NOTEUNA":
      $("#warn").html("Non sei autorizzato a modificare questa nota, l'incidendte é stato segnalato");
      break;
    case "NOTEUNE":
      $("#warn").html("La nota che volevi aggiornare non é stata trovata, copia le modifiche e prova a ricaricare la pagina. Se il problema persiste contatta gli amministratori.");
      break;
    case "FIREFOX":
      $("#warn").html("A causa di errori nel broswer, alcuni elementi del sito potrebbero non funzionare correttamente in Firefox (consigliamo Chrome o Edge). Vedi: https://support.mozilla.org/en-US/questions/1191898 <button class='mozErrorDeactivation' onclick='mozShown()'>Ok</button>");
      break;
    case "NOTEWAE":
      $("#warn").html("Nota giá esistente");
      break;
    case "IEAG":
      $("#warn").html("Errore nel retrieve di acc**** se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "USERANV":
      $("#warn").html("Azione non valida!" + " Codice: " + err);
      break;
    case "USERNV":
      $("#warn").html("Parametro per l'azione sull'utente non valido! (Attenzione ai caratteri speciali)" + " Codice: " + err);
      break;
    case "USERNL":
      $("#warn").html("Prima devi eseguire il login!" + " Codice: " + err);
      break;
    case "USERSNV":
    $("#warn").html("Parametro per ricerca dell'utente non valido! (Attenzione ai caratteri speciali)" + " Codice: " + err);
      break;
    case "USERSIE":
      $("#warn").html("Abbiamo riscontrato un errore interno nella ricerca dell'utente, riferisci questo messaggio agli amministratori." + " Codice: " + err);
      break;
    case "IMGUAE":
      //finché non si potranno modificare le immagini relative alla nota, questo errore non dovrebbe apparire
      $("#warn").html("Hai giá caricato un'immagine relativa alla nota con lo stesso nome!" + " Codice: " + err);
      break;
    case "IMGUIE":
      $("#warn").html("Abbiamo riscontrato un errore interno nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUNL":
      $("#warn").html("Prima devi eseguire il login!" + " Codice: " + err);
      break;
    case "IMGUUE":
      $("#warn").html("Errore sconosciuto nel caricamento dell'immagine! <bold>Riferisci questo messaggio agli amministratori</bold> (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUVNV":
      $("#warn").html("Valori non validi per il caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUFNS":
      $("#warn").html("Questo tipo di immagine non é supportato! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUFNI":
      $("#warn").html("Il file che stai cercando di caricare non é un'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + "Codice: " + err);
      break;
    case "IMGUFTB":
      $("#warn").html("La dimensione massima per un'immagine é di 22MB! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUMIE":
      $("#warn").html("C'é stato un errore nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUUIE":
      $("#warn").html("Errore imprevisto nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine) <bold>Riferisci questo messaggio agli amministratori</bold>" + " Codice: " + err);
      break;
    case "IMGUNEN":
      $("#warn").html("La nota a cui stai cercando di allegare l'immagine non esiste!" + " Codice: " + err);
      break;
    default:
      $("#warn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
  }
  setTimeout(function(){$("#warn").hide();}, 10000);
}
