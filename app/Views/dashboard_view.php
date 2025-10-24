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


<?= $this->endSection() ?>

<?= $this->section('script') ?>
<script src="assets/libs/chart.js/Chart.bundle.min.js"></script>
<script src="assets/js/pages/dashboard.js"></script>
<?= $this->endSection() ?>