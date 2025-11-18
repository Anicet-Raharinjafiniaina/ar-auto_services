<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
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
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-paiement" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>N° Facture</th>
                            <th>Montant payé</th>
                            <th>Restant dû actuel</th>
                            <th>date</th>
                            <th>Commentaire</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_data)):
                            foreach ($arr_data as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><b><?= ("FA-" . str_pad($value->num_facture, 4, '0', STR_PAD_LEFT)) ?></b></td>
                                    <td><?= fmod($value->montant, 1) == 0 ? number_format($value->montant, 0, ',', ' ') : number_format($value->montant, 2, ',', ' ') ?> <i>Ar</i></td>
                                    <td><b><?= fmod($value->restant_du, 1) == 0 ? number_format($value->restant_du, 0, ',', ' ') : number_format($value->restant_du, 2, ',', ' ') ?></b> <i>Ar</i></td>
                                    <td><?= date("d/m/Y", strtotime($value->date_paiement))  ?></td>
                                    <td><?= $value->commentaire ?></td>
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button>
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" id="del_user" data-popup="tooltip" title="Supprimer" data-placement="bottom" onclick="deleteItem(<?= $value->id ?>)" <?= $style_btn; ?>><img src="<?= base_url('assets/images/supprimer.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
                                    </td>
                                </tr>
                        <?php endforeach;
                        endif;   ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ajout user -->
<div id="modal_ajout_paiement" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter un paiement</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-paiement-content">
                    <div class="form-group">
                        <label>N° de facture <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un numéro de facture..." name="bc_id" id="bc_id" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_client)) :
                                foreach ($arr_client as $row) : ?>
                                    <option value="<?= $row->id ?>">
                                        <?= ("FA-" . str_pad($row->num_facture, 4, '0', STR_PAD_LEFT)) ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                        <label id="bc_id-error" class="validation-error-label" for="bc_id"></label>
                    </div>

                    <div class="form-group">
                        <label>Montant <span class="text-bold text-danger-600">*</span></label>
                        <div class="input-group">
                            <input type="text" class="form-control input-xs text-end obligatoire" placeholder="Montant" name="montant" id="montant" required="required" onkeyup="formatMontant(this)">
                            <div class="input-group-append">
                                <span class="input-group-text fst-italic"> Ar </span>
                            </div>
                        </div>
                        <label id="montant-error" class="validation-error-label" for="montant"></label>
                    </div>

                    <div class="form-group">
                        <label>Date <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Date du paiement..." name="date_paiement" id="date_paiement" value="<?= date('d/m/Y'); ?>" onblur="isFormatDateValideFr(this)">
                        <label id="date_paiement-error" class="validation-error-label" for="date_paiement"></label>
                    </div>

                    <div class="form-group mt-2">
                        <label>Commentaire</label>
                        <textarea type="text" class="form-control input-xs" placeholder="Commentaire..." name="commentaire" id="commentaire"></textarea>
                        <label id="commentaire-error" class="validation-error-label" for="commentaire"></label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-xs btn-sm" style="background-color:#21a89f;" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- /Ajout user -->



<!-- Visualiser/Modifier paiement -->
<div id="modal_view_paiement" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification Paiement</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-paiement"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier paiement -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script type="text/javascript" src="assets/js/pages/paiement.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>