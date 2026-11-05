// Barre de progression de l'envoi du justificatif. Pendant que le formulaire part, la page
// interroge progression.php, qui lit l'avancement que PHP enregistre dans la session.
const formulaire = document.querySelector('#nouvelle-demande');

formulaire.addEventListener('submit', async (evenement) => {
  const fichier = formulaire.querySelector('input[type=file]');
  if (fichier.files.length === 0) {
    return; // sans justificatif, envoi classique du formulaire
  }
  evenement.preventDefault();
  const cle = formulaire.querySelector('input[name=PHP_SESSION_UPLOAD_PROGRESS]').value;
  const barre = document.querySelector('#progression');
  const texte = document.querySelector('#progression-texte');
  barre.hidden = false;
  texte.textContent = 'Envoi en cours…';

  const suivi = setInterval(async () => {
    const reponse = await fetch('/progression.php?cle=' + encodeURIComponent(cle));
    const avancement = await reponse.json();
    if (avancement.suivi && avancement.total > 0) {
      barre.value = Math.round((100 * avancement.recu) / avancement.total);
      texte.textContent = `Envoi : ${barre.value} %`;
    }
  }, 250);

  const reponse = await fetch(formulaire.action, { method: 'POST', body: new FormData(formulaire) });
  clearInterval(suivi);
  window.location = reponse.url; // page d'arrivée après l'envoi (confirmation ou message d'erreur)
});
