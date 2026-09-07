document.addEventListener('DOMContentLoaded', () => {
    // Ciblage du conteneur spécifique défini dans la vue
    const container = document.querySelector('.moderation-menus-plats');
    if (!container) return;

    // Gesttion du toggle des statuts des menus et plats
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

    // Gestion de l'ouverture et du pré-remplissage de la modal pour l'édition d'un plat

    // Clic sur "Modifier" : ouverture et pré-remplissage de la modal
    container.addEventListener('click', (e) => {
        const btnEditPlat = e.target.closest('.btn-edit-plat');
        if (!btnEditPlat) return;

        const row = btnEditPlat.closest('tr');
        const platId = btnEditPlat.dataset.id;
        const titrePlat = row.children[1].textContent.trim();

        // Remplissage des champs de la modal
        document.getElementById('edit_plat_id').value = platId;
        document.getElementById('edit_titre_plat').value = titrePlat;

        // Reset de l'input fichier
        const photoInput = document.getElementById('edit_photo_plat');
        if (photoInput) photoInput.value = '';

        // Affichage de la modal Bootstrap
        const modalEl = document.getElementById('modalEditPlat');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    // Soumission du formulaire AJAX avec FormData
    const formEditPlat = document.getElementById('formEditPlat');
    if (formEditPlat) {
        formEditPlat.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnSave = document.getElementById('btnSavePlat');
            if (btnSave) btnSave.disabled = true;

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
                    // Fermeture de la modal
                    const modalEl = document.getElementById('modalEditPlat');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    // Mise à jour dynamique du titre dans le tableau HTML
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
                if (btnSave) btnSave.disabled = false;
            }
        });
    }
});