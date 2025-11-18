<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
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
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<link rel="stylesheet" href="assets/libs/flatpickr/flatpickr.min.css">
<?php
$acces_btn = "";
$style_btn = ($acces_btn == "write" || $acces_btn == "") ? "" : 'style = "display:none;"'; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-create-bc" <?= $style_btn; ?>>
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
                            <th>Statut</th>
                            <th>Type</th>
                            <th>Nom</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_bc)):
                            foreach ($arr_bc as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><?= "BC-" . str_pad($value->id, 4, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <?php
                                        $badge = '';
                                        switch ($value->statut_id) {
                                            case 1:
                                                $badge = '<span class="badge bg-warning text-dark rounded-pill p-2 px-3">En attente</span>';
                                                break;
                                            case 2:
                                                $badge = '<span class="badge bg-danger rounded-pill p-2 px-3">Refusé</span>';
                                                break;
                                            case 3:
                                                $badge = '<span class="badge bg-success rounded-pill p-2 px-3">Validé</span>';
                                                break;
                                            default:
                                                $badge = '<span class="badge bg-dark rounded-pill p-2 px-3">Inconnu</span>';
                                                break;
                                        }
                                        echo $badge;
                                        ?>
                                    </td>
                                    <td><?= ($value->type_client == 1 ? "Client standard" : "Client entreprise") ?></td>
                                    <td><?= $value->client ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date)) ?></td>
                                    <td><?= fmod($value->net_a_payer, 1) == 0 ? number_format($value->net_a_payer, 0, ',', ' ') : number_format($value->net_a_payer, 2, ',', ' ')   ?></td>
                                    <?php $disabled_modifier = ($value->statut_id == 3) ? "disabled" : ""; ?>
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button>
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title="Mettre à jour" data-placement="bottom" <?= $disabled_modifier ?> onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_user" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteBc(<?= $value->id ?>)" <?= $disabled_modifier ?> <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
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

<!-- Ajout bc -->
<div id="modal_ajout_bc" class="modal fade">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">créer un bon de commande</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form id="bc_form" class="bc-form" action="<?= base_url('BonDeCommande/saveBc') ?>" method="post">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Type de client <span class="text-bold text-danger-600">*</span></label>
                                                <select class="select select-search obligatoire" data-placeholder="Choisir un type de client..." name="type_client" id="type_client" /*required="required" */ style="width: 100%;">
                                                    <option value=""></option>
                                                    <option value="1">Client standard</option>
                                                    <option value="2">Client entreprise</option>
                                                </select>
                                                <label id="type_client-error" class="validation-error-label" for="type_client"></label>
                                            </div>
                                            <div class="col-md-6">
                                                <label>Nom du client <span class="text-bold text-danger-600">*</span></label>
                                                <select class="select select-search obligatoire" data-placeholder="Choisir un client..." name="client" id="client" /*required="required" */ style="width: 100%;">
                                                </select>
                                                <label id="client-error" class="validation-error-label" for="client"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label>Date <span class="text-bold text-danger-600">*</span></label>
                                                <input type="text" id="date" name="date" class="form-control input-xs text-end obligatoire" value="<?= date('d/m/Y'); ?>" onblur="isFormatDateValideFr(this)">
                                                <label id="date-error" class="validation-error-label" for="date"></label>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="message">Nature des travaux </label>
                                                <textarea class="form-control" id="nature_travaux" name="nature_travaux" rows="3" placeholder="Nature des travaux ..."></textarea>
                                            </div>
                                        </div>
                                    </div>

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
                                                    <td><input class="form-control input-xs text-end obligatoire" id="quantite0" name="article[0][quantite]" type="text" onkeyup="formatMontant(this);checkQuantite(this)">
                                                        <label id="quantite0-error" class="validation-error-label" for="quantite0"></label>
                                                    </td>
                                                    <td><input class="form-control input-xs text-end obligatoire" id="prix_unitaire0" name="article[0][prix_unitaire]" type="text" onkeyup="formatMontant(this)">
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
                                    <button type="submit" id="save" class="btn btn-primary btn-sm float-end" style="width:150px;"><i class="fas fa-edit"></i> Créer</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Visualiser/Modifier bc -->
<div id="modal_view_bc" class="modal fade">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'un utilisateur</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;" id="content-bc"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier bc -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script>
    const articleOptions = `<?php if (!empty($arr_article)): foreach ($arr_article as $row_article): ?>
                                <option value="<?= $row_article->id ?>">
                                    <?= $row_article->reference . " - " . $row_article->libelle ?>
                                </option>
                            <?php endforeach;
                            endif; ?>`;
</script>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script src="assets/js/bases_pages/typeahead.min.js"></script>
<script type="text/javascript" src="assets/js/pages/bc.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>