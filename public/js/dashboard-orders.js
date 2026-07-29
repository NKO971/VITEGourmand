// Attendre que le DOM soit complètement chargé
document.addEventListener('DOMContentLoaded', () => {

    // Sélection des éléments du DOM
    const searchInput  = document.getElementById('filter-search');
    const statusSelect = document.getElementById('filter-status');
    const dateInput    = document.getElementById('filter-date');
    const btnReset     = document.getElementById('btn-reset');
    const ordersTbody  = document.getElementById('orders-tbody');
    const ordersCount  = document.getElementById('orders-count');

    let searchTimeout = null;

    /**
     * Fonction principale : Charger les commandes depuis l'API
     */
    async function loadOrders() {
        const params = new URLSearchParams({
            search: searchInput.value.trim(),
            status: statusSelect.value,
            date: dateInput.value
        });

        try {
            const response = await fetch(`api/get_orders.php?${params.toString()}`);
            
            // Vérification du statut HTTP avant de tenter de parser en JSON
            if (!response.ok) {
                throw new Error(`Erreur serveur (${response.status})`);
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            const ordersList = Array.isArray(data) ? data : (data.orders || []);
            renderOrdersTable(ordersList);

        } catch (error) {
            console.error('Erreur Fetch:', error);
            ordersTbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>${escapeHtml(error.message || 'Erreur réseau')}
                    </td>
                </tr>`;
        }
    }

    /**
     * Rendu du tableau HTML
     */
    function renderOrdersTable(orders) {
        ordersCount.textContent = `${orders.length} commande(s)`;

        if (orders.length === 0) {
            ordersTbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-2"></i>Aucune commande trouvée.
                    </td>
                </tr>`;
            return;
        }

        ordersTbody.innerHTML = orders.map(order => `
            <tr>
                <td class="fw-bold">#${escapeHtml(String(order.commande_id || order.id || ''))}</td>
                <td>${escapeHtml(order.prenom || '')} ${escapeHtml(order.nom || '')}</td>
                <td>${formatDate(order.date_commande)}</td>
                <td class="fw-semibold">${parseFloat(order.montant_total || 0).toFixed(2)} €</td>
                <td>
                  <span class="badge ${getStatusBadgeClass()}">
                  ${formatStatus(order.statut)}
                  </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1" title="Voir détails" data-id="${order.commande_id}">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Utilitaires de formatage et sécurité
     */
    function getStatusBadgeClass() {
    // Style unique, sobre et élégant pour tous les statuts
    return 'bg-light text-dark border fw-normal px-2 py-1';
    }

    function formatStatus(status) {
    if (!status) return 'Inconnu';
    return escapeHtml(status);
    } 

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        // Remplacer l'espace par 'T' pour que le constructeur Date fonctionne correctement
        const formattedStr = dateStr.replace(' ', 'T');
        const date = new Date(formattedStr);
        return isNaN(date.getTime()) ? dateStr : date.toLocaleString('fr-FR', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // ÉCOUTE DES ÉVÉNEMENTS

    // Recherche textuelle avec anti-rebond (300ms)
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(loadOrders, 300);
    });

    // Filtres instantanés
    statusSelect.addEventListener('change', loadOrders);
    dateInput.addEventListener('change', loadOrders);

    // Bouton de réinitialisation
    btnReset.addEventListener('click', () => {
        searchInput.value = '';
        statusSelect.value = '';
        dateInput.value = '';
        loadOrders();
    });

    // Chargement initial
    loadOrders();
});
