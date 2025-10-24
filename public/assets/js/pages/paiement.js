$(function () {
    initialiseSelect2Modal("bc_id", "modal_ajout_paiement")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-paiement" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})


$("#btn-add-paiement").click(function () {
    loaderContent('main')
    $("#modal_ajout_paiement").modal("show");
    $("#bc_id").val(null).trigger("change.select2");
    $("#montant").val("");
    $("#commentaire").val("");
    $(".validation-error-label").html("");
    InputDateForm("date_paiement")
    stopLoaderContent('main')
});

function insert() {
    isValid = checkObligatoire(".add-paiement-content", ".obligatoire");
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".add-paiement-content")
        $("#save").prop("disabled", true);
        loaderContent('modal_ajout_paiement')
        $.ajax({
            url: urlProject + "PaiementCredit/insert",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                $("#save").prop("disabled", false);
                stopLoaderContent('modal_ajout_paiement')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le paiement pour de la facture <b>" + $("#bc_id option:selected").text() + "</b>  a été ajouté avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "PaiementCredit";
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
                stopLoaderContent('modal_ajout_paiement')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-paiement").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "PaiementCredit/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-paiement").html(res);
            $("#modal_view_paiement").modal("show");
            InputDateForm("date_paiement_upd")
            initialiseSelect2Modal("bc_id_upd", "modal_view_paiement")
            $('#montant_upd').trigger('keyup');
            $('.select2-container').css({
                'pointer-events': 'none',  // empêche tout clic
                'opacity': '0.8'           // effet "grisé"
            })
                .find('.select2-selection__arrow') // cible la flèche
                .css('display', 'none');
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du paiement <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un paiement");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cette ligne de paiement est irréversible !",
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
                url: urlProject + "PaiementCredit/deletePaiement",
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
                text: "Le paiement a été supprimé avec succès.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                stopLoaderContent('main')
                location.reload(true);
            });
        }
    });

}

function maj() {
    isValid = checkObligatoire(".modifier-paiement-content", ".obligatoire");
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-paiement-content")
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
                loaderContent('modal_view_paiement')
                $.ajax({
                    url: urlProject + "PaiementCredit/majPaiement",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_paiement')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "PaiementCredit";
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
                                html: "Un paramétrage pour <b>" + $("#bc_id_upd option:selected").text() + "</b>  existe déjà",
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
                        stopLoaderContent('modal_view_paiement')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "PaiementCredit/doExport";
    stopLoaderContent('main')
}