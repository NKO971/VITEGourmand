document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.admin-dashboard');
    if (!container) return;

    // GRAPHIQUE : Nombre de commandes par menu
    const canvasEl = document.getElementById('chartCommandesParMenu');
    if (canvasEl && typeof commandesParMenuData !== 'undefined' && commandesParMenuData.length > 0) {
        const labels = commandesParMenuData.map(item => item.titre);
        const valeurs = commandesParMenuData.map(item => item.nombre);

        new Chart(canvasEl, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Commandes',
                    data: valeurs,
                    backgroundColor: '#0d6efd'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    }

    // CHIFFRE D'AFFAIRES FILTRABLE
    const caTotal = document.getElementById('ca-total');
    const btnFiltrer = document.getElementById('btn-appliquer-filtres-ca');

    async function chargerChiffreAffaires() {
        const menuId = document.getElementById('filtre_ca_menu').value;
        const dateDebut = document.getElementById('filtre_ca_date_debut').value;
        const dateFin = document.getElementById('filtre_ca_date_fin').value;

        const params = new URLSearchParams();
        if (menuId) params.append('menu_id', menuId);
        if (dateDebut) params.append('date_debut', dateDebut);
        if (dateFin) params.append('date_fin', dateFin);

        caTotal.textContent = '...';

        try {
            const response = await fetch(`index.php?page=get_chiffre_affaires&${params.toString()}`);
            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.error || 'Erreur lors du calcul du chiffre d\'affaires.');
            }

            caTotal.textContent = result.total.toFixed(2) + ' €';
        } catch (error) {
            console.error('Erreur CA :', error);
            caTotal.textContent = 'Erreur';
        }
    }

    if (btnFiltrer) {
        btnFiltrer.addEventListener('click', chargerChiffreAffaires);
    }

    // Chargement initial (tous menus confondus, sans filtre de date)
    chargerChiffreAffaires();
});