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
                <input id="date_upd" name="date_upd" class="form-control input-xs text-end obligatoire" type="text" value="<?= date("d/m/Y", strtotime($arr_bc->date)) ?>" onblur="isFormatDateValideFr(this)">
            </div>
            <div class="col-md-6">
                <label for="message">Nature des travaux </label>
                <textarea class="form-control" id="nature_travaux_upd" name="nature_travaux_upd" rows="3" placeholder="Nature des travaux ..."><?= $arr_bc->nature_travaux ?></textarea>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label id="vehicule_label_upd">Type de véhicule </label>
                <input type="text" class="form-control input-xs" id="type_vehicule_upd" name="type_vehicule_upd" placeholder="Choisir une marque de véhicule..." value="<?= $arr_bc->type_vehicule ?>" onblur="checkRequiredFields('type_vehicule_upd', 'immatriculation_upd')">
                <label id="type_vehicule_upd-error" class="validation-error-label" for="type_vehicule_upd"></label>
            </div>
            <div class="col-md-6">
                <label id="immatriculation_label_upd">Immatriculation </label>
                <input type="text" class="form-control input-xs" id="immatriculation_upd" name="immatriculation_upd" placeholder="Immatriculation..." value="<?= $arr_bc->immatriculation ?>" onkeyup="majuscule(this)" onblur="checkRequiredFields('type_vehicule_upd', 'immatriculation_upd')">
                <label id="immatriculation_upd-error" class="validation-error-label" for="immatriculation_upd"></label>
            </div>
        </div>
    </div>

    <br><br>
    <?php if (!empty($arr_bc) && !empty($arr_bc_article)) : ?>
        <div class="table-responsive">
            <table id="article_table_upd" border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead id="article_titre_upd" class="text-center">
                    <tr>
                        <th>Description</th>
                        <th style="width:10%">Quantité</th>
                        <th style="width:20%">Prix unitaire</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    <?php foreach ($arr_bc_article as $key => $value_article) : ?>
                        <tr>
                            <input type="hidden" id="id_upd" name="article[<?= $i ?>][id_upd]" value="<?= $value_article->id ?>">
                            <input type="hidden" id="bc_id_upd" name="article[<?= $i ?>][bc_id_upd]" value="<?= $value_article->bc_id ?>">
                            <td>
                                <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article[<?= $i ?>][article_id_upd]" id="article_id_upd<?= $i ?>" style="width: 100%;" onchange="prixAutoUpd(this)">
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
                            <td><input class="form-control input-xs text-end obligatoire" id="quantite_upd<?= $i ?>" name="article[<?= $i ?>][quantite_upd]" type="text" value="<?= $value_article->quantite ?>" onkeyup="formatMontant(this);checkQuantite(this,'upd')">
                                <label id="quantite_upd<?= $i ?>-error" class="validation-error-label" for="quantite_upd<?= $i ?>"></label>
                            </td>
                            <td><input class="form-control input-xs text-end obligatoire" id="prix_unitaire_upd<?= $i ?>" name="article[<?= $i ?>][prix_unitaire_upd]" type="text" value="<?= $value_article->prix_unitaire ?>" onkeyup="formatMontant(this); ">
                                <label id="prix_unitaire_upd<?= $i ?>-error" class="validation-error-label" for="prix_unitaire_upd<?= $i ?>"></label>
                            </td>
                            <td class="total-article-upd montant text-center" name="article[<?= $i ?>][total_upd]"><?= $value_article->total ?></td>
                            <td style="text-align:right"> <button type="button" style="margin-right:1.5em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-article-upd" id="del_article_upd" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button></td>
                        </tr>
                    <?php $i++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
        <br>
        <button type="button" class="btn btn-primary btn-sm float-end" style="width:150px;" id="add_article_upd"><i class="fas fa-plus"></i> ajouter un article</button>
        <br> <br>
    <?php endif; ?>

    <?php if (!empty($arr_bc) && !empty($arr_bc_autre)) :   ?>
        <div class="table-responsive">
            <table class="table-responsive" id="autre_table_upd" border="1" cellpadding="5" cellspacing="0" width="100%">
                <thead>
                    <tr id="autre_titre_upd" class="text-center">
                        <th>Description</th>
                        <!-- <th>Commentaire</th> -->
                        <th></th>
                        <th>Prix</th>
                        <th>Montant</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                    <?php $j = 0; ?>
                    <?php foreach ($arr_bc_autre as $key => $value_autre) : ?>
                        <tr>
                            <input id="id_upd<?= $j ?>" name="autre[<?= $j ?>][id_upd]" type="hidden" value="<?= $value_autre->id ?>">
                            <input type="hidden" id="bc_id_upd" name="autre[<?= $j ?>][bc_id_upd]" value="<?= $value_autre->bc_id ?>">
                            <td><input class="form-control input-xs obligatoire" id="description_upd<?= $j ?>" name="autre[<?= $j ?>][description_upd]" value="<?= $value_autre->description ?>">
                                <label id="description_upd<?= $j ?>-error" class="validation-error-label" for="description_upd<?= $j ?>"></label>
                            </td>
                            <td><input class="form-control input-xs" name="autre[<?= $j ?>][commentaire]" type="hidden" disabled></td>
                            <td><input class="form-control input-xs text-end obligatoire" id="montant_upd<?= $j ?>" name="autre[<?= $j ?>][montant_upd]" type="text" value="<?= $value_autre->montant ?>" onkeyup="formatMontant(this)">
                                <label id="montant_upd<?= $j ?>-error" class="validation-error-label" for="montant_upd<?= $j ?>"></label>
                            </td>
                            <td class="total-autre-upd montant text-center" name="autre[<?= $j ?>][total_upd]"><?= $value_autre->montant ?></td>
                            <td style="text-align:right"> <button type="button" style="margin-right:1em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-autre-upd" id="del_autre" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button></td>
                        </tr>
                    <?php $j++;
                    endforeach; ?>
                </tbody>
            </table>
        </div>
        <br>
        <button type="button" class="btn btn-primary btn-sm float-end" id="add_autre_upd" style="width:150px;"><i class="fas fa-plus"></i> ajouter autre</button>
    <?php endif; ?>
    <br> <br>
    <div class="row">
        <div class="col-md-6"> </div>
        <div class="col-md-6">
            <label><b>Total </b></label>
            <div class="input-group">
                <input name="total_upd" id="total_upd" class="form-control input-xs montant text-end fw-bold" type="text" value="<?= $arr_bc->total ?>" style="pointer-events: none;" onchange="formatMontant(this)">
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
                <input id="remise_upd" name="remise_upd" class="form-control input-xs text-end" type="text" value="<?= $arr_bc->remise ?>" onkeyup="formatNumberInput(this);calculerRemiseUpd()" onblur="setZeroIfEmpty(this)">
                <div class="input-group-append">
                    <span class="input-group-text"> % </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <label><b>Net à payer </b></label>
            <div class="input-group">
                <input name="net_a_payer_upd" id="net_a_payer_upd" class="form-control input-xs montant text-end fw-bold" type="text" style="pointer-events: none;" value="<?= $arr_bc->net_a_payer ?>" onchange="formatMontant(this)">
                <div class="input-group-append">
                    <span class="input-group-text"><i>Ar</i> </span>
                </div>
            </div>
        </div>
    </div>

    <br>
    <?php if ($disabled == ""): ?>
        <div class="modal-footer d-flex justify-content-end" id="div-upd-footer">
            <button type="submit" class="btn btn-success btn-sm  float-right" style="background-color:#21a89f;" id="save_upd"
                data-loading-text="<i class='icon-spinner10 spinner'></i> Enregistrer" <?= $disabled; ?>>
                Enregistrer</button>
        </div>
    <?php endif; ?>
</form>
<script type="text/javascript" src="assets/js/pages/bc_upd.js?d=<?= date('YmdHis') ?>"></script>