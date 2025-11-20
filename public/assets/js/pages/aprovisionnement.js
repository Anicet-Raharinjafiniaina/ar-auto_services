$(function () {
    initialiseSelect2Modal("fournisseur_id", "modal_ajout_appro")
    initialiseSelect2Modal("categorie_id", "modal_ajout_appro")
    initialiseSelect2Modal("article_id", "modal_ajout_appro")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-appro" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$('#categorie_id').on('change', function () {
    loaderContent('modal_ajout_appro')
    $.ajax({
        url: urlProject + 'Approvisionnement/getAllArticle',
        type: 'POST',
        dataType: 'json',
        data: {
            categorie: $('#categorie_id').val()
        },
        success: function (data) {
            setDataSelect('article_id', data)
            stopLoaderContent('modal_ajout_appro')
        }
    })
})

$("#btn-add-appro").click(function () {
    loaderContent('main')
    $("#modal_ajout_appro").modal("show");
    $("#fournisseur_id").val(null).trigger("change.select2");
    $("#categorie_id").val(null).trigger("change.select2");
    $("#article_id").val(null).trigger("change.select2");
    $("#quantite").val("");
    $("#commentaire").val("");
    $(".validation-error-label").html("");
    InputDateForm("date_appro")
    stopLoaderContent('main')
});

function insert() {
    isValid = checkObligatoire(".add-appro-content", ".obligatoire");
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".add-appro-content")
        $("#save").prop("disabled", true);
        loaderContent('modal_ajout_appro')
        $.ajax({
            url: urlProject + "Approvisionnement/insert",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                $("#save").prop("disabled", false);
                stopLoaderContent('modal_ajout_appro')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'approvisionnement de l'article <b>" + $("#article_id option:selected").text() + "</b>  a été ajouté avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_appro').modal('hide');
                            loadPage(urlProject + "Approvisionnement", true)
                        }
                    });
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false,
                    });
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
                stopLoaderContent('modal_ajout_appro')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-appro").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Approvisionnement/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-appro").html(res);
            $("#modal_view_appro").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de l'approvisionnement <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification de l'approvisionnement");
            }
            InputDateForm("date_appro_upd")
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cette ligne d'approvisionnement est irréversible !",
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
                url: urlProject + "Approvisionnement/deleteAppro",
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
                text: "La ligne pour cet approvisionnement a été supprimé.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                stopLoaderContent('main')
                loadPage(urlProject + "Approvisionnement", true)
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-appro-content", ".obligatoire");
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-appro-content")
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
                loaderContent('modal_view_appro')
                $.ajax({
                    url: urlProject + "Approvisionnement/majAppro",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_appro')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_appro').modal('hide');
                                    loadPage(urlProject + "Approvisionnement", true)
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
                        stopLoaderContent('modal_view_appro')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Approvisionnement/doExport";
    stopLoaderContent('main')
}