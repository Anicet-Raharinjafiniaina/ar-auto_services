$(function () {

    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-categorie" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-categorie").click(function () {
    loaderContent('main')
    $("#modal_ajout_categorie").modal("show");
    $("#libelle").val("");
    $("#commentaire").val("");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

function insert() {
    $(".validation-error-label").html("");
    if ($("#reference").val() == "") {
        $('#reference-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else if ($("#libelle").val() == "") {
        $('#libelle-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else {
        const formData = new FormData(document.querySelector('.add-categorie-content'));
        $("#save").prop("disabled", true);
        loaderContent('modal_ajout_categorie')
        $.ajax({
            url: urlProject + "Categorie/insert",
            type: "POST",
            data: formData, // Envoyez formData directement
            processData: false, // Important pour FormData
            contentType: false, // Important pour FormData
            success: function (res) {
                stopLoaderContent('modal_ajout_categorie')
                $("#save").prop("disabled", false);
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'categorie <b>" + $("#reference").val() + " - " + $("#libelle").val() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "categorie";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "L'categorie <b>" + $("#libelle").val() + "</b>  existe déjà",
                        icon: "warning",
                        showConfirmButton: true
                    })
                } else if (res == 3) {
                    Swal.fire({
                        title: "Information",
                        html: "L'categorie dont la référence est <b>" + $("#reference").val() + "</b>  existe déjà",
                        icon: "warning",
                        showConfirmButton: true
                    })
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
                stopLoaderContent('modal_ajout_categorie')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-categorie").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Categorie/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-categorie").html(res);
            $("#modal_view_categorie").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de la catégorie <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'une catégorie");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cette catégorie est irréversible !",
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
                url: urlProject + "Categorie/deleteCategorie",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main')
                if (response === 1) {
                    return { status: 1 };
                } else if (response === 2) {
                    Swal.fire({
                        title: "Supprimé !",
                        text: "Impossible de supprimer cette catégorie car elle est rattachée à un article.",
                        icon: "warning",
                        showConfirmButton: true
                    })
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false,
                    });
                }
            }).catch(error => {
                stopLoaderContent('main')
                Swal.showValidationMessage(error.message);
            });
        }
    }).then((result) => {
        if (result.isConfirmed && result.value && result.value.status === 1) {
            Swal.fire({
                title: "Supprimé !",
                text: "La catégorie a été supprimée.",
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
    $("#libelle_upd-error").text("");
    isValid = true;
    if ($('#libelle_upd').val() == "") {
        $('#libelle_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    }
    if (isValid == true) {
        const formData = new FormData(document.querySelector('.modifier-categorie-content'));
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
                loaderContent('modal_view_categorie')
                $.ajax({
                    url: urlProject + "Categorie/majCategorie",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_categorie')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "Categorie";
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
                                html: "La catégorie <b>" + $("#libelle").val() + "</b>  existe déjà",
                                icon: "warning",
                                showConfirmButton: true
                            })
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 4) {
                            Swal.fire({
                                title: "Information",
                                html: "La catégorie dont la référence est <b>" + $("#reference").val() + "</b>  existe déjà",
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
                        stopLoaderContent('modal_view_categorie')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Categorie/doExport";
    stopLoaderContent('main')
}