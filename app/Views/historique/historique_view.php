<?php if ($request_ajax == 0) : ?>
    <?= $this->extend('layouts/main') ?>
    <?= $this->section('content') ?>
<?php endif; ?>
<?php if (isset($request_ajax) && $request_ajax): ?>
    <div id="ajax-title" data-title="<?= esc($titre) ?>"></div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Date</th>
                            <th>Action</th>
                            <th>Login</th>
                            <th>Nom</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_histo)):
                            foreach ($arr_histo as $key => $value) : ?>
                                <tr id="<?= $value->id ?>" class="text-center">
                                    <td><?= (new DateTime($value->date_creation))->format('d/m/Y H\h i\m\n s\s') ?></td>
                                    <td><?= $value->libelle ?></td>
                                    <td><?= $value->login ?></td>
                                    <td><?= $value->nom ?></td>
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
<?php endif; ?>