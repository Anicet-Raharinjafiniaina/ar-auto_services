<form class="form-validate-upd-jquery modifier-appro-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>

    <div class="form-group">
        <label>Fournisseur <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un fournisseur..." name="fournisseur_id_upd" id="fournisseur_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php if (!empty($arr_fournisseur)):
                foreach ($arr_fournisseur as $row_fournisseur) :
                    $selected = (($row_fournisseur->id == $data->fournisseur_id) ? 'selected' : ''); ?>
                    <option value="<?= $row_fournisseur->id ?>" <?= $selected ?>>
                        <?= "F" . str_pad($row_fournisseur->id, 4, '0', STR_PAD_LEFT)  . " - " .  $row_fournisseur->libelle ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="fournisseur_id_upd-error" class="validation-error-label" for="fournisseur_id_upd"></label>
    </div>

    <div class="form-group">
        <label>Catégorie <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="categorie_id_upd" id="categorie_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
            <option value=""></option>
            <?php if (!empty($arr_categorie)):
                foreach ($arr_categorie as $arr_categorie) :
                    $selected = (($arr_categorie->id == $data->categorie_id) ? 'selected' : ''); ?>
                    <option value="<?= $arr_categorie->id ?>" <?= $selected ?>>
                        <?= $arr_categorie->reference . " - " .  $arr_categorie->libelle ?>
                    </option>
            <?php endforeach;
            endif; ?>
        </select>
        <label id="categorie_id_upd-error" class="validation-error-label" for="categorie_id_upd"></label>
    </div>

    <div class="form-group">
        <label>Article <span class="text-bold text-danger-600" <?= $display; ?>>*</span></label>
        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article_id_upd" id="article_id_upd" required="required" style="width: 100%;" <?= $disabled; ?>>
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
        <label id="article_id_upd-error" class="validation-error-label" for="article_id_upd"></label>
    </div>

    <div class="form-group">
        <label>Quantité <span class="text-bold text-danger-600">*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Quantitét..." name="quantite_upd" id="quantite_upd" value="<?= $data->quantite; ?>" onkeyup="numberDecimal(this)" <?= $disabled; ?>>
        <label id="quantite_upd-error" class="validation-error-label" for="quantite_upd"></label>
    </div>

    <div class="form-group">
        <label>Date d'approvisionnement <span class="text-bold text-danger-600">*</span></label>
        <input type="text" class="form-control input-xs obligatoire" placeholder="Date d'approvisionnement..." name="date_appro_upd" id="date_appro_upd" value="<?= date("d/m/Y", strtotime($data->date_appro)); ?>" onblur="isFormatDateValideFr(this)" <?= $disabled; ?>>
        <label id="date_appro_upd-error" class="validation-error-label" for="date_appro_upd"></label>
    </div>

    <div class="form-group mt-2">
        <label>Commentaire</label>
        <textarea type="text" class="form-control input-xs" placeholder="Commentaire..." name="commentaire_upd" id="commentaire_upd" <?= $disabled; ?>><?= $data->commentaire; ?></textarea>
        <label id="commentaire_upd-error" class="validation-error-label" for="commentaire_upd"></label>
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
        initialiseSelect2Modal("fournisseur_id_upd", "modal_view_appro")
        initialiseSelect2Modal("categorie_id_upd", "modal_view_appro")
        initialiseSelect2Modal("article_id_upd", "modal_view_appro")

        $('input[name="actif_upd"]').click(function() {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else if ($(this).is(":not(:checked)")) {
                $(this).val(0);
            }
        });

        $('#categorie_id_upd').on('change', function() {
            loaderContent('modal_view_appro')
            $.ajax({
                url: urlProject + 'Approvisionnement/getAllArticle',
                type: 'POST',
                dataType: 'json',
                data: {
                    categorie: $('#categorie_id_upd').val()
                },
                success: function(data) {
                    stopLoaderContent('modal_view_appro')
                    setDataSelected("article_id_upd", data, "article_id_upd")
                }
            })
        })
    });
</script>