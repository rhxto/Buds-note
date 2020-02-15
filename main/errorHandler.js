function error(err) {
  $(".localWarn").show();
  switch (err) {
    case "sessione":
      $("#localWarn").html("Errore nel logout, se hai visto questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
    case "IES":
      $("#localWarn").html("Abbiamo riscontrato un errore nella ricerca, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "man":
      $("#localWarn").html("Il server é in manutenzione, certe funzionalità potrebbero essere bloccate.");
      break;
    case "IEMAN":
      $("#localWarn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANS":
      $("#localWarn").html("Errore nell'impostazione della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "IEMANR":
      $("#localWarn").html("Errore nella lettura dello stato della manutenzione, controlla il log degli errori." + " Codice: " + err);
      break;
    case "NOMAN":
      $("#localWarn").html("Non sei autorizzato a modificare lo stato della manutenzione. Questo incidente é stato segnalato.");
      break;
    case "MANAA":
      $("#localWarn").html("Manutenzione giá attiva!" + " Codice: " + err);
      break;
    case "MANAT":
      $("#localWarn").html("Manutenzione giá terminata!" + " Codice: " + err);
      break;
    case "NOTENV":
      $("#localWarn").html("Nota non valida!" + " Codice: " + err);
      break;
    case "NOTEANV":
      $("#localWarn").html("Tipo di azione non valido (se vedi questo messaggio riferiscilo agli amministratori)!" + " Codice: " + err);
      break;
    case "NOTENL":
      $("#localWarn").html("Devi eseguire il login per scrivere una nota!");
      break;
    case "NOTEW":
      $("#localWarn").html("Errore nella scrittura della nota, se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "NOTEWYNV":
      $("#localWarn").html("Anno non valido! Codice: " + err);
      break;
    case "NOTEDNA":
      $("#localWarn").html("Non sei autorizzato a cancellare le note, questo incidente é stato segnalato");
      break;
    case "NOTEDE":
      $("#localWarn").html("C'é stato un errore nella rimozione della nota, controlla il log degli errori.");
      break;
    case "NOTESC":
      $("#localWarn").html("Non si possono usare caratteri speciali in una nota! (. e / non supportati)");
      break;
    case "NOTESYNV":
      $("#localWarn").html("Anno della ricerca non valido! Codice: " + err);
      break;
    case "NOTEDNF":
      $("#localWarn").html("Nota non trovata!");
      break;
    case "NOTEUNV":
      $("#localWarn").html("Testo della nota non valido");
      break;
    case "NOTEUNA":
      $("#localWarn").html("Non sei autorizzato a modificare questa nota, l'incidendte é stato segnalato");
      break;
    case "NOTEUNE":
      $("#localWarn").html("La nota che volevi aggiornare non é stata trovata, copia le modifiche e prova a ricaricare la pagina. Se il problema persiste contatta gli amministratori.");
      break;
    case "NOTEWNFAW":
      $("#localWarn").html("Abbiamo riscontrato un errore nella scrittura della nota. Codice: " + err);
      break;
    case "FIREFOX":
      $("#localWarn").html("A causa di errori nel broswer, alcuni elementi del sito potrebbero non funzionare correttamente in Firefox (consigliamo Chrome o Edge). Vedi: https://support.mozilla.org/en-US/questions/1191898 <button class='mozErrorDeactivation' onclick='mozShown()'>Ok</button>");
      break;
    case "NOTEWAE":
      $("#localWarn").html("Nota giá esistente");
      break;
    case "IEAG":
      $("#localWarn").html("Errore nel retrieve di acc**** se vedi questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
      break;
    case "USERANV":
      $("#localWarn").html("Azione non valida!" + " Codice: " + err);
      break;
    case "USERNV":
      $("#localWarn").html("Parametro per l'azione sull'utente non valido! (Attenzione ai caratteri speciali)" + " Codice: " + err);
      break;
    case "USERNL":
      $("#localWarn").html("Prima devi eseguire il login!" + " Codice: " + err);
      break;
    case "USERSNV":
    $("#localWarn").html("Parametro per ricerca dell'utente non valido! (Attenzione ai caratteri speciali)" + " Codice: " + err);
      break;
    case "USERSIE":
      $("#localWarn").html("Abbiamo riscontrato un errore interno nella ricerca dell'utente, riferisci questo messaggio agli amministratori." + " Codice: " + err);
      break;
    case "IMGUAE":
      //finché non si potranno modificare le immagini relative alla nota, questo errore non dovrebbe apparire
      $("#localWarn").html("Hai giá caricato un'immagine relativa alla nota con lo stesso nome!" + " Codice: " + err);
      break;
    case "IMGUIE":
      $("#localWarn").html("Abbiamo riscontrato un errore interno nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUNL":
      $("#localWarn").html("Prima devi eseguire il login!" + " Codice: " + err);
      break;
    case "IMGUUE":
      $("#localWarn").html("Errore sconosciuto nel caricamento dell'immagine! <bold>Riferisci questo messaggio agli amministratori</bold> (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUVNV":
      $("#localWarn").html("Valori non validi per il caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUFNS":
      $("#localWarn").html("Questo tipo di immagine non é supportato! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUFNI":
      $("#localWarn").html("Il file che stai cercando di caricare non é un'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + "Codice: " + err);
      break;
    case "IMGUFTB":
      $("#localWarn").html("La dimensione massima per un'immagine é di 22MB! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUMIE":
      $("#localWarn").html("C'é stato un errore nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine)" + " Codice: " + err);
      break;
    case "IMGUUIE":
      $("#localWarn").html("Errore imprevisto nel caricamento dell'immagine! (Tuttavia la nota é stata pubblicata senza immagine) <bold>Riferisci questo messaggio agli amministratori</bold>" + " Codice: " + err);
      break;
    case "IMGUNEN":
      $("#localWarn").html("La nota a cui stai cercando di allegare l'immagine non esiste!" + " Codice: " + err);
      break;
    default:
      $("#localWarn").html("Abbiamo riscontrato un errore, se stai vedendo questo messaggio riferiscilo agli amministratori." + " Codice: " + err);
    break;
  }
  //setTimeout(function(){$("#localWarn").hide();}, 10000);
}
