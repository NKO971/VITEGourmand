// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', () => {

    // --- SÉLECTION DES ÉLÉMENTS DU DOM ---

    // Sélection des bouton radio pour le choix du rôle
    const radioRoles = document.querySelectorAll('input[name="role_preference"]');
    const blocChauffeur = document.querySelector('.conteneur-chauffeur-flex');

    // Sélection des éléments pour le calcul de la commission
    const inputPrix = document.getElementById('prix_personne');
    const texteCommission = document.querySelector('.commission');

    // Formulaire de trajet complet pour cibler la soumission
    const formTrajet = document.getElementById('form-trajet'); 

    // Sélection du bouton pour ajouter un véhicule
    const btnAjouterVehicule = document.querySelector('.btn-outline-primary');
    const sectionVehicule = document.getElementById('section_vehicule');
    let vehiculeCount = 1; // Compteur de véhicules

    // Sélection des éléments de l'historique des trajets
    const tabAvenir = document.getElementById('tab-avenir');
    const tabHistorique = document.getElementById('tab-historique');

    // NOTE: L'authentification et les données utilisateur sont gérées par le serveur PHP
    // Les formulaires envoient directement les données au serveur via l'attribut action
    
    // --- SÉLECTION PROFIL ---
    const btnActionProfil = document.getElementById('btn-action-profil');
    const formProfil = document.getElementById('form-profil');

    // --- LOGIQUE D'AFFICHAGE EN FONCTION DES ROLES ---

    // On récupère le conteneur du bouton de recherche rapide
    const zoneActionPassager = document.querySelector('.action-passager');

  function affichageEnFonctionDesRoles(valeurRole) {
    // Gestion du bloc Chauffeur (on vérifie s'il existe avant d'agir, sans bloquer le reste)
    if (blocChauffeur) {
        if (valeurRole === 'chauffeur' || valeurRole === 'les_deux') {
            blocChauffeur.style.display = 'flex';
        } else {
            blocChauffeur.style.display = 'none';
        }
    }

    // Gestion du bouton de recherche Passager
    if (zoneActionPassager) {
        if (valeurRole === 'passager' || valeurRole === 'les_deux') {
            zoneActionPassager.style.display = 'block';
        } else {
            zoneActionPassager.style.display = 'none';
        }
    }
}

// Initialisation de l'affichage selon le rôle sélectionné au chargement
const roleSelectionne = document.querySelector('input[name="role_preference"]:checked');
if (roleSelectionne) {
    affichageEnFonctionDesRoles(roleSelectionne.value);
} else {
    if (blocChauffeur) blocChauffeur.style.display = 'none';
    if (zoneActionPassager) zoneActionPassager.style.display = 'none';
}

