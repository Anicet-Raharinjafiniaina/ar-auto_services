$(function () {
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-vehicule" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-vehicule").click(function () {
    loaderContent('main')
    $("#modal_ajout_vehicule").modal("show");
    $("#libelle").val("");
    $("#fileInput").val("");
    $("#preview").attr("src", "data:image/png;base64,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")
    $(".validation-error-label").html("");
    stopLoaderContent('main');
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
        return;
    } else {
        const formData = new FormData(document.querySelector('.add-vehicule-content'));
        $("#save_upd").prop("disabled", true);
        loaderContent('modal_ajout_vehicule')
        $.ajax({
            url: urlProject + "Vehicule/insert",
            type: "POST",
            data: formData, // Envoyez formData directement
            processData: false, // Important pour FormData
            contentType: false, // Important pour FormData
            success: function (res) {
                stopLoaderContent('modal_ajout_vehicule')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le véhicule <b>" + $("#libelle").val() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "Vehicule";
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "Le véhicule <b>" + $("#libelle").val() + "</b>  existe déjà",
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
                stopLoaderContent('modal_ajout_vehicule')
                $("#save_upd").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-vehicule").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Vehicule/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-vehicule").html(res);
            $("#modal_view_vehicule").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du vehicule <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un vehicule");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce vehicule est irréversible !",
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
                url: urlProject + "Vehicule/del",
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
                text: "Le vehicule a été supprimé.",
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
        const formData = new FormData(document.querySelector('.modifier-vehicule-content'));
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
                loaderContent('modal_view_vehicule')
                $.ajax({
                    url: urlProject + "Vehicule/maj",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_vehicule')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "Vehicule";
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
                                html: "Le véhicule <b>" + $("#libelle").val() + "</b>  existe déjà",
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
                        stopLoaderContent('modal_view_vehicule')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Vehicule/doExport";
    stopLoaderContent('main')
}