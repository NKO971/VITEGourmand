document.addEventListener('DOMContentLoaded', () => {
    // Conteneur principal
    const container = document.querySelector('.moderation-menus-plats');
    if (!container) return;

    // Fonction utilitaire de sélection (ID puis Nom)
    function setSelectByNomOrId(selectId, itemData) {
        const select = document.getElementById(selectId);
        if (!select || !itemData) return;

        if (itemData.plat_id) {
            select.value = itemData.plat_id;
        } else if (itemData.nom) {
            const option = Array.from(select.options).find(opt => opt.dataset.nom === itemData.nom);
            if (option) select.value = option.value;
        }
    }

    // BASCULE D'ÉTAT (TOGGLE ACTIF / MASQUÉ)
    container.addEventListener('click', async (e) => {
        const btnMenu = e.target.closest('.btn-toggle-menu');
        const btnPlat = e.target.closest('.btn-toggle-plat');

        if (!btnMenu && !btnPlat) return;

        const button = btnMenu || btnPlat;
        const isMenu = !!btnMenu;

        const id = button.dataset.id;
        const targetStatus = parseInt(button.dataset.actif, 10);

        const route = isMenu ? 'toggle_menu_status' : 'toggle_plat_status';
        const payload = isMenu 
            ? { menu_id: id, actif: targetStatus } 
            : { plat_id: id, actif: targetStatus };

        button.disabled = true;

        try {
            const response = await fetch(`index.php?page=${route}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Erreur lors de la mise à jour.');
            }

            if (result.success) {
                const nextTargetStatus = targetStatus === 1 ? 0 : 1;
                button.dataset.actif = nextTargetStatus;

                if (targetStatus === 0) {
                    button.classList.replace('btn-outline-danger', 'btn-outline-success');
                    button.innerHTML = '<i class="bi bi-eye"></i> Activer';
                } else {
                    button.classList.replace('btn-outline-success', 'btn-outline-danger');
                    button.innerHTML = '<i class="bi bi-eye-slash"></i> Masquer';
                }

                const row = button.closest('tr');
                const badge = row ? row.querySelector('.badge-status') : null;
                if (badge) {
                    badge.className = `badge badge-status ${targetStatus === 1 ? 'bg-success' : 'bg-danger'}`;
                    badge.textContent = targetStatus === 1 ? 'Actif' : 'Masqué';
                }
            }
        } catch (error) {
            console.error('Erreur toggle :', error);
            alert(error.message);
        } finally {
            button.disabled = false;
        }
    });

    // OUVERTURE MODAL CRÉATION PLAT
const btnAddPlat = document.getElementById('btn-add-plat');
if (btnAddPlat) 
    {
    btnAddPlat.addEventListener('click', () => 
        {
        const modalEl = document.getElementById('modalCreatePlat');
        if (modalEl) 
            {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
            }
        });
    }

    // OUVERTURE MODAL MODIFICATION PLAT
    container.addEventListener('click', (e) => 
        {
        const btnEditPlat = e.target.closest('.btn-edit-plat');
        if (!btnEditPlat) return;

        const row = btnEditPlat.closest('tr');
        const platId = btnEditPlat.dataset.id;
        const titrePlat = row.children[1].textContent.trim();

        // Récupération de l'état actif depuis le bouton de toggle de la même ligne
        const btnToggle = row.querySelector('.btn-toggle-plat');
        const isActif = btnToggle ? btnToggle.dataset.actif === '1' : true;

        document.getElementById('edit_plat_id').value = platId;
        document.getElementById('edit_titre_plat').value = titrePlat;
        
        // Mise à jour du switch "Actif"
        const inputActif = document.getElementById('edit_plat_actif');
        if (inputActif) {
            inputActif.checked = isActif;
        }

        const modalEl = document.getElementById('modalEditPlat');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        });

    // OUVERTURE MODAL MODIFICATION MENU
// Fonction de corespondnace entre les noms et les IDs des plats pour la sélection dans les dropdowns
// Normalisation avancée (suppression des accents, apostrophes et espaces superflus)
const normalizeStr = (str) => 
    {
    if (!str) return '';
    return str
        .toString()
        .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // Supprime les accents (é -> e)
        .toLowerCase()
        .replace(/['’`]/g, "'")                          // Unifie les apostrophes
        .replace(/[^a-z0-9']/g, ' ')                      // Ne garde que caractères alfanumériques
        .replace(/\s+/g, ' ')                             // Condense les espaces
        .trim();
    };

const selectOptionInDropdown = (selectEl, itemData, fieldLabel) => {
    if (!selectEl || !itemData) return;

    // Affichage texte brut des options disponibles dans la console
    const availableOptions = Array.from(selectEl.options).map(o => ({
        value: o.value,
        text: o.text,
        dataNom: o.dataset.nom || ''
    }));
    console.log(`[${fieldLabel}] Options BDD dans le select :`, JSON.stringify(availableOptions));

    let targetId = typeof itemData === 'object' ? (itemData.plat_id || itemData.id) : null;
    let targetNom = typeof itemData === 'object' ? (itemData.nom || itemData.titre || itemData.titre_plat) : itemData;

    // Recherche par ID
    if (targetId) {
        const optById = Array.from(selectEl.options).find(o => o.value == targetId);
        if (optById) {
            selectEl.value = optById.value;
            console.log(`✅ [${fieldLabel}] Sélectionné par ID (${targetId})`);
            return;
        }
    }

    // Recherche par Nom avec tolérance d'accents et de correspondance partielle
    if (targetNom) {
        const cleanTarget = normalizeStr(targetNom);

        const optByNom = Array.from(selectEl.options).find(o => {
            if (!o.value) return false;

            const cleanDataNom = normalizeStr(o.dataset.nom);
            const cleanTextNom = normalizeStr(o.text);

            // Match si l'un contient l'autre ou s'ils sont égaux sans accents
            return (cleanDataNom && (cleanDataNom === cleanTarget || cleanDataNom.includes(cleanTarget) || cleanTarget.includes(cleanDataNom))) ||
                   (cleanTextNom && (cleanTextNom === cleanTarget || cleanTextNom.includes(cleanTarget) || cleanTarget.includes(cleanTextNom)));
        });

        if (optByNom) {
            selectEl.value = optByNom.value;
            console.log(`[${fieldLabel}] Sélectionné par Nom : "${targetNom}" -> Option ID ${optByNom.value}`);
        } else {
            console.warn(`[${fieldLabel}] Aucun plat trouvé pour : "${targetNom}" (clean: "${cleanTarget}")`);
        }
    }
};

// Gestion de l'ouverture du modal d'édition de menu
document.addEventListener('click', (e) => {
    const btnEditMenu = e.target.closest('.btn-edit-menu');
    if (!btnEditMenu) return;

    const ds = btnEditMenu.dataset;

    // --- CONTRÔLE CONSOLE ---
    console.log("JSON Composition reçu du dataset :", ds.composition);

    // Pré-remplissage des champs simples
    document.getElementById('edit_menu_id').value = ds.id || '';
    document.getElementById('edit_menu_titre').value = ds.titre || '';
    document.getElementById('edit_menu_description').value = ds.description || '';
    document.getElementById('edit_menu_prix').value = ds.prix || 0;
    document.getElementById('edit_menu_stock').value = ds.stock || 0;
    document.getElementById('edit_menu_theme').value = ds.theme || '';
    document.getElementById('edit_menu_regime').value = ds.regime || '';

    // Traitement du JSON de composition
    let composition = {};
    try {
        let rawComp = ds.composition || '{}';
        if (typeof rawComp === 'string') {
            composition = JSON.parse(rawComp);
            if (typeof composition === 'string') composition = JSON.parse(composition); // Sécurité double encodage
        } else {
            composition = rawComp;
        }
    } catch (err) {
        console.error('Erreur de parsing de la composition :', err);
        composition = {};
    }

    // Réinitialisation des sélecteurs
    const selectEntree = document.getElementById('edit_menu_entree');
    const selectPlat = document.getElementById('edit_menu_plat_principal');
    const selectDessert = document.getElementById('edit_menu_dessert');

    if (selectEntree) selectEntree.value = '';
    if (selectPlat) selectPlat.value = '';
    if (selectDessert) selectDessert.value = '';

    // Application de la sélection avec les libellés pour les logs console
    if (composition && typeof composition === 'object') {
    if (composition.entree) selectOptionInDropdown(selectEntree, composition.entree, 'Entrée');
    if (composition.plat) selectOptionInDropdown(selectPlat, composition.plat, 'Plat');
    if (composition.dessert) selectOptionInDropdown(selectDessert, composition.dessert, 'Dessert');
    }

    // Conditions de stockage & livraison
    let conditions = {};
    try {
        conditions = typeof ds.conditions === 'string' ? JSON.parse(ds.conditions) : (ds.conditions || {});
    } catch (err) {
        conditions = {};
    }
    document.getElementById('edit_delai_commande').value = conditions.delai_commande || '';
    document.getElementById('edit_conservation').value = conditions.conservation || '';

    // Ouverture de la modal Bootstrap
    const modalEl = document.getElementById('modalEditMenu');
    bootstrap.Modal.getOrCreateInstance(modalEl).show();
});

    // SOUMISSION FORMULAIRE PLAT (FormData)
    const formEditPlat = document.getElementById('formEditPlat');
    if (formEditPlat) {
        formEditPlat.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnSave = document.getElementById('btnSavePlat');
            btnSave.disabled = true;

            const formData = new FormData(formEditPlat);

            try {
                const response = await fetch('index.php?page=update_plat', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Erreur lors de la mise à jour.');
                }

                if (result.success) {
                    const modalEl = document.getElementById('modalEditPlat');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    const platId = formData.get('plat_id');
                    const btnInRow = document.querySelector(`.btn-edit-plat[data-id="${platId}"]`);
                    if (btnInRow) {
                        const row = btnInRow.closest('tr');
                        row.children[1].textContent = formData.get('titre_plat');
                    }

                    alert(result.message);
                }
            } catch (error) {
                console.error('Erreur update_plat :', error);
                alert(error.message);
            } finally {
                btnSave.disabled = false;
            }
        });
    }

    // SOUMISSION FORMULAIRE MENU (JSON)
    const formEditMenu = document.getElementById('formEditMenu');
    if (formEditMenu) {
        formEditMenu.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnSave = document.getElementById('btnSaveMenu');
            btnSave.disabled = true;

            const getPlatData = (selectId) => {
                const select = document.getElementById(selectId);
                if (!select || !select.value) return null;
                const selectedOption = select.options[select.selectedIndex];
                return {
                    plat_id: parseInt(select.value, 10),
                    nom: selectedOption.dataset.nom || selectedOption.text
                };
            };

            const compositionObj = {};
            const entreeData = getPlatData('edit_menu_entree');
            if (entreeData) compositionObj.entree = entreeData;

            const platData = getPlatData('edit_menu_plat_principal');
            if (platData) compositionObj.plat = platData;

            const dessertData = getPlatData('edit_menu_dessert');
            if (dessertData) compositionObj.dessert = dessertData;

            const conditionsObj = {
                delai_commande: document.getElementById('edit_delai_commande').value.trim(),
                conservation: document.getElementById('edit_conservation').value.trim()
            };

            // Nettoyage et conversion du prix (gestion de la virgule)
            const rawPrix = document.getElementById('edit_menu_prix').value.toString().replace(',', '.').trim();
            const prixFormate = parseFloat(rawPrix);

            const payload = {
                menu_id: document.getElementById('edit_menu_id').value,
                titre: document.getElementById('edit_menu_titre').value.trim(),
                prix: isNaN(prixFormate) ? 0 : prixFormate, // Sécurité si le champ est vide
                stock: parseInt(document.getElementById('edit_menu_stock').value, 10) || 0,
                theme_id: document.getElementById('edit_menu_theme').value,
                regime_id: document.getElementById('edit_menu_regime').value,
                description: document.getElementById('edit_menu_description').value,
                composition: JSON.stringify(compositionObj),
                conditions_stockage: JSON.stringify(conditionsObj)
            };

            try { console.log("Payload envoyé :", payload);
                const response = await fetch('index.php?page=update_menu', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Erreur lors de la mise à jour du menu.');
                }

                if (result.success) {
                    const modalEl = document.getElementById('modalEditMenu');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur update_menu :', error);
                alert(error.message);
            } finally {
                btnSave.disabled = false;
            }
        });
    }
});