<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
    <?= $this->section('link') ?>
<?php endif; ?>
<style>
    .image-upload>input {
        display: none;
    }

    .image-upload img {
        width: 350px;
        height: 150px;
        cursor: pointer;
        object-fit: contain;
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
            <div class="card-header"> </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th> </th>
                            <th>Dénomination</th>
                            <th>Adresse</th>
                            <th>ville</th>
                            <th>NIF</th>
                            <th>STAT</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_societe)):
                            foreach ($arr_societe as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><img src="data:image/png;base64,<?= $value->logo ?>" alt="" width="50" height="30" style="object-fit: contain; border-radius: 8px;"></td>
                                    <td><?= $value->libelle ?></td>
                                    <td><?= $value->adresse ?></td>
                                    <td><?= $value->ville ?></td>
                                    <td><?= $value->nif ?></td>
                                    <td><?= $value->stat ?></td>
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="view(<?= $value->id ?>,'voir')"><i class="fas fa-eye"></i></a></button>
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_user" data-popup="tooltip" title=" Mettre à jour" data-placement="bottom" onclick="view(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
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

<!-- Visualiser/Modifier societe -->
<div id="modal_view_societe" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification de la societe</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="content-societe"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier societe -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="assets/js/pages/societe.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>