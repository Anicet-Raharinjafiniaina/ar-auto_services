<form class="form-validate-upd-jquery modifier-promotion-content">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $data->id ?>" disabled>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label>Libellé<span class="text-bold text-danger-600">*</span></label>
                <input type="text" class="form-control input-xs obligatoire" placeholder="Libellé..." name="libelle_upd" id="libelle_upd" value="<?= $data->libelle; ?>" <?= $disabled; ?>>
                <label id="libelle_upd-error" class="validation-error-label" for="libelle_upd"></label>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label>Promotion (%) <span class="text-bold text-danger-600">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Pourcentage..." name="pourcentage_upd" id="pourcentage_upd" value="<?= $data->pourcentage; ?>" onkeyup="formatNumberInput(this)" onblur="setZeroIfEmpty(this)" <?= $disabled; ?>>
                        <div class="input-group-append">
                            <!-- Icône d'œil pour montrer/cacher le mot de passe -->
                            <span class="input-group-text"> % </span>
                        </div>
                    </div>
                    <label id="pourcentage_upd-error" class="validation-error-label" for="pourcentage_upd"></label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-md-6">
                    <label>Date de début <span class="text-bold text-danger-600">*</span></label>
                    <input type="text" class="form-control input-xs obligatoire" placeholder=".." name="date_debut_upd" id="date_debut_upd" value="<?= date("d/m/Y", strtotime($data->date_debut)); ?>" onblur="isFormatDateValideFr(this);compareTwoDate('date_debut_upd','date_fin_upd', 'date de début','date de fin')" <?= $disabled; ?>>
                    <label id="date_debut_upd-error" class="validation-error-label" for="date_debut_upd"></label>
                </div>

                <div class="col-md-6">
                    <label>Date de fin <span class="text-bold text-danger-600">*</span></label>
                    <input type="text" class="form-control input-xs obligatoire" placeholder="..." name="date_fin_upd" id="date_fin_upd" value="<?= date("d/m/Y", strtotime($data->date_fin)); ?>" onblur="isFormatDateValideFr(this);compareTwoDate('date_debut_upd','date_fin_upd', 'date de début','date de fin')" <?= $disabled; ?>>
                    <label id="date_fin_upd-error" class="validation-error-label" for="date_fin_upd"></label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="panel panel-flat">
                <div class="panel-heading">
                    <h6 class="panel-title">Liste des articles <span class="text-bold text-danger-600">*</span></h6>
                </div>
                <br><br>
                <div class="panel-body">
                    <select multiple="multiple" class="form-control listbox" id="list_article_id_upd" name="list_article_id_upd" <?= $disabled; ?>>
                        <?php if (!empty($arr_article)) :
                            foreach ($arr_article as $key => $row_article) :
                                $selected = (in_array($row_article->id, $data->list_article_id) ? 'selected' : ''); ?>
                                <option value="<?= $row_article->id ?>" <?= $selected ?>>
                                    <?= $row_article->reference . " - " . $row_article->libelle ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            </div>
            <label id="list_article_id_upd-error" class="mt-2 validation-error-label" for="list_article_id_upd"></label>
        </div>

        <div class="form-group mt-2">
            <label>Commentaire</label>
            <textarea type="text" class="form-control input-xs" placeholder="Commentaire..." name="commentaire_upd" id="commentaire_upd" <?= $disabled; ?>><?= $data->commentaire; ?></textarea>
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
                <button type="button" class="btn btn-success btn-sm float-right" style="background-color:#21a89f;" id="save_upd" onclick="maj()"
                    data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                    Enregistrer</button>
            </div>
        <?php endif; ?>
</form>

<script type="text/javascript">
    $(function() {
        initializeDuallistBox("list_article_id_upd")
        /** pour dualListBox */
        $('.icon-last').removeClass('icon-last').addClass('fas fa-angle-double-right');
        $('.icon-first').removeClass('icon-first').addClass('fas fa-angle-double-left');
        /** /pour dualListBox */

        $('input[name="actif_upd"]').click(function() {
            if ($(this).is(":checked")) {
                $(this).val(1);
            } else if ($(this).is(":not(:checked)")) {
                $(this).val(0);
            }
        });
    });
</script>