<form class="form-validate-upd-jquery modifier-societe-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>
    <div class="form-group image-upload text-center">
        <label for="fileInput_upd">
            <img id="preview_upd" src="data:image/png;base64,<?= $data->logo ?>" alt="" width="350px" height="150px" style=" border-radius: 3px;">
        </label>
        <input type="file" id="fileInput_upd" name="logo_upd" accept="image/*" />
    </div>

    <div class="form-group text-center">
        <span><b><?= $data->libelle ?></b></span>
    </div>
    <br> <br> <br> <br>
    <div class="form-group">
        <label>Dénomination <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Dénomination" name="libelle_upd"
            id="libelle_upd" required="required" value="<?= $data->libelle ?>" <?= $disabled; ?>>
        <label id="libelle_upd-error" class="validation-error-label" for="libelle_upd"></label>
    </div>

    <div class="form-group">
        <label>Adresse <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Adresse" name="adresse_upd"
            id="adresse_upd" required="required" value="<?= $data->adresse ?>" <?= $disabled; ?>>
        <label id="adresse_upd-error" class="validation-error-label" for="adresse_upd"></label>
    </div>

    <div class="form-group">
        <label>Ville <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Ville" name="ville_upd"
            id="ville_upd" required="required" value="<?= $data->ville ?>" <?= $disabled; ?>>
        <label id="ville_upd-error" class="validation-error-label" for="ville_upd"></label>
    </div>

    <div class="form-group">
        <label>NIF <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="NIF" name="nif_upd"
            id="nif_upd" required="required" value="<?= $data->nif ?>" <?= $disabled; ?> onkeyup="numberAndSpaceOnly(this)">
        <label id="nif_upd-error" class="validation-error-label" for="nif_upd"></label>
    </div>

    <div class="form-group">
        <label>STAT <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="STAT" name="stat_upd"
            id="stat_upd" required="required" value="<?= $data->stat ?>" <?= $disabled; ?> onkeyup="numberAndSpaceOnly(this)">
        <label id="stat_upd-error" class="validation-error-label" for="stat_upd"></label>
    </div>

    <div class="form-group">
        <label>RCS <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="RCS" name="rcs_upd"
            id="rcs_upd" required="required" value="<?= $data->rcs ?>" <?= $disabled; ?>>
        <label id="rcs_upd-error" class="validation-error-label" for="rcs_upd"></label>
    </div>

    <div class="form-group">
        <label>Nom de la banque <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Nom de la banque" name="banque_upd"
            id="banque_upd" required="required" value="<?= $data->banque ?>" <?= $disabled; ?>>
        <label id="banque_upd-error" class="validation-error-label" for="banque_upd"></label>
    </div>

    <div class="form-group">
        <label>Compte bancaire <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Compte bancaire" name="compte_bancaire_upd"
            id="compte_bancaire_upd" required="required" value="<?= $data->compte_bancaire ?>" <?= $disabled; ?> onkeyup="numberAndSpaceOnly(this)">
        <label id="compte_bancaire_upd-error" class="validation-error-label" for="compte_bancaire_upd"></label>
    </div>

    <div class="form-group">
        <label>Téléphone <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Téléphone" name="telephone_upd"
            id="telephone_upd" required="required" value="<?= $data->telephone ?>" <?= $disabled; ?> onkeyup="inputPhoneNumber(this)">
        <label id="telephone_upd-error" class="validation-error-label" for="telephone_upd"></label>
    </div>

    <div class="form-group">
        <label>Adresse mail <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Adresse mail" name="adresse_mail_upd"
            id="adresse_mail_upd" required="required" value="<?= $data->adresse_mail ?>" <?= $disabled; ?>>
        <label id="adresse_mail_upd-error" class="validation-error-label" for="adresse_mail_upd"></label>
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