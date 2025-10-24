$(function () {
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-promotion" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-promotion").click(function () {
    loaderContent('main')
    $("#modal_ajout_promotion").modal("show");
    initializeDuallistBox("list_article_id")
    /** pour dualListBox */
    $('.icon-last').removeClass('icon-last').addClass('fas fa-angle-double-right');
    $('.icon-first').removeClass('icon-first').addClass('fas fa-angle-double-left');
    /** /pour dualListBox */
    $("#pourcentage").val("");
    $("#date_debut").val("");
    $("#date_fin").val("");
    $("#commentaire").val("");
    $("#list_article_id").val("");
    $(".validation-error-label").html("");
    InputDateForm("date_debut")
    InputDateForm("date_fin")
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-promotion-content", ".obligatoire")
    if (String($('#list_article_id').val()).trim() == "") {
        $("#list_article_id-error").html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Séléctionné au moins un artcile.</span>');
        isValid = false;
        return;
    }
    if (isValid == true) {
        $("#save_upd").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-promotion-content")
        loaderContent('modal_ajout_promotion')
        $.ajax({
            url: urlProject + "Promotion/insert",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_promotion')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "La promotion <b>" + $("#libelle").val() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "Promotion";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "Le paramétrage pour <b>" + $("#libelle").val() + "</b>  existe déjà",
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
                stopLoaderContent('modal_ajout_promotion')
                $("#save_upd").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-promotion").html("");
    stopLoaderContent('main')
    $.ajax({
        url: urlProject + "Promotion/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-promotion").html(res);
            $("#modal_view_promotion").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de la promotion <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'une promotion");
            }
            InputDateForm("date_debut_upd")
            InputDateForm("date_fin_upd")
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce paramétrage de promotion est irréversible !",
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
                url: urlProject + "Promotion/deletePromotion",
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
    var isValid = true;
    isValid = checkObligatoire(".modifier-promotion-content", ".obligatoire")
    if (String($('#list_article_id_upd').val()).trim() == "") {
        $("#list_article_id_upd-error").html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Séléctionné au moins un artcile.</span>');
        isValid = false;
        return;
    }
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-promotion-content")
        console.log("arr data " + arr_data);

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
                loaderContent('modal_view_promotion')
                $.ajax({
                    url: urlProject + "Promotion/majPromotion",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_promotion')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "Promotion";
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
                                html: "Le même paramétrage pour la promotion  <b>" + $("#libelle_upd").val() + "</b>  existe déjà",
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
                            stopLoaderContent('modal_view_promotion')
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
                        stopLoaderContent('modal_view_promotion')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Promotion/doExport";
    stopLoaderContent('main')
}