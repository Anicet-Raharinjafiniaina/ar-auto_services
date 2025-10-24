<form class="form-validate-upd-jquery modifier-categorie-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>

    <div class="form-group text-center">
        <span><b><?= $data->reference ?></b></span>
    </div>

    <div class="form-group">
        <label>Référence <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Référence" name="reference_upd"
            id="reference_upd" required="required" value="<?= $data->reference ?>" <?= $disabled; ?>>
        <label id="reference_upd-error" class="validation-error-label" for="reference_upd"></label>
    </div>

    <div class="form-group">
        <label>Libellé <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Nom" name="libelle_upd"
            id="libelle_upd" required="required" value="<?= $data->libelle ?>" <?= $disabled; ?>>
        <label id="libelle_upd-error" class="validation-error-label" for="libelle_upd"></label>
    </div>

    <div class="form-group">
        <label>Commentaire</label>
        <input type="text" class="form-control input-xs" placeholder="" name="commentaire_upd"
            id="commentaire_upd" onkeyup="inputPhoneNumber(this)" required="required" value="<?= $data->commentaire ?>" <?= $disabled; ?>>
        <label id="commentaire_upd-error" class="validation-error-label" for="commentaire_upd"></label>
    </div>

    <?php $checked = ($data->actif == 1) ? 'checked="checked"' : ""; ?>
    <div class="form-group">
        <div class="form-check form-switch switch-label">
            <input type="checkbox" class="form-check-input" name="actif_upd" id="actif_upd" value="<?= $data->actif; ?>"
                <?= $checked; ?> <?= $disabled; ?>>
        </div>
    </div>

    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-success btn-sm  float-right" style="background-color:#21a89f;" id="save_upd" onclick="maj()"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>

<script type="text/javascript">
    $(function() {
        $('input[name="actif_upd"]').click(function() {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else if ($(this).is(":not(:checked)")) {
                $(this).val(0);
            }
        });
    });
</script>