<?= $this->extend('layouts/main') ?>
<?= $this->section('link') ?>
<style>
    @media (max-width: 576px) {
        .custom-modal-width {
            max-width: 100% !important;
            margin: 0;
            /* optionnel : enlève l'espace sur les côtés */
        }
    }

    @media (min-width: 577px) {
        .custom-modal-width {
            max-width: 60%;
        }
    }

    .validation-error-label {
        display: block;
        white-space: nowrap;
        /* empêche le passage à la ligne */
        overflow: hidden;
        /* coupe le texte qui dépasse */
        text-overflow: ellipsis;
        /* ajoute "..." si le texte est trop long */
        color: red;
        font-size: 0.9em;
    }
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$acces_btn = "";
$style_btn = ($acces_btn == "write" || $acces_btn == "") ? "" : 'style = "display:none;"'; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-create-devis" <?= $style_btn; ?>>
                            <i class="fas fa-edit"></i> créer
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Référence</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date de création</th>
                            <th>Date d'expiration</th>
                            <th>Montant</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_devis)):
                            foreach ($arr_devis as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><?= "PF-" . str_pad($value->id, 4, '0', STR_PAD_LEFT) .  (($value->type_client == 2) ? "-" . $value->client : "") . "-" . date("Y", strtotime($value->date)) . " " . (isset($value->type_vehicule) ? $value->type_vehicule : "") . " " . (isset($value->immatriculation) ? $value->immatriculation : ""); ?></td>
                                    <td><?= ($value->type_client == 1 ? "Client standard" : "Client entreprise") ?></td>
                                    <td><?= $value->client ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date)) ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date_fin)) ?></td>
                                    <td class="montant"><?= fmod($value->net_a_payer, 1) == 0 ? number_format($value->net_a_payer, 0, ',', ' ') : number_format($value->net_a_payer, 2, ',', ' ') ?> <i> Ar</i></td>
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'view')"><i class="fas fa-eye"></i></a></button>
                                        <button href="button" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title="Télécharger" data-placement="bottom" onclick="view(<?= $value->id ?>,'download')" <?= $style_btn; ?>><i class="fa fa-download"></i></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_user" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteDevis(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif;   ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end cardaa -->
    </div> <!-- end col -->
</div>

