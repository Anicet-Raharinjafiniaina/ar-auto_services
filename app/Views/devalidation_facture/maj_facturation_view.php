<form class="form-validate-upd-jquery modifier-bc-content" id="bc_form_upd" action="<?= base_url('BonDeCommande/majBc') ?>" method="post">
    <input type="hidden" id="id_upd" name="id_upd" value="<?= $arr_bc->id ?>">
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label>Type de client <span class="text-bold text-danger-600">*</span></label>
                <select class="select select-search obligatoire" data-placeholder="Choisir un type de client..." name="type_client_upd" id="type_client_upd" style="width: 100%;">
                    <option value=""></option>
                    <?php if ($arr_bc->type_client == 1): ?>
                        <option value="1" selected>Client standard</option>
                        <option value="2">Client entreprise</option>
                    <?php else : ?>
                        <option value="1">Client standard</option>
                        <option value="2" selected>Client entreprise</option>
                    <?php endif; ?>
                </select>
                <label id="type_client_upd-error" class="validation-error-label" for="type_client_upd"></label>
            </div>
            <div class="col-md-6">
                <input type="hidden" id="client_id_upd" name="client_id_upd" value="<?= $arr_bc->client_id ?>">
                <label>Nom du client <span class="text-bold text-danger-600">*</span></label>
                <select class="select select-search obligatoire" data-placeholder="Choisir un client..." name="client_upd" id="client_upd" style="width: 100%;">
                </select>
                <label id="client_upd-error" class="validation-error-label" for="client_upd"></label>
            </div>
        </div>
    </div>
    <br>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label>Date <span class="text-bold text-danger-600">*</span></label>
                <input id="date_upd" name="date_upd" class="form-control input-xs text-end obligatoire" type="date" value="<?= $arr_bc->date ?>" onblur="isFormatDateValideEn(this)" style="pointer-events: none;">
            </div>
            <div class="col-md-6">
                <label for="message">Nature des travaux </label>
                <textarea class="form-control" id="nature_travaux_upd" name="nature_travaux_upd" rows="3" placeholder="Nature des travaux ..." style="pointer-events: none;"><?= $arr_bc->nature_travaux ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label id="vehicule_label_upd">Type de véhicule </label>
                <input type="text" class="form-control input-xs" id="type_vehicule_upd" name="type_vehicule_upd" placeholder="Choisir une marque de véhicule..." style="pointer-events: none;" value="<?= $arr_bc->type_vehicule ?>" onblur="checkRequiredFields('type_vehicule_upd', 'immatriculation_upd')">
                <label id="type_vehicule_upd-error" class="validation-error-label" for="type_vehicule_upd"></label>
            </div>
            <div class="col-md-6">
                <label id="immatriculation_label_upd">Immatriculation </label>
                <input type="text" class="form-control input-xs" id="immatriculation_upd" name="immatriculation_upd" placeholder="Immatriculation..." style="pointer-events: none;" value="<?= $arr_bc->immatriculation ?>" onkeyup="majuscule(this)" onblur="checkRequiredFields('type_vehicule_upd', 'immatriculation_upd')">
                <label id="immatriculation_upd-error" class="validation-error-label" for="immatriculation_upd"></label>
            </div>
        </div>
    </div>

    <br><br>
    <div class="table-responsive">
        <table id="article_table_upd" border="1" cellpadding="5" cellspacing="0" width="100%">
            <thead id="article_titre_upd" class="text-center">
                <tr>
                    <th>Description</th>
                    <th style="width:10%">Quantité</th>
                    <th style="width:20%">Prix unitaire</th>
                    <th>Montant</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($arr_bc) && !empty($arr_bc_article)) :
                    $i = 0; ?>
                    <?php foreach ($arr_bc_article as $key => $value_article) : ?>
                        <tr>
                            <input type="hidden" id="id_upd" name="article[<?= $i ?>][id_upd]" value="<?= $value_article->id ?>">
                            <input type="hidden" id="bc_id_upd" name="article[<?= $i ?>][bc_id_upd]" value="<?= $value_article->bc_id ?>">
                            <td>
                                <select class="select select-search obligatoire" data-placeholder="Choisir un article..." style="pointer-events: none;" name="article[<?= $i ?>][article_id_upd]" id="article_id_upd<?= $i ?>" style="width: 100%;" onchange="prixAutoUpd(this)">
                                    <option value=""></option>
                                    <?php if (!empty($arr_article)) :
                                        foreach ($arr_article as $row_article) :
                                            $selected = (($arr_bc->id == $value_article->bc_id && $row_article->id == $value_article->article_id) ? 'selected' : ''); ?>
                                            <option value="<?= $row_article->id ?>" <?= $selected ?>>
                                                <?= $row_article->reference . " - " .  $row_article->libelle ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <label id="article_id_upd<?= $i ?>'-error" class="validation-error-label" for="article_id_upd<?= $i ?>"></label>
                            </td>
                            <td class="text-center"><?= $value_article->quantite ?></td>
                            <td class="montant text-center"><?= $value_article->prix_unitaire ?></td>
                            <td class="total-article-upd montant text-center" name="article[<?= $i ?>][total_upd]"><?= $value_article->total ?></td>
                        </tr>
                <?php $i++;
                    endforeach;
                endif; ?>
                <?php if (!empty($arr_bc) && !empty($arr_bc_autre)) :
                    $j = 0; ?>
                    <?php foreach ($arr_bc_autre as $key => $value_autre) : ?>
                        <tr>
                            <input id="id_upd<?= $j ?>" name="autre[<?= $j ?>][id_upd]" type="hidden" value="<?= $value_autre->id ?>">
                            <input type="hidden" id="bc_id_upd" name="autre[<?= $j ?>][bc_id_upd]" value="<?= $value_autre->bc_id ?>">
                            <td>
                                <select class="select select-search obligatoire" data-placeholder=".." style="pointer-events: none;" name="desc[<?= $j ?>][desc_upd_id_upd]" id="desc_upd_id_upd<?= $j ?>" style="width: 100%;" onchange="prixAutoUpd(this)">
                                    <option value="<?= $value_autre->description ?>"><?= $value_autre->description ?></option>
                                </select>
                            </td>
                            <td></td>
                            <td class="montant text-center"><?= $value_autre->montant ?></td>
                            <td class="total-autre-upd montant text-center" name="autre[<?= $j ?>][total_upd]"><?= $value_autre->montant ?></td>
                        </tr>
                    <?php $j++;
                    endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <br>
    <div class="row">
        <div class="col-md-6"> </div>
        <div class="col-md-6">
            <label><b>Total </b></label>
            <div class="input-group">
                <input name="total_upd" id="total_upd" class="form-control input-xs montant_upd text-end fw-bold" type="text" value="<?= $arr_bc->total ?>" style="pointer-events: none;">
                <div class="input-group-append">
                    <span class="input-group-text"><i>Ar</i> </span>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-md-6">
            <label><b>Remise </b></label>
            <div class="input-group">
                <input id="remise_upd" name="remise_upd" class="form-control input-xs text-end" type="text" value="<?= $arr_bc->remise ?>" style="pointer-events: none;">
                <div class="input-group-append">
                    <span class="input-group-text"> % </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <label><b>Net à payer </b></label>
            <div class="input-group">
                <input name="net_a_payer_upd" id="net_a_payer_upd" class="form-control input-xs montant_upd text-end fw-bold" type="text" style="pointer-events: none;" value="<?= $arr_bc->net_a_payer ?>">
                <div class="input-group-append">
                    <span class="input-group-text"><i>Ar</i> </span>
                </div>
            </div>
        </div>
    </div>

    <br>
    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="button" class="btn btn-danger btn-sm float-right" id="devalider" onclick="devalidationFacture('<?= $arr_bc->id ?>')"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Dévalider" <?= $disabled; ?>>
                Dévalider</button>
            <button type="button" class="btn btn-primary btn-sm" data-bs-dismiss="modal" <?= $disabled; ?>>
                Annuler</button>
        </div>
    <?php endif; ?>
</form>
<script type="text/javascript" src="assets/js/pages/facturation.js"></script>