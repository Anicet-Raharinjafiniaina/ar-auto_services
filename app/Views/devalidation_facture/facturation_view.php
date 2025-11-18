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
<?php
$acces_btn = "";
$style_btn = ($acces_btn == "write" || $acces_btn == "") ? "" : 'style = "display:none;"'; ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row"> </div>
            </div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>N° Facture</th>
                            <th>N° BC</th>
                            <th>Statut</th>
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
                                    <td><?= (($value->num_facture != null) ? "FA-" . str_pad($value->num_facture, 4, '0', STR_PAD_LEFT)  : "") ?></td>
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
                                    <td><?= $value->client ?></td>
                                    <td><?= date("d/m/Y", strtotime($value->date)) ?></td>
                                    <td><?= fmod($value->net_a_payer, 1) == 0 ? number_format($value->net_a_payer, 0, ',', ' ') : number_format($value->net_a_payer, 2, ',', ' ') ?></td>
                                    <td class="text-center cursor-pointer td_no_border">
                                        <button type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_devalidation" data-target="Visualiser" data-popup="tooltip" title="Visualiser" data-placement="bottom" onclick="viewPdf(<?= $value->id ?>,'view')"><i class="fas fa-eye"></i></a></button>
                                        <button href="#" type="button" style="margin-right:0.3em;background:transparent" class="btn btn-icon btn-rounded btn-xs" data-toggle="modal" data-target="#modal_view_devalidation" data-popup="tooltip" title="Mettre à jour" data-placement="bottom" onclick="viewDevalidation(<?= $value->id ?>,'upd')" <?= $style_btn; ?>><img src="<?= base_url('assets/images/modifier.png') ?>" alt="" style="width: 20px; height: 20px;"></button>
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

<!-- Visualiser/Modifier devalidation -->
<div id="modal_view_devalidation" class="modal fade">
    <div class="modal-dialog custom-modal-width">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="title">Modification d'un utilisateur</h5>
                <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;" id="content-devalidation"></div>
        </div>
    </div>
</div>
<!-- /Visualiser/Modifier devalidation -->

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="assets/js/pages/facturation.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>