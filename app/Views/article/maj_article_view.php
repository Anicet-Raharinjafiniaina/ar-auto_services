<form class="form-validate-upd-jquery modifier-article-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>
    <div class="form-group image-upload text-center">
        <label for="fileInput_upd">
            <img id="preview_upd" src="data:image/png;base64,<?= $data->photo ?>" alt="" width="150" height="150" style="object-fit: cover; border-radius: 8px;">
        </label>
        <input type="file" id="fileInput_upd" name="image_upd" accept="image/*" />
    </div>

    <div class="form-group text-center">
        <span><b><?= $data->reference ?></b></span>
    </div>

    <div class="form-group">
        <label>Catégorie <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir une catégorie..." name="categorie_upd" id="categorie_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php if (!empty($arr_categorie)) :
                foreach ($arr_categorie as $row_categorie) :
                    $selected = (($row_categorie->id == $data->categorie_id) ? 'selected' : ''); ?>
                    <option value="<?= $row_categorie->id ?>" <?= $selected ?>>
                        <?= $row_categorie->reference . " - " . $row_categorie->libelle ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="categorie_upd-error" class="validation-error-label" for="categorie_upd"></label>
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
            id="commentaire_upd" required="required" value="<?= $data->commentaire ?>" <?= $disabled; ?>>
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
        initialiseSelect2Modal("categorie_upd", "modal_view_article")
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