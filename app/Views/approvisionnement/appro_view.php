<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-appro" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Approvisionner
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ID Article</th>
                            <th>Référence</th>
                            <th>Dénomination</th>
                            <th>Quantite</th>
                            <th>Date d'approvisionnement</th>
                            <th>Fournisseur</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_appro)):
                            foreach ($arr_appro as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><b><?= "A" . str_pad($value->article_id, 4, '0', STR_PAD_LEFT); ?></b></td>
                                    <td><?= $value->reference ?></td>
                                    <td><?= $value->article ?></td>
                                    <td><?= $value->quantite ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date_appro)) ?></td>
                                    <td><?= $value->fournisseur ?></td>
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
        <!-- end cardaa -->
    </div> <!-- end col -->
</div>

<!-- Ajout appro -->
<div id="modal_ajout_appro" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter un approvisionnement</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-appro-content">
                    <div class="form-group">
                        <label>Fournisseur <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un fournisseur..." name="fournisseur_id" id="fournisseur_id" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_fournisseur)) :
                                foreach ($arr_fournisseur as $row_fournisseur) : ?>
                                    <option value="<?= $row_fournisseur->id ?>">
                                        <?= "F" . str_pad($row_fournisseur->id, 4, '0', STR_PAD_LEFT) . " - " . $row_fournisseur->libelle ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                        <label id="fournisseur_id-error" class="validation-error-label" for="fournisseur_id"></label>
                    </div>

                    <div class="form-group">
                        <label>Catégorie <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir une catégorie..." name="categorie_id" id="categorie_id" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_categorie)) :
                                foreach ($arr_categorie as $row_categorie) : ?>
                                    <option value="<?= $row_categorie->id ?>">
                                        <?= $row_categorie->reference . " - " . $row_categorie->libelle ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                        <label id="categorie_id-error" class="validation-error-label" for="categorie_id"></label>
                    </div>

                    <div class="form-group">
                        <label>Article <span class="text-bold text-danger-600">*</span> &nbsp;</label>
                        <select class="select select-search select-clear select-xs obligatoire" data-placeholder="" name="article_id" id="article_id" required="required">
                        </select>
                        <label id="article_id-error" class="validation-error-label" for="article_id"></label>
                    </div>

                    <div class="form-group">
                        <label>Quantité <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Quantitét..." name="quantite" id="quantite" onkeyup="numberDecimal(this)">
                        <label id="quantite-error" class="validation-error-label" for="quantite"></label>
                    </div>

                    <div class="form-group">
                        <label>Date d'approvisionnement <span class="text-bold text-danger-600">*</span></label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Date d'approvisionnement..." name="date_appro" id="date_appro" value="<?= date('d/m/Y'); ?>" onblur="isFormatDateValideFr(this)">
                        <label id="date_appro-error" class="validation-error-label" for="date_appro"></label>
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
<!-- /Ajout appro -->

<!-- Visualiser/Modifier appro -->
<div id="modal_view_appro" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'un approvisionnement</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-appro"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier appro -->
<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script type="text/javascript" src="assets/js/pages/aprovisionnement.js"></script>
<?= $this->endSection() ?>