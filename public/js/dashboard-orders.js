// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', () => {
      // Sélection des éléments du DOM
    const searchInput = document.getElementById('filter-search');
    const statusSelect = document.getElementById('filter-status');
    const dateInput = document.getElementById('filter-date');
    const btnReset = document.getElementById('btn-reset');
    const ordersTbody = document.getElementById('orders-tbody');
    const ordersCount = document.getElementById('orders-count');

    // Fonction principale : Aller chercher les commandes via l'API
    async function loadOrders() {
        // Préparation des paramètres de recherche pour l'URL
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            status: statusSelect.value,
            date: dateInput.value
        });

        try {
            // Appels asynchrones vers l'API PHP
            const response = await fetch(`api/get_orders.php?${params.toString()}`);
            const data = await response.json();
            
            if (!response.ok || data.error) {
                throw new Error(data.error || 'Erreur lors du chregement des données');
            }
            
            // Appel de la fonction pour afficher les commandes dans le tableau
            renderOrdersTable(Array.isArray(data) ? data : (data.orders || []));

        } catch (error) {
            console.error('Erreur Fetch:', error);
            ordersTbody.innerHTML = `
                <tr>
                <td colspan="6" class="text-center text-danger py-4">
                    <i class="bi bi-exclamation-triangle me-2"></i>${escapeHtml(error.message)}
                </td>
            </tr>`;
        }
    }

    // Fonction d'affichage du tableau
    function renderOrdersTable(orders) {
        ordersCount.textContent = `${orders.length} commande(s)`;

        if (orders.length === 0) {
            ordersTbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        Aucune commande trouvée.
                    </td>
                </tr>`;
            return;
        }

        // Génération dynamique des lignes <tr>
        ordersTbody.innerHTML = orders.map(order => `
            <tr>
                <td class="fw-bold">#${order.commande_id}</td>
                <td>${escapeHtml(order.prenom)} ${escapeHtml(order.nom)}</td>
                <td>${new Date(order.date_commande).toLocaleString('fr-FR')}</td>
                <td>${parseFloat(order.montant_total || 0).toFixed(2)} €</td>
                <td>
                    <span class="badge ${getStatusBadgeClass(order.statut)}">
                        ${formatStatus(order.statut)}
                    </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" title="Voir détails">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Utilitaires de formatage
    function getStatusBadgeClass(status) {
        switch (status) {
            case 'en_attente': return 'bg-warning text-dark';
            case 'en_cours': return 'bg-info text-dark';
            case 'prete': return 'bg-primary';
            case 'livree': return 'bg-success';
            case 'annulee': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    function formatStatus(status) {
        return status ? status.replace('_', ' ').toUpperCase() : 'INCONNU';
    }

    function escapeHtml(str) {
        return str ? str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;") : '';
    }

    // Écoute des événements sur les filtres (Déclenchement automatique !)
    searchInput.addEventListener('input', loadOrders);
    statusSelect.addEventListener('change', loadOrders);
    dateInput.addEventListener('change', loadOrders);

    btnReset.addEventListener('click', () => {
        searchInput.value = '';
        statusSelect.value = '';
        dateInput.value = '';
        loadOrders();
    });

    // Chargement initial des données au chargement de la page
    loadOrders();
});

