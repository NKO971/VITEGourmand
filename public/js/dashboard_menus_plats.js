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

    // OUVERTURE MODAL MODIFICATION PLAT
    container.addEventListener('click', (e) => {
        const btnEditPlat = e.target.closest('.btn-edit-plat');
        if (!btnEditPlat) return;

        const row = btnEditPlat.closest('tr');
        const platId = btnEditPlat.dataset.id;
        const titrePlat = row.children[1].textContent.trim();

        document.getElementById('edit_plat_id').value = platId;
        document.getElementById('edit_titre_plat').value = titrePlat;
        document.getElementById('edit_photo_plat').value = '';

        const modalEl = document.getElementById('modalEditPlat');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    });

    // OUVERTURE MODAL MODIFICATION MENU
    container.addEventListener('click', (e) => {
        const btnEditMenu = e.target.closest('.btn-edit-menu');
        if (!btnEditMenu) return;

        const ds = btnEditMenu.dataset;

        document.getElementById('edit_menu_id').value = ds.id;
        document.getElementById('edit_menu_titre').value = ds.titre;
        document.getElementById('edit_menu_prix').value = ds.prix;
        document.getElementById('edit_menu_stock').value = ds.stock;
        document.getElementById('edit_menu_theme').value = ds.theme;
        document.getElementById('edit_menu_regime').value = ds.regime;

        // Décodage du JSON de composition
        let composition = {};
        try {
            composition = JSON.parse(ds.composition || '{}');
        } catch (err) {
            composition = {};
        }

        // Remise à zéro des sélecteurs
        document.getElementById('edit_menu_entree').value = '';
        document.getElementById('edit_menu_plat_principal').value = '';
        document.getElementById('edit_menu_dessert').value = '';

        // Pré-remplissage des sélecteurs
        if (composition && typeof composition === 'object') {
            if (composition.entree) setSelectByNomOrId('edit_menu_entree', composition.entree);
            if (composition.plat) setSelectByNomOrId('edit_menu_plat_principal', composition.plat);
            if (composition.dessert) setSelectByNomOrId('edit_menu_dessert', composition.dessert);
        }

        // Décodage des conditions de stockage
        let conditions = {};
        try {
            conditions = JSON.parse(ds.conditions || '{}');
        } catch (err) {
            conditions = {};
        }

        document.getElementById('edit_delai_commande').value = conditions.delai_commande || '';
        document.getElementById('edit_conservation').value = conditions.conservation || '';
        document.getElementById('edit_menu_description').value = ds.description || '';

        const modalEl = document.getElementById('modalEditMenu');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
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