<!-- Ajout devis -->
<div id="modal_ajout_devis" class="modal fade">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">créer un devis</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="devis_form" class="devis-form" target="_blank" method="post" action="<?= base_url('Devis/saveDevis') ?>">
                                    <input type="hidden" name="form_token" value="<?= esc($token) ?>">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Type de client <span class="text-bold text-danger-600">*</span></label>
                                                <select class="select select-search obligatoire" data-placeholder="Choisir un type de client..." name="type_client" id="type_client" style="width: 100%;">
                                                    <option value=""></option>
                                                    <option value="1">Client standard</option>
                                                    <option value="2">Client entreprise</option>
                                                </select>
                                                <label id="type_client-error" class="validation-error-label" for="type_client"></label>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nom du client <span class="text-bold text-danger-600">*</span></label>
                                                <select class="select select-search obligatoire" data-placeholder="Choisir un client..." name="client" id="client" style="width: 100%;">
                                                </select>
                                                <label id="client-error" class="validation-error-label" for="client"></label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Validité <span class="text-bold text-danger-600">*</span></label>
                                                <div class="input-group">
                                                    <input id="validite" name="validite" class="form-control input-xs text-end obligatoire" type="text" value="15" onkeyup="numberDecimal(this);" onblur="setZeroIfEmpty(this)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"> jour(s) </span>
                                                    </div>
                                                </div>
                                                <label id="validite-error" class="validation-error-label" for="validite"></label>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Garantie des travaux </label>
                                                <div class="input-group">
                                                    <input id="garantie" name="garantie" class="form-control input-xs text-end" type="text" value="5 000" onkeyup="formatMontant(this);" onblur="setZeroIfEmpty(this)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"> km </span>
                                                    </div>
                                                </div>
                                                <label id="garantie-error" class="validation-error-label" for="garantie"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <br>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="message">Nature des travaux </label>
                                                <textarea class="form-control" id="nature_travaux" name="nature_travaux" rows="3" placeholder="Nature des travaux ..."></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Durée approximative des travaux</label>
                                                <div class="input-group">
                                                    <input id="duree_travaux" name="duree_travaux" class="form-control input-xs text-end" type="text" value="1" onkeyup="numberDecimal(this);" onblur="setZeroIfEmpty(this)">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text"> jour(s) </span>
                                                    </div>
                                                </div>
                                                <label id="duree_travaux-error" class="validation-error-label" for="duree_travaux"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label id="vehicule_label">Type de véhicule </label>
                                                <input type="text" class="form-control input-xs" id="type_vehicule" name="type_vehicule" placeholder="Choisir une marque de véhicule..." onblur="checkRequiredFields('type_vehicule', 'immatriculation')">
                                                <label id="type_vehicule-error" class="validation-error-label" for="type_vehicule"></label>
                                            </div>
                                            <div class="col-md-6">
                                                <label id="immatriculation_label">Immatriculation </label>
                                                <input type="text" class="form-control input-xs" id="immatriculation" name="immatriculation" placeholder="Immatriculation..." onkeyup="majuscule(this)" onblur="checkRequiredFields('type_vehicule', 'immatriculation')">
                                                <label id="immatriculation-error" class="validation-error-label" for="immatriculation"></label>
                                            </div>
                                        </div>
                                    </div>

                                    <br><br>
                                    <div class="table-responsive">
                                        <table id="article_table" border="1" cellpadding="5" cellspacing="0" width="100%">
                                            <thead id="article_titre" class="text-center">
                                                <tr>
                                                    <th>Description</th>
                                                    <th style="width:10%">Quantité</th>
                                                    <th style="width:20%">Prix unitaire</th>
                                                    <th>Montant</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article[0][article_id]" id="article_id0" style="width: 100%;" onchange="prixAuto(this)">
                                                            <option value=""></option>
                                                            <?php if (!empty($arr_article)) :
                                                                foreach ($arr_article as $row_article) : ?>
                                                                    <option value="<?= $row_article->id ?>">
                                                                        <?= $row_article->reference . " - " . $row_article->libelle ?>
                                                                    </option>
                                                            <?php endforeach;
                                                            endif; ?>
                                                        </select>
                                                        <label id="article_id0-error" class="validation-error-label" for="article_id0"></label>
                                                    </td>
                                                    <td><input class="form-control input-xs text-end obligatoire" id="quantite0" name="article[0][quantite]" type="text" onkeyup="formatMontant(this)">
                                                        <label id="quantite0-error" class="validation-error-label" for="quantite0"></label>
                                                    </td>
                                                    <td><input class="form-control input-xs text-end obligatoire" id="prix_unitaire0" name="article[0][prix_unitaire]" type="text" onkeyup="formatMontant(this)" onkeyup="prixAuto(this)">
                                                        <label id="prix_unitaire0-error" class="validation-error-label" for="prix_unitaire0"></label>
                                                    </td>
                                                    <td class="total-article montant text-center" name="article[0][total]">0</td>
                                                    <td style="text-align:right"> <button type="button" style="margin-right:1.5em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-article" id="del_article" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-primary btn-sm float-end" style="width:150px;" id="add_article"><i class="fas fa-plus"></i> ajouter un article</button>
                                    <br> <br>
                                    <div class="table-responsive">
                                        <table class="table-responsive" id="autre_table" border="1" cellpadding="5" cellspacing="0" width="100%">
                                            <thead>
                                                <tr id="autre_titre" class="text-center">
                                                    <th>Description</th>
                                                    <!-- <th>Commentaire</th> -->
                                                    <th></th>
                                                    <th>Prix</th>
                                                    <th>Montant</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody> </tbody>
                                        </table>
                                    </div>
                                    <br>
                                    <button type="button" class="btn btn-primary btn-sm float-end" id="add_autre" style="width:150px;"><i class="fas fa-plus"></i> ajouter autre</button>
                                    <br> <br>
                                    <div class="row">
                                        <div class="col-md-6"> </div>
                                        <div class="col-md-6">
                                            <label><b>Total </b></label>
                                            <div class="input-group">
                                                <input name="total" id="total" class="form-control input-xs montant text-end fw-bold" type="text" value="0" style="pointer-events: none;" onchange="formatMontant(this)">
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
                                                <input id="remise" name="remise" class="form-control input-xs text-end" type="text" value="0" onkeyup="formatNumberInput(this);calculerRemise()" onblur="setZeroIfEmpty(this)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"> % </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label><b>Net à payer </b></label>
                                            <div class="input-group">
                                                <input name="net_a_payer" id="net_a_payer" class="form-control input-xs montant text-end fw-bold" type="text" value="0" style="pointer-events: none;" onchange="formatMontant(this)">
                                                <div class="input-group-append">
                                                    <span class="input-group-text"><i>Ar</i> </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <br>
                                    <button type="submit" id="save" class=" btn btn-primary btn-sm float-end" style="width:150px;"><i class="fas fa-edit"></i> Créer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script>
    const articleOptions = `<?php if (!empty($arr_article)): foreach ($arr_article as $row_article): ?>
        <option value="<?= $row_article->id ?>">
            <?= $row_article->reference . " - " . $row_article->libelle ?>
        </option>
    <?php endforeach;
                            endif; ?>`;
</script>
<script src="assets/js/bases_pages/typeahead.min.js?d=<?= date('YmdHis') ?>"></script>
<script type="text/javascript" src="assets/js/pages/devis.js?d=<?= date('YmdHis') ?>"></script>
<?= $this->endSection() ?>