document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.gestion-horaires');
    if (!container) return;

    // Ecputeur pour le bouton "Enregistrer" dans chaque ligne du tableau
    container.addEventListener('click', async (e) => {
        const btn = e.target.closest('.btn-save-horaire');
        if (!btn) return;

        const row = btn.closest('tr');
        const jour = btn.dataset.jour;
        const heureOuverture = row.querySelector('.input-ouverture').value.trim();
        const heureFermeture = row.querySelector('.input-fermeture').value.trim();

        if (!heureOuverture || !heureFermeture) {
            alert('Veuillez renseigner les deux horaires.');
            return;
        }

        const payload = {
            jour: jour,
            heure_ouverture: heureOuverture,
            heure_fermeture: heureFermeture
        };

        btn.disabled = true;
       // Envoyer la requête AJAX pour mettre à jour les horaires
        try {
            const response = await fetch('index.php?page=update_horaire', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
         // Vérifier si la réponse est correcte
            if (!response.ok) {
                throw new Error(result.error || 'Erreur lors de la mise à jour.');
            }

            if (result.success) {
                btn.innerHTML = '<i class="bi bi-check-lg"></i> Enregistré';
                btn.classList.replace('btn-outline-primary', 'btn-outline-success');
                setTimeout(() => {
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Enregistrer';
                    btn.classList.replace('btn-outline-success', 'btn-outline-primary');
                }, 1500);
            }
        } catch (error) {
            console.error('Erreur update_horaire :', error);
            alert(error.message);
        } finally {
            btn.disabled = false;
        }
    });
});