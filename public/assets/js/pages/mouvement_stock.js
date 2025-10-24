$(function () {
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "MouvementStock/doExport";
    stopLoaderContent('main')
}