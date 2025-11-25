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

    <div class="form-group image-upload text-center" style="position: relative; width: 250px; height: 150px; margin: auto;">
        <label for="fileInput_signature_upd" style="display: block; width: 100%; height: 100%; position: relative;">
            <!-- Toujours un <img> avec src valide -->
            <img
                id="preview_signature_upd"
                src="<?= !empty($data->signature) ? 'data:image/png;base64,' . $data->signature : 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=' ?>"
                alt="Signature"
                style="width: 100%; height: 100%; border-radius: 3px; object-fit: contain; background-color: #f0f0f0;">
            <!-- Placeholder texte -->
            <span id="signature_placeholder"
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; 
                     display: <?= !empty($data->signature) ? 'none' : 'flex' ?>; 
                     align-items: center; justify-content: center; color: #888; font-style: italic;">
                Aucune signature
            </span>
        </label>
        <input type="file" id="fileInput_signature_upd" name="fileInput_signature_upd" accept="image/*" style="display: none;">
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

        const fileInputSignature = document.getElementById('fileInput_signature_upd');
        const previewSignature = document.getElementById('preview_signature_upd');
        const placeholder = document.getElementById('signature_placeholder');

        fileInputSignature.addEventListener('change', function(e) {
            const fileS = e.target.files[0];
            if (fileS) {
                const readerS = new FileReader();
                readerS.onload = function(event) {
                    previewSignature.src = event.target.result;
                    // Masquer le texte "Aucune signature"
                    if (placeholder) placeholder.style.display = 'none';
                };
                readerS.readAsDataURL(fileS);
            }
        });
    });
</script>