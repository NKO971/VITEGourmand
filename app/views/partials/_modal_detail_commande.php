<!-- MODALE DETAIL ET MODIFICATION COMMANDE -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <!-- En-tête de la modale -->
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="orderModalLabel">
                    Détails de la commande <span id="modal-order-number" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>

            <!-- Formulaire de modification -->
            <form id="form-update-order">
                <div class="modal-body">
                    <input type="hidden" id="modal-order-id" name="commande_id">

                    <!-- Infos Client & Prestation -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-muted mb-1">CLIENT</h6>
                            <p id="modal-client-info" class="fw-bold fs-6 mb-0"></p>
                            <small id="modal-client-email" class="text-primary"></small>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h6 class="fw-bold text-muted mb-1">DÉTAILS PRESTATION</h6>
                            <p id="modal-order-date" class="fw-bold mb-0"></p>
                            <small id="modal-order-guests" class="text-muted"></small>
                        </div>
                    </div>

                    <!-- BLOC DYNAMIQUE : Alertes Matériel Prêté & CGV 600 € -->
                    <div id="modal-equipment-alert" class="alert alert-warning border-warning d-flex align-items-start mb-3 d-none" role="alert">
                        <i class="bi bi-box-seam fs-4 me-3 text-warning-emphasis"></i>
                        <div>
                            <h6 class="alert-heading fw-bold mb-1" id="equipment-alert-title">Prêt de matériel associé</h6>
                            <p class="mb-0 small" id="equipment-alert-desc">
                                Du matériel a été mis à disposition du client pour cette prestation.
                            </p>
                            <div id="equipment-penalty-notice" class="mt-2 pt-2 border-top border-warning-subtle small text-danger">
                                <div class="fw-bold mb-1 text-dark">
                                    <i class="bi bi-calendar-check me-1 text-warning-emphasis"></i> Date limite de remise :
                                    <span id="modal-equipment-deadline" class="badge bg-warning text-dark fs-6 ms-1"></span>
                                </div>
                                <div class="fw-bold">
                                    <i class="bi bi-shield-exclamation me-1"></i> Pénalité non-restitution : <strong>600,00 €</strong> (selon CGV).
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Modification du Statut -->
                    <div class="mb-3">
                        <label for="modal-status-select" class="form-label fw-bold">Changer le statut de la commande :</label>
                        <select class="form-select" id="modal-status-select" name="statut" required>
                            <option value="Acceptée">Acceptée</option>
                            <option value="En préparation">En préparation</option>
                            <option value="En cours de livraison">En cours de livraison</option>
                            <option value="Livré">Livré</option>
                            <option value="En attente du retour de matériel">En attente du retour de matériel</option>
                            <option value="Terminée">Terminée</option>
                            <option value="Annulée">Annulée</option>
                        </select>
                    </div>

                    <!-- Champs obligatoires si Annulation -->
                    <div id="cancellation-fields" class="p-3 bg-light border border-danger rounded d-none">
                        <h6 class="text-danger fw-bold mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Contact client obligatoire avant annulation
                        </h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Mode de contact utilisé * :</label>
                            <select class="form-select" id="modal-contact-mode" name="mode_contact">
                                <option value="">-- Sélectionner le moyen de contact --</option>
                                <option value="GSM">Appel GSM</option>
                                <option value="Mail">E-mail</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Motif de l'annulation * :</label>
                            <textarea class="form-control" id="modal-cancel-reason" name="motif_annulation" rows="3" placeholder="Ex: Rupture de stock / Demande du client par téléphone..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- Pied de la modale -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>

        </div>
    </div>
</div>