// Ajout des écouteurs de changement sur les radios
if (radioRoles.length > 0) {
    radioRoles.forEach(radio => {
        radio.addEventListener('change', () => {
            const valeurRole = radio.value;

            // 1. On change le visuel sur l'écran
            affichageEnFonctionDesRoles(valeurRole);

            // 2. ENVOI DE L'INFO À PHP (Le pont manquant)
            fetch('?page=profile', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=update_role_preference&role_preference=${valeurRole}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("✅ Rôle enregistré en BDD :", valeurRole);
                } else {
                    console.error("❌ Erreur PHP :", data.message);
                }
            })
            .catch(error => console.error("⚠️ Erreur réseau :", error));
        });
    });
}

    // --- LOGIQUE DE CALCUL DE LA COMMISSION ---

    const frais = 2;

    function calculCommission(valeurNumerique) {
        // Sécurité : vérifier que les éléments existent
        if (!texteCommission || !inputPrix) return false;

        if (valeurNumerique > frais) {
            const gainsUtilisateur = valeurNumerique - frais;
            texteCommission.textContent = `Après une commission de ${frais} crédits, vous gagnez ${gainsUtilisateur.toFixed(2)} crédits par passager.`;
            texteCommission.style.color = "var(--color-primary)";
            inputPrix.style.border = "1px solid var(--color-light)";
            return true;
        } else if (valeurNumerique > 0 && valeurNumerique <= frais) {
            texteCommission.textContent = `Le prix doit être supérieur à ${frais} crédits pour couvrir les frais de commission.`;
            texteCommission.style.color = "red";
            inputPrix.style.border = "2px solid red";
            return false;
        } else {
            texteCommission.textContent = "EcoRide prélèvera 2 crédits de commission par passager.";
            texteCommission.style.color = "var(--color-dark)";
            inputPrix.style.border = "1px solid var(--color-light)";
            return false;
        }
    }

    // Saisie du prix en temps réel (sécurité : vérifier existence)
    if (inputPrix && texteCommission) {
        inputPrix.addEventListener('input', () => {
            const prixSaisi = parseFloat(inputPrix.value) || 0;
            calculCommission(prixSaisi);
        });
    }

    // --- VALIDATION STRICTE DU FORMULAIRE DE TRAJET ---
    // On écoute l'événement 'submit' du formulaire de trajet uniquement pour ne pas bloquer les véhicules
   // --- VALIDATION STRICTE DU FORMULAIRE DE TRAJET ---
    if (formTrajet && inputPrix) {
        formTrajet.addEventListener('submit', (event) => {
            console.log("🚀 Tentative de soumission du formulaire de trajet...");

            const prixFinal = parseFloat(inputPrix.value) || 0;

            // 1. Vérification du prix
            if (calculCommission(prixFinal) === false) {
                event.preventDefault();
                console.warn("❌ Soumission bloquée : Le prix est inférieur aux frais de commission.");
                alert("Action impossible : veuillez choisir un prix supérieur aux frais de commission (2 crédits).");
                inputPrix.style.border = "2px solid red";
                return;
            }

            // 2. Vérification du véhicule
            const selectVehiculeTrajet = formTrajet.querySelector('#vehicule_id');
            if (!selectVehiculeTrajet || selectVehiculeTrajet.value === "") {
                event.preventDefault();
                console.warn("❌ Soumission bloquée : Aucun véhicule sélectionné.");
                alert("Veuillez sélectionner un véhicule valide avant de publier votre trajet.");
                return;
            }

            console.log("✅ Formulaire Trajet validé à 100% - Envoi au serveur PHP !");
        });
    }

    // --- LOGIQUE D'AJOUT DE FORMULAIRE VÉHICULE ---

    if (btnAjouterVehicule) {
        btnAjouterVehicule.addEventListener('click', () => {
            const formOriginal = document.querySelector('.infos-vehicule');

            if (!formOriginal) {
                console.warn("⚠️ Formulaire original introuvable");
                return;
            }

            // Clone du formulaire
            const nouveauFormVehicule = formOriginal.cloneNode(true);
            vehiculeCount++;

            // Changer le titre (Legend)
            const legend = nouveauFormVehicule.querySelector('legend');
            if (legend) legend.textContent = `Mon Véhicule ${vehiculeCount}`;

            // Nettoyer et renommer les IDs pour éviter les doublons
            const inputs = nouveauFormVehicule.querySelectorAll('input, select');
            inputs.forEach(input => {
                const originalId = input.getAttribute('id');
                const name = input.getAttribute('name');

                // Vider le champ
                input.value = "";
                input.style.border = "";

                // Générer un nouvel ID unique (ex: immatriculation_2, marque_id_2)
                if (originalId) {
                    const newId = `${originalId}_${vehiculeCount}`;
                    input.setAttribute('id', newId);
                }
            });

            // Mettre à jour les labels pour pointer vers les nouveaux IDs
            const labels = nouveauFormVehicule.querySelectorAll('label');
            labels.forEach(label => {
                const forAttribute = label.getAttribute('for');
                if (forAttribute) {
                    const newForAttribute = `${forAttribute}_${vehiculeCount}`;
                    label.setAttribute('for', newForAttribute);
                }
            });

            // Insérer le clone avant le bouton
            formOriginal.parentNode.insertBefore(nouveauFormVehicule, btnAjouterVehicule);

            // Focus sur le premier input du nouveau formulaire
            const premierInput = nouveauFormVehicule.querySelector('input');
            if (premierInput) {
                premierInput.focus();
                console.log(`✅ Véhicule ${vehiculeCount} ajouté avec succès`);
            }
        });
    }

    // --- LOGIQUE DE GESTION DES ONGLETS (À VENIR / HISTORIQUE) ---

    function updateTabStyles(activeTab, inactiveTab) {
        if (!activeTab || !inactiveTab) return; // Sécurité

        activeTab.style.backgroundColor = "transparent";
        inactiveTab.style.backgroundColor = "transparent";

        // Styles pour l'onglet actif
        activeTab.classList.add('text-primary');
        activeTab.classList.remove('text-muted', 'opacity-50');

        // Styles pour l'onglet inactif
        inactiveTab.classList.add('text-muted', 'opacity-50');
        inactiveTab.classList.remove('text-primary');
    }

    // Écouteur sur l'onglet "À venir"
    if (tabAvenir && tabHistorique) {
        tabAvenir.addEventListener('click', () => {
            updateTabStyles(tabAvenir, tabHistorique);
        });
    }

    // Écouteur sur l'onglet "Historique"
    if (tabHistorique && tabAvenir) {
        tabHistorique.addEventListener('click', () => {
            updateTabStyles(tabHistorique, tabAvenir);
        });
    }

    // --- LOGIQUE DE GESTION DU MODE ÉDITION DU PROFIL ---

    if (btnActionProfil && formProfil) {
        btnActionProfil.addEventListener('click', function(event) {
            const inputs = formProfil.querySelectorAll('input:not([type="hidden"]), select, textarea');
            
            if (inputs.length > 0) {
                const isReadOnly = inputs[0].hasAttribute('disabled');

                if (isReadOnly) {
                    // Empêche la soumission PHP au tout premier clic de déblocage
                    event.preventDefault(); 
                    
                    // PASSER EN MODE ÉDITION
                    inputs.forEach(input => input.removeAttribute('disabled'));
                    
                    // Mettre à jour le bouton
                    this.textContent = 'Enregistrer mon profil';
                    this.setAttribute('type', 'submit');
                }
            }
        });
    }
});