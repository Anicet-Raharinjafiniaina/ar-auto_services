<form class="form-validate-upd-jquery modifier-paiement-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>
    <div class="form-group">
        <label>N° de facture <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un numéro de facture..." name="bc_id_upd" id="bc_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value="<?= $data->bc_id ?>"> <?= ("FA-" . str_pad($data->num_facture, 4, '0', STR_PAD_LEFT)) ?></option>
            <?php if (!empty($arr_client)) :
                foreach ($arr_client as $row) :
                    $selected = (($row->id == $data->bc_id) ? 'selected' : '');
            ?>
                    <option value="<?= $row->id ?>" <?= $selected ?>>
                        <?= ("FA-" . str_pad($row->num_facture, 4, '0', STR_PAD_LEFT)) ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="bc_id_upd-error" class="validation-error-label" for="bc_id_upd"></label>
    </div>

    <div class="form-group">
        <label>Montant<span class="text-bold text-danger-600">*</span></label>
        <div class="input-group">
            <input type="text" class="form-control input-xs obligatoire" placeholder="Montant..." name="montant_upd" id="montant_upd" value="<?= $data->montant; ?>" onkeyup="formatMontant(this)" <?= $disabled; ?>>
            <div class="input-group-append">
                <span class="input-group-text"><i> Ar</i></span>
            </div>
        </div>
        <label id="montant_upd-error" class="validation-error-label" for="montant_upd"></label>
    </div>



    <div class="form-group">
        <label>Date <span class="text-bold text-danger-600">*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder=".." name="date_paiement_upd" id="date_paiement_upd" value="<?= date("d/m/Y", strtotime($data->date_paiement)); ?>" onblur="isFormatDateValideFr(this)" <?= $disabled; ?>>
        <label id="date_paiement_upd-error" class="validation-error-label" for="date_paiement_upd"></label>
    </div>


    <div class="form-group mt-2">
        <label>Commentaire</label>
        <textarea type="text" class="form-control input-xs" placeholder="Commentaire..." name="commentaire_upd" id="commentaire_upd" <?= $disabled; ?>><?= $data->commentaire; ?></textarea>
        <label id="commentaire_upd-error" class="validation-error-label" for="commentaire_upd"></label>
    </div>

    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-success btn-sm float-right" style="background-color:#21a89f;" id="save_upd" onclick="maj()"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>