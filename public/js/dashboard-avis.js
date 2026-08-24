document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.moderation-avis');
    if (!container) return;

    container.addEventListener('click', async (e) => {
        const btnValider = e.target.closest('.btn-valider');
        const btnRefuser = e.target.closest('.btn-refuser');

        if (!btnValider && !btnRefuser) return;

        const button = btnValider || btnRefuser;
        const id = button.dataset.id;
        const statut = btnValider ? 'valide' : 'refuse';

        button.disabled = true;

        try {
            const response = await fetch('index.php?page=update_avis_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, statut: statut })
            });

            const data = await response.json();

            if (data.success) {
                const card = document.getElementById(`avis-${id}`);
                if (card) {
                    card.style.transition = 'opacity 0.3s ease';
                    card.style.opacity = '0';
                    setTimeout(() => {
                        card.remove();
                        const remaining = container.querySelectorAll('.col-md-4');
                        if (remaining.length === 0) {
                            const row = container.querySelector('.row');
                            if (row) {
                                row.innerHTML = '<div class="col-12"><p class="alert alert-info">Aucun avis en attente de validation.</p></div>';
                            }
                        }
                    }, 300);
                }
            } else {
                alert('Erreur : ' + (data.message || 'Mise à jour impossible'));
                button.disabled = false;
            }
        } catch (err) {
            console.error('Erreur AJAX :', err);
            alert('Une erreur réseau est survenue.');
            button.disabled = false;
        }
    });
});