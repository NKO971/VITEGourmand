document.addEventListener('DOMContentLoaded', () => {
    // 1. Récupération de tous nos éléments du DOM
    const inputCodePostal = document.getElementById('postal_code');
    const inputAdresse = document.getElementById('lieu_livraison');
    const listeSuggestions = document.getElementById('adresse_suggestions');
    const inputNbPersonnes = document.getElementById('nb_personnes');
    const formCommande = inputNbPersonnes.closest('form');
    
    const txtAffichageNbPers = document.getElementById('affichage_nb_personnes');
    const txtMenuTotal = document.getElementById('prix_menu_total');
    const ligneReduction = document.getElementById('ligne_reduction');
    const txtMontantReduction = document.getElementById('montant_reduction');
    const txtLivraison = document.getElementById('prix_livraison');
    const txtTotal = document.getElementById('prix_total');
    
    const prixMenuParPersonne = parseFloat(document.getElementById('prix_menu_hidden').value) || 0; 
    const minPersonnes = parseInt(document.getElementById('min_personnes_hidden').value) || 1;

    // Délai de commande minimum (structuré : valeur + unité heures/jours)
    const inputDatePrestation = document.getElementById('date_prestation');
    const elDelaiValeur = document.getElementById('delai_valeur_hidden');
    const elDelaiUnite = document.getElementById('delai_unite_hidden');
    const elDelaiInfo = document.getElementById('delai_commande_info');
    const delaiValeur = elDelaiValeur ? (parseInt(elDelaiValeur.value, 10) || 0) : 0;
    const delaiUnite = elDelaiUnite ? elDelaiUnite.value : '';

    // Calcule la date la plus proche autorisée (granularité jour : les heures
    // sont arrondies au jour supérieur) ou null si le menu n'a pas de délai.
    function calculerDateMinimum(valeur, unite) {
        if (!valeur || valeur <= 0) return null;
        const heuresTotales = unite === 'jours' ? valeur * 24 : valeur;
        const joursMinimum = Math.ceil(heuresTotales / 24);
        const d = new Date();
        d.setHours(0, 0, 0, 0);
        d.setDate(d.getDate() + joursMinimum);
        return d;
    }

    // Formate une date en chaîne "YYYY/MM/DD" pour l'affichage dans le message d'information.
    function formatDateLocale(d) {
        const annee = d.getFullYear();
        const mois = String(d.getMonth() + 1).padStart(2, '0');
        const jour = String(d.getDate()).padStart(2, '0');
        return `${annee}/${mois}/${jour}`;
    }

    const dateMinimumAutorisee = calculerDateMinimum(delaiValeur, delaiUnite);

    if (dateMinimumAutorisee && inputDatePrestation) {
        const iso = formatDateLocale(dateMinimumAutorisee);
        inputDatePrestation.min = iso;
        if (elDelaiInfo) {
            elDelaiInfo.textContent = `Ce menu doit être commandé au moins ${delaiValeur} ${delaiUnite} avant la prestation (date la plus proche possible : ${iso.split('-').reverse().join('/')}).`;
        }
    }

    let fraisLivraisonActuels = 0;
let zoneDesservie = true;
let debounceTimer = null;

function mettreAJourResume() {
    inputNbPersonnes.setCustomValidity('');
    const nbPersonnes = parseInt(inputNbPersonnes.value) || 1;

    txtAffichageNbPers.textContent = nbPersonnes;

    let prixTotalMenu = prixMenuParPersonne * nbPersonnes;

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

function verifierAdresse() {
    inputCodePostal.setCustomValidity('');
    const codePostal = inputCodePostal.value.trim();
    const adresse = inputAdresse.value.trim(); // <-- à remplacer par le vrai id, voir plus bas

    if (codePostal.length === 5 && adresse.length > 0) {
        fetch(`index.php?page=api_zone&adresse=${encodeURIComponent(adresse)}&code_postal=${encodeURIComponent(codePostal)}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    txtLivraison.textContent = data.message || "Zone non desservie";
                    txtLivraison.classList.add('text-danger');
                    fraisLivraisonActuels = 0;
                    zoneDesservie = false;
                    document.querySelector('button[type="submit"]').disabled = true;
                } else {
                    document.querySelector('button[type="submit"]').disabled = false;
                    txtLivraison.classList.remove('text-danger');
                    zoneDesservie = true;
                    fraisLivraisonActuels = data.gratuit ? 0 : (5 + (0.59 * data.distance_km));
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
        txtLivraison.textContent = "0.00 €";
        txtLivraison.classList.remove('text-danger');
        fraisLivraisonActuels = 0;
        mettreAJourResume();
    }
}

// Debounce : on attend 600ms après la dernière frappe avant d'appeler l'API,
// pour ne pas la spammer à chaque caractère tapé.
function declencherVerificationAdresse() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(verifierAdresse, 600);
}

let suggestionDebounceTimer = null;

function rechercherSuggestions() {
    const texte = inputAdresse.value.trim();

    if (texte.length < 3) {
        listeSuggestions.innerHTML = '';
        return;
    }

    fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(texte)}&limit=5&autocomplete=1`)
        .then(response => response.json())
        .then(data => {
            listeSuggestions.innerHTML = '';

            if (!data.features || data.features.length === 0) {
                return;
            }

            data.features.forEach(feature => {
                const item = document.createElement('li');
                item.classList.add('list-group-item', 'list-group-item-action');
                item.style.cursor = 'pointer';
                item.textContent = feature.properties.label;

                // Le mousedown plutôt : se déclenche avant le blur de l'input, sinon la liste se ferme avant que le clic soit pris en compte.
                item.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    inputAdresse.value = feature.properties.label;
                    if (feature.properties.postcode) {
                        inputCodePostal.value = feature.properties.postcode;
                    }
                    listeSuggestions.innerHTML = '';
                    declencherVerificationAdresse();
                });

                listeSuggestions.appendChild(item);
            });
        })
        .catch(error => {
            console.error('Erreur suggestions adresse:', error);
            listeSuggestions.innerHTML = '';
        });
}

function declencherRechercheSuggestions() {
    clearTimeout(suggestionDebounceTimer);
    suggestionDebounceTimer = setTimeout(rechercherSuggestions, 300);
}

function validerAvantEnvoi(e) {
    inputNbPersonnes.setCustomValidity('');
    inputCodePostal.setCustomValidity('');
    if (inputDatePrestation) {
        inputDatePrestation.setCustomValidity('');
    }

    const nbPersonnes = parseInt(inputNbPersonnes.value) || 0;

    if (nbPersonnes < minPersonnes) {
        e.preventDefault();
        inputNbPersonnes.setCustomValidity(`Le nombre de personnes minimum pour ce menu est de ${minPersonnes}.`);
        inputNbPersonnes.reportValidity();
        return;
    }

    if (!zoneDesservie) {
        e.preventDefault();
        inputCodePostal.setCustomValidity("Cette zone n'est pas desservie par nos services.");
        inputCodePostal.reportValidity();
        return;
    }

    if (dateMinimumAutorisee && inputDatePrestation && inputDatePrestation.value) {
        const datePresChoisie = new Date(inputDatePrestation.value + 'T00:00:00');
        if (datePresChoisie < dateMinimumAutorisee) {
            e.preventDefault();
            inputDatePrestation.setCustomValidity(`Ce menu doit être commandé au moins ${delaiValeur} ${delaiUnite} avant la prestation.`);
            inputDatePrestation.reportValidity();
            return;
        }
    }
}

   inputNbPersonnes.addEventListener('input', mettreAJourResume);
   inputCodePostal.addEventListener('input', declencherVerificationAdresse);
   
   inputAdresse.addEventListener('input', () => {
       declencherRechercheSuggestions();
       declencherVerificationAdresse();
    });

    inputAdresse.addEventListener('blur', () => {
        setTimeout(() => { listeSuggestions.innerHTML = ''; }, 150);
    });
   formCommande.addEventListener('submit', validerAvantEnvoi);

   if (inputDatePrestation) {
        inputDatePrestation.addEventListener('input', () => inputDatePrestation.setCustomValidity(''));
    }

    if (inputCodePostal.value.trim().length === 5 && inputAdresse.value.trim().length > 0) {
        verifierAdresse();
    } else {
        mettreAJourResume();
    }
});