<form class="form-validate-upd-jquery modifier-vehicule-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>
    <div class="form-group image-upload text-center">
        <label for="fileInput_upd">
            <img id="preview_upd" src="data:image/png;base64,<?= $data->photo ?>"
                alt="" style="width: 350px; height: 150px; border-radius: 3px; object-fit: contain;">
        </label>
        <input type="file" id="fileInput_upd" name="image_upd" accept="image/*" />
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