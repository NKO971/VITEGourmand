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

    // Mémoire locale globale pour stocker la liste des commandes
    let cachedOrders = [];

    // Fonction principale : Charger les commandes depuis l'API
    async function loadOrders() {
        const params = new URLSearchParams({
            search: searchInput ? searchInput.value.trim() : '',
            status: statusSelect ? statusSelect.value : '',
            date: dateInput ? dateInput.value : ''
        });

        try {
            const response = await fetch(`index.php?page=get_orders&${params.toString()}`);
            
            if (!response.ok) {
                throw new Error(`Erreur serveur (${response.status})`);
            }

            const data = await response.json();

            if (data.error) {
                throw new Error(data.error);
            }

            // Enregistrement des commandes dans la mémoire globale
            cachedOrders = Array.isArray(data) ? data : (data.orders || []);

            renderPendingOrdersZone(cachedOrders);

            // Filtrage des commandes traitées 
            const processedOrders = cachedOrders.filter(order => order.statut !== 'En attente');
            
            renderOrdersTable(processedOrders);

        } catch (error) {
            console.error('Erreur Fetch:', error);
            if (ordersTbody) {
                ordersTbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-danger py-4">
                            <i class="bi bi-exclamation-triangle me-2"></i>${escapeHtml(error.message || 'Erreur réseau')}
                        </td>
                    </tr>`;
            }
        }
    }

    // Gestion des commandes en attente
    function renderPendingOrdersZone(allOrders) {
        const pendingContainer = document.getElementById('pending-orders-container');
        const pendingTbody     = document.getElementById('pending-orders-tbody');
        const pendingCount     = document.getElementById('pending-count');

        if (!pendingContainer || !pendingTbody || !pendingCount) return;

        const pendingOrders = allOrders.filter(order => order.statut === 'En attente');

        if (pendingOrders.length === 0) {
            pendingContainer.classList.add('d-none');
            return;
        }

        pendingContainer.classList.remove('d-none');
        pendingCount.textContent = `${pendingOrders.length} à valider`;

        pendingTbody.innerHTML = pendingOrders.map(order => `
            <tr class="table-warning-subtle">
                <td class="fw-bold">#${escapeHtml(String(order.commande_id))}</td>
                <td>${escapeHtml(order.prenom || '')} ${escapeHtml(order.nom || '')}</td>
                <td>${formatDate(order.date_commande)}</td>
                <td class="fw-bold text-dark">${parseFloat(order.montant_total || order.prix_menu || 0).toFixed(2)} €</td>
                <td class="text-end">
                    <button class="btn btn-sm btn-success me-2 btn-quick-accept" data-id="${order.commande_id}">
                        <i class="bi bi-check-circle me-1"></i> Accepter
                    </button>
                    <button class="btn btn-sm btn-outline-danger open-cancel-modal" 
                    data-id="${order.commande_id}" 
                    data-bs-toggle="modal" 
                    data-bs-target="#modalAnnulation">
                    <i class="bi bi-x-circle me-1"></i> Refuser / Annuler
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Rendu du tableau HTML principal (Zone 2)
    function renderOrdersTable(orders) {
        if (!ordersTbody || !ordersCount) return;

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
                <td class="fw-semibold">${parseFloat(order.montant_total || order.prix_menu || 0).toFixed(2)} €</td>
                <td>
                  <span class="badge ${getStatusBadgeClass()}">
                  ${formatStatus(order.statut)}
                  </span>
                </td>
                <td class="text-end">
                    <button class="btn btn-sm btn-outline-primary me-1 btn-view-detail" title="Voir détails" data-id="${order.commande_id}">
                        <i class="bi bi-eye"></i> Voir détails
                    </button>
                </td>
            </tr>
        `).join('');
    }

    // Utilitaires de formatage et sécurité
    
    function getStatusBadgeClass() {
        return 'bg-light text-dark border fw-normal px-2 py-1';
    }

    function formatStatus(status) {
        if (!status) return 'Inconnu';
        return escapeHtml(status);
    } 

    function formatDate(dateStr) {
        if (!dateStr || dateStr === '0000-00-00') return '-';
        const formattedStr = dateStr.replace(' ', 'T');
        const date = new Date(formattedStr);
        return isNaN(date.getTime()) ? dateStr : date.toLocaleString('fr-FR', {
            day: '2-digit', month: '2-digit', year: 'numeric',
            hour: '2-digit', minute: '2-digit'
        });
    }

    function formatDateOnly(dateStr) {
        if (!dateStr || dateStr === '0000-00-00') return 'Non définie';
        const date = new Date(dateStr.replace(' ', 'T'));
        return isNaN(date.getTime()) ? dateStr : date.toLocaleDateString('fr-FR', {
            day: '2-digit', month: '2-digit', year: 'numeric'
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

    function toggleCancellationFields(show) {
        const cancelBlock = document.getElementById('cancellation-fields');
        if (!cancelBlock) return;
        if (show) {
            cancelBlock.classList.remove('d-none');
        } else {
            cancelBlock.classList.add('d-none');
        }
    }

    function getReturnDeadline(dateStr, workingDays = 10) {
        if (!dateStr || dateStr === '0000-00-00') return 'Non définie';
        let date = new Date(dateStr.replace(' ', 'T'));
        if (isNaN(date.getTime())) return 'Non définie';

        let addedDays = 0;
        while (addedDays < workingDays) {
            date.setDate(date.getDate() + 1);
            if (date.getDay() !== 0 && date.getDay() !== 6) {
                addedDays++;
            }
        }

        return date.toLocaleDateString('fr-FR', {
            day: '2-digit', month: '2-digit', year: 'numeric'
        });
    }

    // ÉCOUTE DES ÉVÉNEMENTS (FILTRES ET BOUTONS)
    
    // Recherche textuelle avec anti-rebond (300ms)
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(loadOrders, 300);
        });
    }

    // Filtres instantanés
    if (statusSelect) statusSelect.addEventListener('change', loadOrders);
    if (dateInput) dateInput.addEventListener('change', loadOrders);

    // Bouton de réinitialisation
    if (btnReset) {
        btnReset.addEventListener('click', () => {
            if (searchInput) searchInput.value = '';
            if (statusSelect) statusSelect.value = '';
            if (dateInput) dateInput.value = '';
            loadOrders();
        });
    }

    // ÉCOUTEUR : Clic sur le bouton d'ouverture de la modale d'annulation
    document.addEventListener('click', (e) => {
        const btnCancel = e.target.closest('.open-cancel-modal');
        if (!btnCancel) return;

        const cancelInputId = document.getElementById('cancel_commande_id');
        if (cancelInputId) {
            cancelInputId.value = btnCancel.getAttribute('data-id');
        }
    });

    // ÉCOUTEUR : Clic sur le bouton "Accepter" dans la Zone 1
    document.addEventListener('click', async (e) => {
        const btnAccept = e.target.closest('.btn-quick-accept');
        if (!btnAccept) return;

        const commandeId = btnAccept.dataset.id;

        try {
            const response = await fetch('index.php?page=update_order_status', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    commande_id: commandeId,
                    statut: 'Acceptée'
                })
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.error);

            loadOrders();

        } catch (error) {
            alert("Erreur lors de la validation : " + error.message);
        }
    });

    // ÉCOUTEUR : Clic sur "Voir détails" pour ouvrir la modale
    document.addEventListener('click', (e) => {
        const btnDetail = e.target.closest('.btn-view-detail');
        if (!btnDetail) return;

        const orderId = parseInt(btnDetail.dataset.id, 10);
        
        const order = cachedOrders.find(o => parseInt(o.commande_id, 10) === orderId);
        if (!order) return;

        const modalOrderId = document.getElementById('modal-order-id');
        if (modalOrderId) modalOrderId.value = order.commande_id;

        const modalOrderNum = document.getElementById('modal-order-number');
        if (modalOrderNum) modalOrderNum.textContent = `#${order.commande_id}`;

        const modalClientInfo = document.getElementById('modal-client-info');
        if (modalClientInfo) {
            modalClientInfo.textContent = `${order.prenom || ''} ${order.nom || ''}`.trim() || 'Client inconnu';
        }

        const modalClientEmail = document.getElementById('modal-client-email');
        if (modalClientEmail) {
            modalClientEmail.textContent = order.email ? order.email : 'Aucun email associé';
        }

        const datePresta = formatDateOnly(order.date_prestation);
        const heurePresta = order.heure_livraison ? ` à ${order.heure_livraison}` : '';
        const modalOrderDate = document.getElementById('modal-order-date');
        if (modalOrderDate) modalOrderDate.textContent = `Date : ${datePresta}${heurePresta}`;

        const modalOrderGuests = document.getElementById('modal-order-guests');
        if (modalOrderGuests) modalOrderGuests.textContent = `${order.nombre_personne || 0} convives`;

        const equipmentAlert    = document.getElementById('modal-equipment-alert');
        const equipmentTitle    = document.getElementById('equipment-alert-title');
        const equipmentDesc     = document.getElementById('equipment-alert-desc');
        const penaltyNotice     = document.getElementById('equipment-penalty-notice');
        const equipmentDeadline = document.getElementById('modal-equipment-deadline');

        const isPret = parseInt(order.pret_materiel, 10) === 1;
        const isRestitue = parseInt(order.restitution_materiel, 10) === 1;

        if (equipmentAlert) {
            if (isPret) {
                equipmentAlert.classList.remove('d-none');
                
                if (isRestitue) {
                    equipmentAlert.className = "alert alert-success border-success d-flex align-items-start mb-3";
                    if (equipmentTitle) equipmentTitle.textContent = "Matériel restitué";
                    if (equipmentDesc) equipmentDesc.textContent = "Le matériel prêté pour cette commande a bien été retourné par le client.";
                    if (penaltyNotice) penaltyNotice.classList.add('d-none');
                } else {
                    equipmentAlert.className = "alert alert-warning border-warning d-flex align-items-start mb-3";
                    if (equipmentTitle) equipmentTitle.textContent = "Prêt de matériel associé";
                    if (equipmentDesc) equipmentDesc.textContent = "Du matériel a été mis à disposition du client pour cette prestation.";
                    if (penaltyNotice) penaltyNotice.classList.remove('d-none');

                    const deadlineStr = getReturnDeadline(order.date_prestation || order.date_commande, 10);
                    if (equipmentDeadline) equipmentDeadline.textContent = deadlineStr;
                }
            } else {
                equipmentAlert.classList.add('d-none');
            }
        }

        const statusSelectElem = document.getElementById('modal-status-select');
        if (statusSelectElem) statusSelectElem.value = order.statut;

        const contactModeElem = document.getElementById('modal-contact-mode');
        const cancelReasonElem = document.getElementById('modal-cancel-reason');
        if (contactModeElem) contactModeElem.value = order.mode_contact || '';
        if (cancelReasonElem) cancelReasonElem.value = order.motif_annulation || '';
        
        toggleCancellationFields(order.statut === 'Annulée');

        const modalEl = document.getElementById('orderModal');
        if (modalEl) {
            const modalInstance = bootstrap.Modal.getOrCreateInstance(modalEl);
            modalInstance.show();
        }
    });

    // ÉCOUTEUR : Changement dynamique du statut dans le select de la modale
    const modalStatusSelect = document.getElementById('modal-status-select');
    if (modalStatusSelect) {
        modalStatusSelect.addEventListener('change', (e) => {
            toggleCancellationFields(e.target.value === 'Annulée');
        });
    }

    // ÉCOUTEUR : Soumission du formulaire de la Modale de modification
    const formUpdateOrder = document.getElementById('form-update-order');
    if (formUpdateOrder) {
        formUpdateOrder.addEventListener('submit', async (e) => {
            e.preventDefault();

            const orderId      = document.getElementById('modal-order-id').value;
            const newStatus    = document.getElementById('modal-status-select').value;
            const contactMode  = document.getElementById('modal-contact-mode').value;
            const cancelReason = document.getElementById('modal-cancel-reason').value;

            try {
                const response = await fetch('index.php?page=update_order_status', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        commande_id: orderId,
                        statut: newStatus,
                        mode_contact: contactMode,
                        motif_annulation: cancelReason
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Erreur lors de la mise à jour.');

                const modalEl = document.getElementById('orderModal');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                loadOrders();

            } catch (error) {
                alert("Attention : " + error.message);
            }
        });
    }

    // ÉCOUTEUR : Soumission du formulaire de la Modale d'annulation
    const formCancelOrder = document.getElementById('form-cancel-order');
    if (formCancelOrder) {
        formCancelOrder.addEventListener('submit', async (e) => {
            e.preventDefault();

            const commandeId  = document.getElementById('cancel_commande_id').value;
            const modeContact = document.querySelector('input[name="mode_contact"]:checked')?.value;
            const motif       = document.getElementById('motif_annulation').value;

            try {
                const response = await fetch('index.php?page=cancel_order', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        commande_id: commandeId,
                        statut: 'Annulée',
                        mode_contact: modeContact,
                        motif_annulation: motif
                    })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || 'Erreur lors de l\'annulation.');

                // Fermeture de la modale d'annulation
                const modalEl = document.getElementById('modalAnnulation');
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                if (modalInstance) modalInstance.hide();

                // Réinitialisation du formulaire
                formCancelOrder.reset();

                // Rechargement dynamique du tableau
                loadOrders();

            } catch (error) {
                alert("Attention : " + error.message);
            }
        });
    }

    // Chargement initial au démarrage de la page
    loadOrders();
});