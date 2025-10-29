<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<style>
    /* Supprime la largeur fixe */
    #chart_container_recette,
    #chart_container_article {
        width: 100%;
        max-width: 100%;
        margin: auto;
    }

    canvas {
        width: 100% !important;
        height: auto !important;
    }
</style>

<div class="row mt-5">
    <div class="col-12 col-md-6 text-center mb-4">
        <h3 class="mb-4">Montant des recettes</h3>
        <div class="form-group">
            <label for="periode_recette"><strong>Choisir la période :</strong></label>
            <select id="periode_recette" class="form-control w-50 mx-auto">
                <option value="quotidien">Quotidien</option>
                <option value="hebdomadaire">Hebdomadaire</option>
                <option value="mensuel" selected>Mensuel</option>
                <option value="annuel">Annuel</option>
            </select>
        </div>
        <div id="chart_container_recette">
            <canvas id="recette_chart"></canvas>
        </div>
    </div>

    <div class="col-12 col-md-6 text-center mb-4">
        <h3 class="mb-4">Vente par article</h3>
        <div class="form-group">
            <label for="periode_article"><strong>Choisir la période :</strong></label>
            <select id="periode_article" class="form-control w-50 mx-auto">
                <option value="quotidien">Quotidien</option>
                <option value="hebdomadaire">Hebdomadaire</option>
                <option value="mensuel" selected>Mensuel</option>
                <option value="annuel">Annuel</option>
            </select>
        </div>
        <div id="chart_container_article">
            <canvas id="article_chart"></canvas>
        </div>
    </div>
</div>

<div class="row">
    <br><br><br>
    <h3 class="text-center">Détails des recettes</h3>
    <div class="col-12">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <table id="datatable-buttons" class="table table-bordered dt-responsive nowrap w-100">
                    <thead class="text-center">
                        <tr>
                            <th>Numéro facture</th>
                            <th>Nature</th>
                            <th>Montant</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        if (!empty($arr_all_recette)):
                            foreach ($arr_all_recette as $key => $value) : ?>
                                <tr>
                                    <td><b><?= "FA-" . str_pad($value->numero_facture_id, 4, '0', STR_PAD_LEFT); ?></b></td>
                                    <td><?= $value->source ?></td>
                                    <td><?= fmod($value->montant, 1) == 0 ? number_format($value->montant, 0, ',', ' ') : number_format($value->montant, 2, ',', ' ') ?><i> Ar</i></td>
                                    <td><?= $value->date ?></td>
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

<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="assets/libs/chart.js/Chart.bundle.min.js"></script>
<script src="assets/js/pages/dashboard.js"></script>
<?= $this->endSection() ?>