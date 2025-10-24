$(function () {
    initialiseSelect2Modal("profil", "modal_ajout_user")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-user" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-user").click(function () {
    loaderContent('main')
    $("#modal_ajout_user").modal("show");
    $("#login").val("");
    $("#nom").val("");
    $("#prenom").val("");
    $("#profil").val("");
    $("#profil").trigger("change");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".add-user-content", ".obligatoire")
    if (isValid == true) {
        $("#save").prop("disabled", true);
        let arr_data = getFormDataFromParentClass(".add-user-content")
        loaderContent('modal_ajout_user')
        $.ajax({
            url: urlProject + "User/insertUser",
            type: "POST",
            data: { data: arr_data },
            success: function (res) {
                stopLoaderContent('modal_ajout_user')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'utilisateur a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "User";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Doublon",
                        html: "Le login  <b>" + $("#login").val() + "</b> existe déjà",
                        icon: "warning",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#save").prop("disabled", false);
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                    $("#save").prop("disabled", false);
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
                stopLoaderContent('modal_ajout_user')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-user").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "User/getUser",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-user").html(res);
            $("#modal_view_user").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du l'utilisateur <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un utilisateur");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cet utilisateur est irréversible !",
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
                url: urlProject + "User/deleteUser",
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
                text: "L'utilisateur a été supprimé.",
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
    isValid = checkObligatoire(".modifier-user-content", ".obligatoire")
    if (isValid == true) {
        let arr_data = getFormDataFromParentClass(".modifier-user-content")
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
                loaderContent('modal_view_user')
                $.ajax({
                    url: urlProject + "User/majUser",
                    type: "POST",
                    data: { data: arr_data },
                    success: function (res) {
                        stopLoaderContent('modal_view_user')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "User";
                                }
                            });
                        } else if (res == 2) {
                            Swal.fire({
                                title: "Doublon",
                                html: "Le login  <b>" + $("#login_upd").val() + "</b> existe déjà.",
                                icon: "warning",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 3) {
                            Swal.fire({
                                title: "Modification",
                                html: "Aucune modification.",
                                icon: "warning",
                                timer: 2000,
                                showConfirmButton: false,
                            });
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
                        stopLoaderContent('modal_view_user')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "User/doExport";
    stopLoaderContent('main')
}