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
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-promotion" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Libellé</th>
                            <th>Pourcentage</th>
                            <th>Article</th>
                            <th>Date début</th>
                            <th>Date fin</th>
                            <th>Statut</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_promotion)):
                            foreach ($arr_promotion as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><?= $value->libelle ?></td>
                                    <td><?= $value->pourcentage . "%" ?></td>
                                    <td><?= $value->article_libelle ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date_debut)) ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date_fin)) ?></td>
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

<!-- Ajout promotion -->
<div id="modal_ajout_promotion" class="modal fade">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter une promotion</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-promotion-content">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Libellé<span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="Libellé..." name="libelle" id="libelle">
                                <label id="libelle-error" class="validation-error-label" for="libelle"></label>
                            </div>
                            <div class="col-md-6">
                                <label>Promotion (%) <span class="text-bold text-danger-600">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-xs obligatoire" placeholder="Pourcentage..." name="pourcentage" id="pourcentage" onkeyup="formatNumberInput(this)" onblur="setZeroIfEmpty(this)">
                                    <div class="input-group-append">
                                        <!-- Icône d'œil pour montrer/cacher le mot de passe -->
                                        <span class="input-group-text"> % </span>
                                    </div>
                                </div>
                                <label id="pourcentage-error" class="validation-error-label" for="pourcentage"></label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Date de début <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="..." name="date_debut" id="date_debut" value="<?= date('d-m-Y'); ?>" onblur="isFormatDateValideFr(this);compareTwoDate('date_debut','date_fin', 'date de début','date de fin')">
                                <label id="date_debut-error" class="validation-error-label" for="date_debut"></label>
                            </div>

                            <div class="col-md-6">
                                <label>Date de fin <span class="text-bold text-danger-600">*</span></label>
                                <input type="text" class="form-control input-xs obligatoire" placeholder="..." name="date_fin" id="date_fin" value="<?= date('d-m-Y'); ?>" onblur="isFormatDateValideFr(this);compareTwoDate('date_debut','date_fin', 'date de début','date de fin')">
                                <label id="date_fin-error" class="validation-error-label" for="date_fin"></label>
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
                                <select multiple="multiple" class="form-control listbox" id="list_article_id" name="list_article_id">
                                    <?php if (!empty($arr_article)) :
                                        foreach ($arr_article as $key => $val) : ?>
                                            <option value="<?= $val->id ?>">
                                                <?= $val->reference  . " - " . $val->libelle ?>
                                            </option>
                                    <?php endforeach;
                                    endif; ?>
                                </select>
                            </div>
                        </div>
                        <label id="list_article_id-error" class="mt-2 validation-error-label" for="list_article_id"></label>
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
<!-- /Ajout promotion -->

<!-- Visualiser/Modifier promotion -->
<div id="modal_view_promotion" class="modal fade">
    <div class="modal-dialog  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'une promotion</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-promotion"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier promotion -->

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script type="text/javascript" src='<?= base_url("assets/libs/duallistbox/duallistbox.min.js") ?>'></script>
<script src="assets/libs/flatpickr/flatpickr.min.js"></script>
<script type="text/javascript" src="assets/js/pages/promotion.js?d=<?= date('YmdHis')  ?>"></script>
<?= $this->endSection() ?>