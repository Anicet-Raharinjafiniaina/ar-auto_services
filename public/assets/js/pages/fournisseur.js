$(function () {
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-fournisseur" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-fournisseur").click(function () {
    loaderContent('main')
    $("#modal_ajout_fournisseur").modal("show");
    $("#libelle").val("");
    $("#contact").val("");
    $("#mail").val("");
    $("#adresse").val("");
    $("#fileInput").val("");
    $("#preview").attr("src", "data:image/png;base64,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")
    $(".validation-error-label").html("");
    stopLoaderContent('main')
});

(function () {
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');

    fileInput.addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();

            reader.onload = function (event) {
                preview.src = event.target.result;
            };

            reader.readAsDataURL(file);
        }
    });
})();

function insert() {
    $(".validation-error-label").html("");
    if ($("#libelle").val() == "") {
        $('#libelle-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
    } else {
        const formData = new FormData(document.querySelector('.add-fournisseur-content'));
        $("#save_upd").prop("disabled", true);
        loaderContent('modal_ajout_fournisseur')
        $.ajax({
            url: urlProject + "Fournisseur/insert",
            type: "POST",
            data: formData, // Envoyez formData directement
            processData: false, // Important pour FormData
            contentType: false, // Important pour FormData
            success: function (res) {
                stopLoaderContent('modal_ajout_fournisseur')
                $("#save_upd").prop("disabled", false);
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le fournisseur <b>" + $("#libelle").val() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "Fournisseur";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "Le fournisseur <b>" + $("#libelle").val() + "</b>  existe déjà",
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
                stopLoaderContent('modal_ajout_fournisseur')
                $("#save_upd").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-fournisseur").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Fournisseur/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-fournisseur").html(res);
            $("#modal_view_fournisseur").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du fournisseur <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un fournisseur");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce fournisseur est irréversible !",
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
                url: urlProject + "Fournisseur/deleteFournisseur",
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
                text: "Le fournisseur a été supprimé.",
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
    $("#libelle_upd-error").text("");
    isValid = true;
    if ($('#libelle_upd').val() == "") {
        $('#libelle_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    }
    if (isValid == true) {
        const formData = new FormData(document.querySelector('.modifier-fournisseur-content'));
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
                loaderContent('modal_view_fournisseur')
                $.ajax({
                    url: urlProject + "Fournisseur/majFournisseur",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_fournisseur')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "Fournisseur";
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
                                html: "Le fournisseur <b>" + $("#libelle").val() + "</b>  existe déjà",
                                icon: "warning",
                                showConfirmButton: true
                            })
                            $("#save_upd").prop("disabled", false);
                        }
                        else {
                            Swal.fire({
                                title: "Erreur",
                                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                                icon: "error",
                                timer: 2000,
                                showConfirmButton: false,
                            });
                            stopLoaderContent('modal_view_fournisseur')
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
                        stopLoaderContent('modal_view_fournisseur')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Fournisseur/doExport";
    stopLoaderContent('main')
}