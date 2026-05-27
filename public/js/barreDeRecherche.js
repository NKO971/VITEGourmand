document.addEventListener('DOMContentLoaded', () => {
    const barreRecherche = document.querySelector('.barreRecherche');
    const lieuDepartInput = document.getElementById('lieu_depart');
    const lieuArriveeInput = document.getElementById('lieu_arrivee');
    const dateDepartInput = document.getElementById('date_depart');
    const messageErreur = document.getElementById('message-erreur');

    // Écouteur pour cacher le message d'erreur dès la saisie
    if (lieuDepartInput) {
        lieuDepartInput.addEventListener('input', () => {
            messageErreur?.classList.add('d-none');
        });
    }

    if (lieuArriveeInput) {
        lieuArriveeInput.addEventListener('input', () => {
            messageErreur?.classList.add('d-none');
        });
    }

    if (dateDepartInput) {
        dateDepartInput.addEventListener('input', () => {
            messageErreur?.classList.add('d-none');
        });
    }

    // Logique de validation - Soumission du formulaire
    if (barreRecherche) {
        barreRecherche.addEventListener('submit', (event) => {
            // Vérification que tous les champs sont remplis
            if (lieuDepartInput.value.trim() === '' || 
                lieuArriveeInput.value.trim() === '' || 
                dateDepartInput.value.trim() === '') {
                
                event.preventDefault();
                messageErreur?.classList.remove('d-none');
                console.log("⚠️ Validation échouée : un ou plusieurs champs sont vides");
            } else {
                // Tous les champs sont remplis
                messageErreur?.classList.add('d-none');
                console.log("✅ Recherche validée - Envoi au serveur");
                // Le formulaire POSTe directement au serveur via l'attribut action
            }
        });
    }

});