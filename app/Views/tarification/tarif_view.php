<?= $this->extend('layouts/main') ?>

<?= $this->section('link') ?>

<?= $this->endSection() ?>
<!-- datepicker css -->
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
                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" id="btn-add-tarification" <?= $style_btn; ?>>
                            <i class="fas fa-plus position-left"></i> Ajouter
                        </button>
                    </div>
                </div>

            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Catégorie</th>
                            <th>ID Article</th>
                            <th>Référence Article</th>
                            <th>Dénomination Article</th>
                            <th>Prix client standard</th>
                            <th>Prix client entreprise</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($arr_tarif)):
                            foreach ($arr_tarif as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><?= $value->categorie ?></td>
                                    <td><b><?= "A" . str_pad($value->article_id, 4, '0', STR_PAD_LEFT); ?></b></td>
                                    <td><?= $value->reference ?></td>
                                    <td><?= $value->article ?></td>
                                    <td class="montant"><?= $value->prix_client_standard ?> <i> Ar</i></td>
                                    <td class="montant"><?= $value->prix_client_entreprise ?> <i> Ar</i></td>
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

<!-- Ajout tarification -->
<div id="modal_ajout_tarification" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myModalLabel">Ajouter un tarification minimum</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form class="form-validate-jquery add-tarification-content">
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
                        <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article_id" id="article_id" style="width: 100%;">
                        </select>
                        <label id="article_id-error" class="validation-error-label" for="article_id"></label>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Prix pour les clients standards</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-xs text-end obligatoire" placeholder="Prix..." name="prix_client_standard" id="prix_client_standard" onkeyup="formatMontant(this)">
                                    <div class="input-group-append">
                                        <span class="input-group-text fst-italic"> Ar </span>
                                    </div>
                                </div>
                                <label id="prix_client_standard-error" class="validation-error-label" for="prix_client_standard"></label>
                            </div>
                            <div class="col-md-6">
                                <label>Prix pour les clients entreprises</label>
                                <div class="input-group">
                                    <input type="text" class="form-control input-xs text-end obligatoire" placeholder="Prix..." name="prix_client_entreprise" id="prix_client_entreprise" onkeyup="formatMontant(this)">
                                    <div class="input-group-append">
                                        <span class="input-group-text fst-italic"> Ar </span>
                                    </div>
                                </div>
                                <label id="prix_client_entreprise-error" class="validation-error-label" for="prix_client_entreprise"></label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-success btn-xs btn-sm" style="background-color:#21a89f;" id="save" onclick="insert()">Enregistrer</button>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- /Ajout tarification -->

<!-- Visualiser/Modifier tarification -->
<div id="modal_view_tarification" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification tarif</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-tarification"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier tarification -->

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script type="text/javascript" src="assets/js/pages/tarification.js?d=<?= date('YmdHis') ?>"></script>
<?= $this->endSection() ?>