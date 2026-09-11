document.addEventListener('DOMContentLoaded', () => {
    // 1. Récupération de tous nos éléments du DOM
    const inputCodePostal = document.getElementById('postal_code');
    const inputNbPersonnes = document.getElementById('nb_personnes');
    
    const txtAffichageNbPers = document.getElementById('affichage_nb_personnes');
    const txtMenuTotal = document.getElementById('prix_menu_total');
    const ligneReduction = document.getElementById('ligne_reduction');
    const txtMontantReduction = document.getElementById('montant_reduction');
    const txtLivraison = document.getElementById('prix_livraison');
    const txtTotal = document.getElementById('prix_total');
    
    const prixMenuParPersonne = parseFloat(document.getElementById('prix_menu_hidden').value) || 0; 
    const minPersonnes = parseInt(document.getElementById('min_personnes_hidden').value) || 1;

    let fraisLivraisonActuels = 0;

    function mettreAJourResume() {
        const nbPersonnes = parseInt(inputNbPersonnes.value) || 1;
        
        txtAffichageNbPers.textContent = nbPersonnes;

        let prixTotalMenu = prixMenuParPersonne * nbPersonnes;
        
        // Gestion de la réduction pour les commandes de 5 personnes ou plus
        if (nbPersonnes >= (minPersonnes + 5)) {
            const reduction = prixTotalMenu * 0.10; 
            prixTotalMenu = prixTotalMenu - reduction;
            
            txtMontantReduction.textContent = `- ${reduction.toFixed(2)} €`;
            ligneReduction.classList.remove('d-none');
        } else {
            ligneReduction.classList.add('d-none');
        }

        txtMenuTotal.textContent = (prixMenuParPersonne * nbPersonnes).toFixed(2) + " €";
        
        const prixTotalGeneral = prixTotalMenu + fraisLivraisonActuels;
        txtTotal.textContent = prixTotalGeneral.toFixed(2) + " €";
    }

    // On intéroge l'API à chaque changement du code postal pour mettre à jour les frais de livraison
    function verifierCodePostal() {
        const codePostal = inputCodePostal.value.trim();

        if (codePostal.length === 5) {
            fetch(`index.php?page=api_zone&code_postal=${codePostal}`)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        txtLivraison.textContent = "Zone non desservie";
                        txtLivraison.classList.add('text-danger');
                        fraisLivraisonActuels = 0;
                        document.querySelector('button[type="submit"]').disabled = true;
                    } else {
                        document.querySelector('button[type="submit"]').disabled = false;
                        txtLivraison.classList.remove('text-danger');
                        const distance = data.distance_km;
                        // Formule de Julie
                        fraisLivraisonActuels = (distance > 0) ? (5 + (0.59 * distance)) : 0;
                        txtLivraison.textContent = fraisLivraisonActuels.toFixed(2) + " €";
                    }
                    mettreAJourResume();
                })
                .catch(error => {
                    console.error('Erreur API:', error);
                    txtLivraison.textContent = "Erreur de calcul";
                    txtLivraison.classList.add('text-danger');
                    fraisLivraisonActuels = 0;
                    mettreAJourResume();
                });
        } else {
            // Si le code postal n'est pas complet (ex: en cours de saisie)
            txtLivraison.textContent = "0.00 €";
            txtLivraison.classList.remove('text-danger');
            fraisLivraisonActuels = 0;
            mettreAJourResume();
        }
    }

    inputNbPersonnes.addEventListener('input', mettreAJourResume);
    inputCodePostal.addEventListener('input', verifierCodePostal);

    if (inputCodePostal.value.trim().length === 5) {
        verifierCodePostal(); 
    } else {
        mettreAJourResume();
    }
});