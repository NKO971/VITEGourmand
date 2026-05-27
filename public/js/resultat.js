document.addEventListener('DOMContentLoaded', () => {
    // Utilisation des données réelles transmises par PHP/BDD, sinon un tableau vide
    const trajetsSource = window.trajetsDepuisBDD || [];

    // Constantes pour les éléments du DOM
    const formulaireFiltres = document.querySelector('.filtre');
    const conteneurTrajets = document.getElementById('liste-trajet');
    const barreRecherche = document.querySelector('.barreRecherche');
    const messageErreur = document.getElementById('message-erreur');
    const inputDepart = document.getElementById('lieu_depart');
    const inputArrivee = document.getElementById('lieu_arrivee');
    const inputDate = document.getElementById('date_depart');
    const nbVoyagesTrouves = document.getElementById('nb-voyages-trouve');
    const messageErreurDate = document.getElementById('alerte-trajet-proche');
    const plusRapide = document.getElementById('plus-rapide');
    const plusEcologique = document.getElementById('plus-ecologique');
    const prixMax = document.getElementById('prix-max');
    const dureeMax = document.getElementById('duree-max');
    const avant6H = document.getElementById('avant-6h');
    const entre6H12H = document.getElementById('entre-6h-12');
    const entre12H18H = document.getElementById('entre-12h-18h');
    const apres18H = document.getElementById('apres-18h');
    const profilVerifie = document.getElementById('profile-verifie');
    const notePlus3 = document.getElementById('note-superieur-a-3');

    // Formatage des heures pour l'affichage
    function formatHeure(minutes) {
        const heures = Math.floor(minutes / 60);
        const minutesRestantes = minutes % 60;
        const minutesFormatees = minutesRestantes.toString().padStart(2, '0');
        return `${heures}h${minutesFormatees}`;
    }

    // Fonction pour afficher les trajets
    function afficherTrajets(trajets) {
        conteneurTrajets.innerHTML = ''; 

        if (trajets.length === 0) {
            conteneurTrajets.innerHTML = '<p class="text-center">Aucun trajet trouvé.</p>';
            return;
        }

        trajets.forEach(trajet => {
            // Harmonisation des données PHP (gère les différences de clés entre les requêtes)
            const idReal = trajet.id || trajet.covoiturage_id || 0;
            const prixReal = trajet.prix || trajet.prix_personne || 0;
            const placesReal = trajet.passagers || trajet.nb_place || 0;
            const noteReal = trajet.note || trajet.note_chauffeur || 'N/A';
            const pseudoReal = trajet.conducteur || trajet.pseudo || trajet.pseudo_chauffeur || 'Anonyme';
            const departReal = trajet.depart || trajet.lieu_depart || 'Non renseigné';
            const arriveeReal = trajet.arrivee || trajet.lieu_arivee || trajet.lieu_arrivee || 'Non renseigné';
            const dateReal = trajet.date || trajet.date_depart || '--/--/----';
            
            // Gestion de l'heure (si chaîne "14:00:00", on prend les 5 premiers caractères, sinon formatHeure)
            const hDepartReal = isNaN(trajet.heureDepart || trajet.heure_depart) 
                ? (trajet.heureDepart || trajet.heure_depart || '--h--').substring(0, 5)
                : formatHeure(trajet.heureDepart || trajet.heure_depart);

            const hArriveeReal = isNaN(trajet.heureArrivee || trajet.heure_arrivee)
                ? (trajet.heureArrivee || trajet.heure_arrivee || '--h--').substring(0, 5)
                : formatHeure(trajet.heureArrivee || trajet.heure_arrivee);

            const card = `
            <div class="covoiturage"
                data-trajet-id="${idReal}"
                data-chauffeur-id="${trajet.chauffeurId || trajet.organisateur_id || ''}"
                data-prix-centimes="${prixReal * 100}"
                data-est-ecologique="${trajet.ecologique ? '1' : '0'}"
                data-nb-places="${placesReal}"
                data-note="${noteReal}">
                <div class="destinationHoraire">
                    <div class="trajet">
                        <span class="depart">${departReal}</span>
                        <span class="arrivee">${arriveeReal}</span>
                    </div>
                    <div class="heure_depart">${hDepartReal}</div>
                    <div class="FlechesH"><span class="material-symbols-outlined">line_end_arrow_notch</span></div>
                    <div class="heure_arrivee">${hArriveeReal}</div>
                    <div class="FlechesH"><span class="material-symbols-outlined">line_end_arrow_notch</span></div>
                    <div class="date_arrivee">${dateReal}</div>
                </div>
                <div class="ligneDeSeparation"><hr /></div>
                <div class="info-conducteur">
                    <div class="photo-pseudo col-12 col-md-auto d-flex flex-column flex-md-row align-items-center gap-2">
                        <img src="${trajet.photo ? trajet.photo : '/EcoRide/public/images/default.png'}" alt="${pseudoReal}" class="photoDeProfil">
                        <span class="pseudo">${pseudoReal}</span>
                        ${trajet.verifie ? '<span class="material-symbols-outlined text-success" title="Profil vérifié">verified</span>' : ''}
                    </div>
                    <div class="note">
                        <span class="material-symbols-outlined">star</span>
                        <span class="note-chauffeur">${noteReal}/5</span>
                    </div>
                    <div class="icon-energie">
                        <span class="material-symbols-outlined">${trajet.ecologique ? 'electric_car' : 'directions_car'}</span>
                        <small>${trajet.ecologique ? 'Écologique' : 'Thermique'}</small>
                    </div>
                    <div class="icon-credit">
                        <span class="material-symbols-outlined">payments</span>
                        <span><span class="js-credits">${prixReal}</span> Crédits</span>
                    </div>
                    <div class="icon-passager">
                        <span class="material-symbols-outlined">person</span>
                        <span><span class="js-places">${placesReal}</span> places</span>
                    </div>
                    <div class="action-btn col-12 col-md-auto">
                        <button class="btn-details btn-sm btn-outline-primary w-100 w-md-auto"
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDetailsTrajet-${idReal}"
                                data-covoiturage-id="${idReal}">Détails</button>
                    </div>
                </div>
            </div>`;
            conteneurTrajets.innerHTML += card;
        });
    }
    // TRAVAILLE SUR LES FILTRES ET LE TRI DYNAMIQUE
    function appliquerFiltres(event) {
        // Si c'est la barre de recherche qui est soumise, on laisse faire le rechargement PHP de la page
        if (event && event.type === 'submit' && event.target === barreRecherche) {
            return; 
        }

        console.log("Moteur de recherche : Filtrage dynamique des données BDD...");
        
        // Initialisation des messages
        if (messageErreurDate) messageErreurDate.classList.add('d-none');
        if (messageErreur) messageErreur.classList.add('d-none');
        
        // On part de la liste déjà pré-filtrée par le serveur SQL
        let trajetsFiltres = [...trajetsSource];

        // --- FILTRES AVANCÉS (LATÉRAUX) ---
        const unFiltreHoraireActif = avant6H && avant6H.checked || entre6H12H && entre6H12H.checked || entre12H18H && entre12H18H.checked || apres18H && apres18H.checked;
        const prixMaxValue = prixMax ? parseFloat(prixMax.value) : NaN;
        const dureeSaisie = dureeMax ? parseFloat(dureeMax.value) : NaN;

        // Tri par rapidité
        if (plusRapide && plusRapide.checked) {
            trajetsFiltres.sort((a, b) => (a.heureArrivee - a.heureDepart) - (b.heureArrivee - b.heureDepart));
        }

        // Filtre écologique
        if (plusEcologique && plusEcologique.checked) {
            trajetsFiltres = trajetsFiltres.filter(trajet => trajet.ecologique === true);
        }

        // Filtre prix max
        if (prixMax && prixMax.value) {
            trajetsFiltres = trajetsFiltres.filter(trajet => trajet.prix <= prixMaxValue);
        }

        // Filtre durée max
        if (dureeMax && dureeMax.value) {
            const minutesMaxSaisies = dureeSaisie * 60;
            trajetsFiltres = trajetsFiltres.filter(trajet => (trajet.heureArrivee - trajet.heureDepart) <= minutesMaxSaisies);
        }

        // Filtre note conducteur
        if (notePlus3 && notePlus3.checked) {
            trajetsFiltres = trajetsFiltres.filter(trajet => trajet.note > 3);
        }

        // Filtre horaires de départ
        if (unFiltreHoraireActif) {
            trajetsFiltres = trajetsFiltres.filter(trajet => {
                return (avant6H.checked && trajet.heureDepart < 360) ||
                    (entre6H12H.checked && trajet.heureDepart >= 360 && trajet.heureDepart < 720) ||
                    (entre12H18H.checked && trajet.heureDepart >= 720 && trajet.heureDepart < 1080) ||
                    (apres18H.checked && trajet.heureDepart >= 1080);
            });
        }

        // Filtre profil vérifié
        if (profilVerifie && profilVerifie.checked) {
            trajetsFiltres = trajetsFiltres.filter(trajet => trajet.verifie === true);
        }

        // Mise à jour du compteur et affichage final
        if (nbVoyagesTrouves) {
            nbVoyagesTrouves.textContent = trajetsFiltres.length;
        }
        afficherTrajets(trajetsFiltres);
    }

    // Écouteur pour la barre de recherche (soumission classique gérée par PHP)
    if (barreRecherche) {
        barreRecherche.addEventListener('submit', appliquerFiltres);
    }

    // Écouteur pour les filtres avancés (filtrage JS instantané à chaque clic/saisie)
    if (formulaireFiltres) {
        formulaireFiltres.addEventListener('input', appliquerFiltres);
    }

    // Premier affichage au chargement de la page
    appliquerFiltres();
});