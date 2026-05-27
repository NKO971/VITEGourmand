// Attendre que le DOM soit complètement chargé
document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // FRÉQUENTATION (TRAJETS)
    // ==========================================
    const canvasTrajets = document.getElementById('chart-trajets');
    
    if (canvasTrajets) {
        // Récupération des données depuis le HTML
        const labelsTrajets = JSON.parse(canvasTrajets.dataset.labels);
        const dataTrajets = JSON.parse(canvasTrajets.dataset.valeurs);

        new Chart(canvasTrajets.getContext('2d'), {
            type: 'line',
            data: {
                labels: labelsTrajets,
                datasets: [{
                    label: 'Nombre de trajets validés',
                    data: dataTrajets,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });
    }

    // ==========================================
    // REVENUS (CRÉDITS)
    // ==========================================
    const canvasCredits = document.getElementById('chart-credits');
    
    if (canvasCredits) {
        // Récupération des données depuis le HTML
        const labelsCredits = JSON.parse(canvasCredits.dataset.labels);
        const dataCredits = JSON.parse(canvasCredits.dataset.valeurs);

        new Chart(canvasCredits.getContext('2d'), {
            type: 'bar',
            data: {
                labels: labelsCredits,
                datasets: [{
                    label: 'Crédits gagnés',
                    data: dataCredits,
                    backgroundColor: '#198754',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 2 } }
                }
            }
        });
    }
});