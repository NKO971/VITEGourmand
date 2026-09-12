document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.gestion-employes');
    if (!container) return;

    // OUVERTURE MODAL CRÉATION EMPLOYÉ
    const btnAddEmploye = document.getElementById('btn-add-employe');
    if (btnAddEmploye) {
        btnAddEmploye.addEventListener('click', () => {
            const modalEl = document.getElementById('modalCreateEmploye');
            if (modalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();
            }
        });
    }

    // SOUMISSION FORMULAIRE CRÉATION EMPLOYÉ
    const formCreateEmploye = document.getElementById('formCreateEmploye');
    if (formCreateEmploye) {
        formCreateEmploye.addEventListener('submit', async (e) => {
            e.preventDefault();

            const btnSave = document.getElementById('btnCreateEmploye');
            btnSave.disabled = true;

            const payload = {
                email: document.getElementById('create_employe_email').value.trim(),
                password: document.getElementById('create_employe_password').value
            };

            try {
                const response = await fetch('index.php?page=create_employe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.error || 'Erreur lors de la création du compte.');
                }

                if (result.success) {
                    const modalEl = document.getElementById('modalCreateEmploye');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();

                    window.location.reload();
                }
            } catch (error) {
                console.error('Erreur create_employe :', error);
                alert(error.message);
            } finally {
                btnSave.disabled = false;
            }
        });
    }

    // BASCULE ACTIF / DÉSACTIVÉ
    container.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-toggle-employe');
        if (!btn) return;

        const userId = btn.dataset.id;
        const targetActive = parseInt(btn.dataset.active, 10);

        btn.disabled = true;

        try {
            const response = await fetch('index.php?page=toggle_user_active', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ user_id: userId, is_active: targetActive })
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.error || 'Erreur lors de la mise à jour.');
            }

            if (result.success) {
                const nextTarget = targetActive === 1 ? 0 : 1;
                btn.dataset.active = nextTarget;

                if (targetActive === 1) {
                    btn.classList.replace('btn-outline-success', 'btn-outline-danger');
                    btn.innerHTML = '<i class="bi bi-x-circle"></i> Désactiver';
                } else {
                    btn.classList.replace('btn-outline-danger', 'btn-outline-success');
                    btn.innerHTML = '<i class="bi bi-check-circle"></i> Activer';
                }

                const row = btn.closest('tr');
                const badge = row ? row.querySelector('.badge-status') : null;
                if (badge) {
                        badge.className = `badge badge-status ${targetActive === 1 ? 'bg-success' : 'bg-danger'}`;
                         badge.textContent = targetActive === 1 ? 'Actif' : 'Désactivé';
                        }
            }
        } catch (error) {
            console.error('Erreur toggle employé :', error);
            alert(error.message);
        } finally {
            btn.disabled = false;
        }
    });
});