function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-societe").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Societe/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-societe").html(res);
            $("#modal_view_societe").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail de la société <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification de la société");
            }
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
        const formData = new FormData(document.querySelector('.modifier-societe-content'));
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
                loaderContent('modal_view_societe')
                $.ajax({
                    url: urlProject + "Societe/maj",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_societe')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_societe').modal('hide');
                                    loadPage(urlProject + "Societe", true)
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
                        stopLoaderContent('modal_view_societe')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}