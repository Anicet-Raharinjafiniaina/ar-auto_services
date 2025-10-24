<form class="form-validate-upd-jquery modifier-seuil-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>

    <div class="form-group">
        <label>Catégorie <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="categorie_upd" id="categorie_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php if (!empty($arr_categorie)):
                foreach ($arr_categorie as $row_categorie) :
                    $selected = (($row_categorie->id == $data->categorie_id) ? 'selected' : ''); ?>
                    <option value="<?= $row_categorie->id ?>" <?= $selected ?>>
                        <?= $row_categorie->reference . " - " .  $row_categorie->libelle ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="categorie_upd-error" class="validation-error-label" for="categorie_upd"></label>
    </div>

    <div class="form-group">
        <label>Article <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article_upd" id="article_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php if (!empty($arr_article)):
                foreach ($arr_article as $row_article) :
                    $selected = (($row_article->id == $data->article_id) ? 'selected' : ''); ?>
                    <option value="<?= $row_article->id ?>" <?= $selected ?>>
                        <?= $row_article->reference . " - " .  $row_article->libelle ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="article_upd-error" class="validation-error-label" for="article_upd"></label>
    </div>

    <div class="form-group">
        <label>Seuil minimum</label>
        <input type="text" class="form-control input-xs" placeholder="" name="seuil_min_upd"
            id="seuil_min_upd" onkeyup="numberDecimal(this)" required="required" value="<?= $data->seuil_min ?>" <?= $disabled; ?>>
        <label id="seuil_min_upd-error" class="validation-error-label" for="seuil_min_upd"></label>
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
        initialiseSelect2Modal("categorie_upd", "modal_view_seuil")
        initialiseSelect2Modal("article_upd", "modal_view_seuil")

        $('input[name="actif_upd"]').click(function() {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else if ($(this).is(":not(:checked)")) {
                $(this).val(0);
            }
        });

        $('#categorie_upd').on('change', function() {
            loaderContent('modal_view_seuil')
            $.ajax({
                url: urlProject + 'Approvisionnement/getAllArticle',
                type: 'POST',
                dataType: 'json',
                data: {
                    categorie: $('#categorie_upd').val()
                },
                success: function(data) {
                    stopLoaderContent('modal_view_seuil')
                    setDataSelected("article_upd", data, "article_id_upd")
                }
            })
        })
    });
</script>