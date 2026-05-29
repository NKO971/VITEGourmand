document.addEventListener("DOMContentLoaded", function () {
    const inputPrixMin = document.getElementById("prix-min");
    const inputPrixMax = document.getElementById("prix-max");
    const selectConvives = document.getElementById("filtre-convives");
    const selectRegime = document.getElementById("filtre-regime");
    const selectTheme = document.getElementById("filtre-theme");

    const listeCartes = document.querySelectorAll(".menu-item-card");

    function filtrerLesMenus() {
        // On récupère les valeurs sélectionnées par l'utilisateur
        const prixMin = parseFloat(inputPrixMin.value) || 0;
        const prixMax = parseFloat(inputPrixMax.value) || Infinity;
        const convivesSelectionnes = selectConvives.value;
        const regimeSelectionne = selectRegime.value;
        const themeSelectionne = selectTheme.value;

        // Le filtrage : on parcourt toutes les cartes et on décide de les afficher ou pas
        listeCartes.forEach(function (carte) {
            // On lit les attributs "data-" que PHP a écrit sur la carte
            const cartePrix = parseFloat(carte.getAttribute("data-prix"));
            const carteConvives = parseInt(carte.getAttribute("data-convives"));
            const carteRegime = carte.getAttribute("data-regime");
            const carteTheme = carte.getAttribute("data-theme");

            // Vérification du prix
            let correspondAuPrix = (cartePrix >= prixMin && cartePrix <= prixMax);
            let correspondAuxConvives = true;
            let correspondAuRegime = (regimeSelectionne === "tous" || carteRegime === regimeSelectionne);
            let correspondAuTheme = (themeSelectionne === "tous" || carteTheme === themeSelectionne);

            // Vérification du nombre de convives
            if (convivesSelectionnes !== "Tous") {
                if (convivesSelectionnes === "6") {
                    correspondAuxConvives = (carteConvives >= 6);
                } else {
                    correspondAuxConvives = (carteConvives === parseInt(convivesSelectionnes));
                }
            }

            // Décision : On affiche ou on cache la carte
            if (correspondAuPrix && correspondAuxConvives && correspondAuRegime && correspondAuTheme) {
                carte.style.display = "block";
            } else {
                carte.style.display = "none";
            }
        });
    }

    const boutonReinitialiser = document.getElementById("bouton-reinitialiser");

    boutonReinitialiser.addEventListener("click", function () {
    
    inputPrixMin.value = "";
    inputPrixMax.value = "";
    
    selectConvives.value = "Tous";
    selectRegime.value = "tous";
    selectTheme.value = "tous";

    filtrerLesMenus();
  });

    
    inputPrixMin.addEventListener("input", filtrerLesMenus);
    inputPrixMax.addEventListener("input", filtrerLesMenus);
    selectConvives.addEventListener("change", filtrerLesMenus);
    selectRegime.addEventListener("change", filtrerLesMenus);
    selectTheme.addEventListener("change", filtrerLesMenus);
});