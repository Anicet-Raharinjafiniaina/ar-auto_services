<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
<style>
    .image-upload>input {
        display: none;
    }

    .image-upload img {
        width: 150px;
        height: 150px;
        cursor: pointer;
        object-fit: cover;
        border-radius: 5px;
        border: 2px solid #ddd;
    }
</style>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>
<?php
$acces_btn = "";
$style_btn = ($acces_btn == "write" || $acces_btn == "") ? "" : 'style = "display:none;"'; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-seuil" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
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
                            <th>Seuil minimum</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_seuil)):
                            foreach ($arr_seuil as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><b><?= "A" . str_pad($value->article_id, 4, '0', STR_PAD_LEFT); ?></b></td>
                                    <td><?= $value->reference ?></td>
                                    <td><?= $value->article ?></td>
                                    <td><?= $value->seuil_min ?></td>
                                    <td><?= ($value->actif == 1) ? '<span class="badge bg-primary rounded-pill p-2 px-3">ACTIF</span>' : '<span class="badge bg-secondary rounded-pill p-2 px-3">INACTIF</span>' ?> </td>
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

<!-- Ajout seuil -->
<div id="modal_ajout_seuil" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter un seuil minimum</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-seuil-content">
                    <div class="form-group">
                        <label>Catégorie <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir une catégorie..." name="categorie" id="categorie" style="width: 100%;">
                            <option value=""></option>
                            <?php if (!empty($arr_categorie)) :
                                foreach ($arr_categorie as $row_categorie) : ?>
                                    <option value="<?= $row_categorie->id ?>">
                                        <?= $row_categorie->reference . " - " . $row_categorie->libelle ?>
                                    </option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                        <label id="categorie-error" class="validation-error-label" for="categorie"></label>
                    </div>

                    <div class="form-group">
                        <label>Article <span class="text-bold text-danger-600">*</span></label>
                        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article" id="article" style="width: 100%;">
                        </select>
                        <label id="article-error" class="validation-error-label" for="article"></label>
                    </div>

                    <div class="form-group">
                        <label>Seuil minimum</label>
                        <input type="text" class="form-control input-xs obligatoire" placeholder="Seuil minimum..." name="seuil_min" id="seuil_min" onkeyup="numberDecimal(this)">
                        <label id="seuil_min-error" class="validation-error-label" for="seuil_min"></label>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-xs btn-sm" style="background-color:#21a89f;" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- /Ajout seuil -->

<!-- Visualiser/Modifier seuil -->
<div id="modal_view_seuil" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'un seuil minimum</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-seuil"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier seuil -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="assets/js/pages/seuil_stock.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>