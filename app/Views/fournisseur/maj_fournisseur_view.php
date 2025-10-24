<form class="form-validate-upd-jquery modifier-fournisseur-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>
    <div class="form-group image-upload text-center">
        <label for="fileInput_upd">
            <img id="preview_upd" src="data:image/png;base64,<?= $data->photo ?>" alt="" width="150" height="150" style="object-fit: cover; border-radius: 8px;">
        </label>
        <input type="file" id="fileInput_upd" name="image_upd" accept="image/*" />
    </div>

    <div class="form-group text-center">
        <span><b><?= "F" . str_pad($data->id, 4, '0', STR_PAD_LEFT); ?></b></span>
    </div>

    <div class="form-group">
        <label>Libellé <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Nom" name="libelle_upd"
            id="libelle_upd" required="required" value="<?= $data->libelle ?>" <?= $disabled; ?>>
        <label id="libelle_upd-error" class="validation-error-label" for="libelle_upd"></label>
    </div>

    <div class="form-group">
        <label>Contact téléphonique</label>
        <input type="text" class="form-control input-xs" placeholder="Contact téléphonique" name="contact_upd"
            id="contact_upd" onkeyup="inputPhoneNumber(this)" required="required" value="<?= $data->contact ?>" <?= $disabled; ?>>
        <label id="contact_upd-error" class="validation-error-label" for="contact_upd"></label>
    </div>

    <div class="form-group">
        <label>Adresse mail</label>
        <input type="text" class="form-control input-xs" placeholder="Email" name="mail_upd"
            id="mail_upd" required="required" value="<?= $data->mail ?>" <?= $disabled; ?>>
        <label id="mail_upd-error" class="validation-error-label" for="mail_upd"></label>
    </div>

    <div class="form-group">
        <label>Adresse</label>
        <input type="text" class="form-control input-xs" placeholder="Adresse" name="adresse_upd"
            id="adresse_upd" required="required" value="<?= $data->adresse ?>" <?= $disabled; ?>>
        <label id="adresse_upd-error" class="validation-error-label" for="adresse_upd"></label>
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

        const fileInput = document.getElementById('fileInput_upd');
        const preview = document.getElementById('preview_upd');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(event) {
                    preview.src = event.target.result;
                };

                reader.readAsDataURL(file);
            }
        });
    });
</script>