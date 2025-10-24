$(function () {
    initialiseSelect2Modal("categorie", "modal_ajout_tarification")
    initialiseSelect2Modal("article_id", "modal_ajout_tarification")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-tarification" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
    formatMontantNotForInput()
})

$("#btn-add-tarification").click(function () {
    loaderContent('main')
    $("#modal_ajout_tarification").modal("show");
    $("#categorie").val("");
    $("#article_id").val("");
    $("#prix_client_standard").val("");
    $("#prix_client_entreprise").val("");
    $("#article_id").trigger("change");
    $("#categorie").trigger("change");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

$('#categorie').on('change', function () {
    if ($('#categorie').val() != '') {
        loaderContent('modal_ajout_tarification')
    }
    $.ajax({
        url: urlProject + 'Approvisionnement/getAllArticle',
        type: 'POST',
        dataType: 'json',
        data: {
            categorie: $('#categorie').val()
        },
        success: function (data) {
            stopLoaderContent('modal_ajout_tarification')
            setDataSelect('article_id', data)
        }
    })
})

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-tarification-content", ".obligatoire")
    if (isValid == true) {
        $("#save_upd").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-tarification-content")
        loaderContent('modal_ajout_tarification')
        $.ajax({
            url: urlProject + "Tarification/insert",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_tarification')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "La tarification pour l'article <b>" + $("#article option:selected").text() + "</b>  a été créé avec succès.",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "Tarification";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "La tarification pour l'article <b>" + $("#article option:selected").text() + "</b> existe déjà.",
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
                stopLoaderContent('modal_ajout_tarification')
                $("#save_upd").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-tarification").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Tarification/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-tarification").html(res);
            $("#modal_view_tarification").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du tarif <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification du tarif");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce paramétrage de tarification est irréversible !",
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
                url: urlProject + "Tarification/delete",
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
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".modifier-tarification-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-tarification-content")
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
                loaderContent('modal_view_tarification')
                $.ajax({
                    url: urlProject + "Tarification/maj",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_tarification')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "Tarification";
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
                                html: "Un paramétrage pour l'article <b>" + $("#article_id_upd option:selected").text() + "</b> existe déjà",
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
                        stopLoaderContent('modal_view_tarification')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Tarification/doExport";
    stopLoaderContent('main')
}