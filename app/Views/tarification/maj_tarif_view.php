<form class="form-validate-upd-jquery modifier-tarification-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>">

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
        <div class="row">
            <div class="col-md-6">
                <label>Prix pour les clients standards</label>
                <div class="input-group">
                    <input type="text" class="form-control input-xs text-end obligatoire" placeholder="Prix..." name="prix_client_standard_upd" id="prix_client_standard_upd" value="<?= $data->prix_client_standard ?>" <?= $disabled; ?> onkeyup="formatMontant(this)">
                    <div class="input-group-append">
                        <span class="input-group-text fst-italic"> Ar </span>
                    </div>
                </div>
                <label id="prix_client_standard_upd-error" class="validation-error-label" for="prix_client_standard_upd"></label>
            </div>
            <div class="col-md-6">
                <label>Prix pour les clients entreprises</label>
                <div class="input-group">
                    <input type="text" class="form-control input-xs text-end obligatoire" placeholder="Prix..." name="prix_client_entreprise_upd" id="prix_client_entreprise_upd" value="<?= $data->prix_client_entreprise ?>" <?= $disabled; ?> onkeyup="formatMontant(this)">
                    <div class="input-group-append">
                        <span class="input-group-text fst-italic"> Ar </span>
                    </div>
                </div>
                <label id="prix_client_entreprise_upd-error" class="validation-error-label" for="prix_client_entreprise_upd"></label>
            </div>
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
        initialiseSelect2Modal("categorie_upd", "modal_view_tarification")
        initialiseSelect2Modal("article_id_upd", "modal_view_tarification")

        $('#prix_client_standard_upd, #prix_client_entreprise_upd').each(function() {
            formatMontant(this);
        });

        $('#categorie_upd').on('change', function() {
            loaderContent('modal_view_tarification')
            $.ajax({
                url: urlProject + 'Approvisionnement/getAllArticle',
                type: 'POST',
                dataType: 'json',
                data: {
                    categorie: $('#categorie_upd').val()
                },
                success: function(data) {
                    stopLoaderContent('modal_view_tarification')
                    setDataSelected("article_id_upd", data, "article_id_upd")
                }
            })
        })

    });
</script>