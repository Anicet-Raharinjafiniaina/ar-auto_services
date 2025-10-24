$(function () {
    initialiseSelect2Modal("categorie", "modal_ajout_seuil")
    initialiseSelect2Modal("article", "modal_ajout_seuil")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-seuil" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-seuil").click(function () {
    loaderContent('main')
    $("#modal_ajout_seuil").modal("show");
    $("#categorie").val("");
    $("#article").val("");
    $("#seuil_min").val("");
    $("#article").trigger("change");
    $("#categorie").trigger("change");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

$('#categorie').on('change', function () {
    if ($('#categorie').val() != '') {
        loaderContent('modal_ajout_seuil')
    }
    $.ajax({
        url: urlProject + 'Approvisionnement/getAllArticle',
        type: 'POST',
        dataType: 'json',
        data: {
            categorie: $('#categorie').val()
        },
        success: function (data) {
            stopLoaderContent('modal_ajout_seuil')
            setDataSelect('article', data)
        }
    })
})

function insert() {
    $(".validation-error-label").html("");
    if ($("#categorie").val() == "") {
        $('#categorie-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else if ($("#article").val() == "") {
        $('#article-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else if ($("#seuil_min").val() == "") {
        $('#seuil_min-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else {
        const formData = new FormData(document.querySelector('.add-seuil-content'));
        $("#save_upd").prop("disabled", true);
        loaderContent('modal_ajout_seuil')
        $.ajax({
            url: urlProject + "SeuilStock/insert",
            type: "POST",
            data: formData, // Envoyez formData directement
            processData: false, // Important pour FormData
            contentType: false, // Important pour FormData
            success: function (res) {
                stopLoaderContent('modal_ajout_seuil')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le seuil minimum pour <b>" + $("#article option:selected").text() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "SeuilStock";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "La seuil minimum pour  <b>" + $("#article option:selected").text() + "</b>  existe déjà",
                        icon: "warning",
                        showConfirmButton: true
                    })
                    $("#save_upd").prop("disabled", false);
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#save_upd").prop("disabled", false);
                }
            },
            error: function (xhr, status, error) {
                Swal.fire({
                    title: "Erreur",
                    html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false,
                });
                stopLoaderContent('modal_ajout_seuil')
                $("#save_upd").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-seuil").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "SeuilStock/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-seuil").html(res);
            $("#modal_view_seuil").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du seuil minimum <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification du seuil minimum");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce paramétrage de seuil est irréversible !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#EF5350",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Oui",
        cancelButtonText: "Non",
        showLoaderOnConfirm: true,
        allowOutsideClick: () => !Swal.isLoading(),
        preConfirm: () => {
            loaderContent('main')
            return $.ajax({
                type: "POST",
                url: urlProject + "SeuilStock/deleteSeuil",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return true;
                } else {
                    throw new Error("Erreur lors de la suppression.");
                }
            }).catch(error => {
                stopLoaderContent('main')
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value === true) {
            Swal.fire({
                title: "Supprimé !",
                text: "Suppression faite avec succès.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload(true);
            });
        }
    });

}

function maj() {
    $("#article_upd-error").text("");
    $("#seuil_min_upd-error").text("");
    if ($('#article_upd').val() == "") {
        $('#article_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        return;
    } else if ($('#seuil_min_upd').val() == "") {
        $('#seuil_min_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        return;
    } else {
        const formData = new FormData(document.querySelector('.modifier-seuil-content'));
        formData.append('id_upd', $("#id_upd").val());
        Swal.fire({
            title: "Modification",
            html: "Voulez-vous vraiment procéder à la modification?",
            icon: "warning",
            showConfirmButton: true,
            showCancelButton: true, confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler',
        }).then(function (result) {
            if (result.isConfirmed) {
                $("#save_upd").prop("disabled", true);
                loaderContent('modal_view_seuil')
                $.ajax({
                    url: urlProject + "SeuilStock/majSeuil",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_seuil')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "SeuilStock";
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Modification",
                                html: "Aucune modification.",
                                icon: "warning",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 3) {
                            Swal.fire({
                                title: "Information",
                                html: "Un paramétrage pour <b>" + $("#article_upd option:selected").text() + "</b>  existe déjà",
                                icon: "warning",
                                showConfirmButton: true
                            })
                            $("#save_upd").prop("disabled", false);
                        } else {
                            Swal.fire({
                                title: "Erreur",
                                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                                icon: "error",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        }
                    },
                    error: function (xhr, status, error) {
                        Swal.fire({
                            title: "Erreur",
                            html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false,
                        });
                        stopLoaderContent('modal_view_seuil')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "SeuilStock/doExport";
    stopLoaderContent('main')
}