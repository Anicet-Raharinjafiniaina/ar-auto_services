<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
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
            <div class="card-header"></div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>ID Article</th>
                            <th>Référence</th>
                            <th>Dénomination</th>
                            <th>Entrée</th>
                            <th>Sortie</th>
                            <th>Stock théorique</th>
                            <th>Stock réel</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_mouvement)):
                            foreach ($arr_mouvement as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><b><?= "A" . str_pad($value->id, 4, '0', STR_PAD_LEFT); ?></b></td>
                                    <td><?= $value->reference ?></td>
                                    <td><?= $value->libelle ?></td>
                                    <td><?= $value->quantite_in ?></td>
                                    <td><?= $value->quantite_out ?></td>
                                    <td><?= $value->stock_theorique ?></td>
                                    <td><b><?= $value->stock_reel ?></b></td>
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

<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
    <?= $this->section('script') ?>
<?php endif; ?>
<script type="text/javascript" src="assets/js/pages/mouvement_stock.js"></script>
<?php if ($request_ajax == 0) : ?>
    <?= $this->endSection() ?>
<?php endif; ?>