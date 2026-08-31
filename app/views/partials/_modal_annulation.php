<div class="modal fade" id="modalAnnulation" tabindex="-1" aria-labelledby="modalAnnulationLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-cancel-order">
        
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="modalAnnulationLabel">Annulation de la commande</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fermer"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="commande_id" id="cancel_commande_id" value="">
          <input type="hidden" name="statut" value="Annulée">

          <div class="mb-3">
            <label class="form-label fw-bold">Mode de contact utilisé <span class="text-danger">*</span></label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode_contact" id="contactGSM" value="GSM" required>
                <label class="form-check-label" for="contactGSM">Appel GSM</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="mode_contact" id="contactMail" value="Mail" required>
                <label class="form-check-label" for="contactMail">E-mail</label>
              </div>
            </div>
          </div>

          <div class="mb-3">
            <label for="motif_annulation" class="form-label fw-bold">Motif de l'annulation <span class="text-danger">*</span></label>
            <textarea class="form-control" name="motif_annulation" id="motif_annulation" rows="3" placeholder="Ex : Client joint par téléphone, demande l'annulation suite à un imprévu." required></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
          <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
        </div>

      </form>
    </div>
  </div>
</div>