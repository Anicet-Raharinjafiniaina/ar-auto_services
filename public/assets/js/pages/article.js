$(function () {
    initialiseSelect2Modal("categorie", "modal_ajout_article")
    const boutonEporter = '<button type="button" class="btn btn-success btn-sm mb-2"  id="btn-download-article" onclick="exporter()"> <i class="fas fa-download position-left"></i> Exporter </button>';
    $('#datatable-buttons_wrapper .row .col-sm-12.col-md-6').first().prepend(boutonEporter);
})

$("#btn-add-article").click(function () {
    loaderContent('main')
    $("#modal_ajout_article").modal("show");
    $("#libelle").val("");
    $("#categorie").val("");
    $("#categorie").trigger("change");
    $("#commentaire").val("");
    $("#fileInput").val("");
    $("#preview").attr("src", "data:image/png;base64,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")
    $(".validation-error-label").html("");
    stopLoaderContent('main');
});

(function () {
    const fileInput = document.getElementById('fileInput');
    const preview = document.getElementById('preview');

    if (fileInput && preview) {
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
    }
})();


function insert() {
    $(".validation-error-label").html("");
    if ($("#categorie").val() == "") {
        $('#categorie-error').html(' <i class= "fa fa-exclamation-circle" > <span class="text-danger font-italic">Ce champ est obligatoire..</span>')
        return;
    } else if ($("#reference").val() == "") {
        $('#reference-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
        return;
    } else if ($("#libelle").val() == "") {
        $('#libelle-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire..</span>');
        return;
    } else {
        const formData = new FormData(document.querySelector('.add-article-content'));
        $("#save").prop("disabled", true);
        loaderContent('modal_ajout_article')
        $.ajax({
            url: urlProject + "Article/insert",
            type: "POST",
            data: formData, // Envoyez formData directement
            processData: false, // Important pour FormData
            contentType: false, // Important pour FormData
            success: function (res) {
                stopLoaderContent('modal_ajout_article')
                $("#save").prop("disabled", false);
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "L'article <b>" + $("#reference").val() + " - " + $("#libelle").val() + "</b>  a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            $('#modal_ajout_article').modal('hide');
                            loadPage(urlProject + "Article", true)
                        }
                    });
                } else if (res == 2) {
                    Swal.fire({
                        title: "Information",
                        html: "L'article <b>" + $("#libelle").val() + "</b>  existe déjà",
                        icon: "warning",
                        showConfirmButton: true
                    })
                } else if (res == 3) {
                    Swal.fire({
                        title: "Information",
                        html: "L'article dont la référence est <b>" + $("#reference").val() + "</b>  existe déjà",
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
                stopLoaderContent('modal_ajout_article')
                $("#save").prop("disabled", false);
            }
        });
    }
}

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-article").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Article/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-article").html(res);
            $("#modal_view_article").modal("show");
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du article <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification d'un article");
            }
        }
    });
}

function deleteItem(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de cet article est irréversible !",
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
                url: urlProject + "Article/deleteArticle",
                data: { id: id },
                dataType: "json"
            }).then(response => {
                stopLoaderContent('main')
                return response; // retourne la valeur (0, 1 ou 2)
            }).catch(error => {
                stopLoaderContent('main')
                Swal.showValidationMessage("Erreur AJAX : " + error.statusText);
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value === 1) {
                Swal.fire({
                    title: "Supprimé !",
                    text: "L'article a été supprimé.",
                    icon: "success",
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    loadPage(urlProject + "Article", true)
                });
            } else if (result.value === 2) {
                Swal.fire({
                    title: "Information!",
                    text: "La suppression de cet article n'est pas possible car il est déjà associé à d'autres opérations.",
                    icon: "warning",
                    showConfirmButton: true
                });
            } else if (result.value === 0) {
                Swal.fire({
                    title: "Erreur!",
                    text: "Erreur lors de la suppression.",
                    icon: "error",
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
    });
}


function maj() {
    $("#categorie_upd-error").text("");
    $("#libelle_upd-error").text("");
    $("#reference_upd-error").text("");
    isValid = true;
    if ($('#categorie_upd').val() == "") {
        $('#categorie_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    } else if ($('#reference_upd').val() == "") {
        $('#reference_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    } else if ($('#libelle_upd').val() == "") {
        $('#libelle_upd-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    }
    if (isValid == true) {
        const formData = new FormData(document.querySelector('.modifier-article-content'));
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
                loaderContent('modal_view_article')
                $.ajax({
                    url: urlProject + "Article/majArticle",
                    type: "POST",
                    data: formData, // Envoyez formData directement
                    processData: false, // Important pour FormData
                    contentType: false, // Important pour FormData
                    success: function (res) {
                        stopLoaderContent('modal_view_article')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    $('#modal_view_article').modal('hide');
                                    loadPage(urlProject + "Article", true)
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
                                html: "L'article <b>" + $("#libelle").val() + "</b>  existe déjà",
                                icon: "warning",
                                showConfirmButton: true
                            })
                            $("#save_upd").prop("disabled", false);
                        } else if (res == 4) {
                            Swal.fire({
                                title: "Information",
                                html: "L'article dont la référence est <b>" + $("#reference").val() + "</b>  existe déjà",
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
                        stopLoaderContent('modal_view_article')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
}

function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Article/doExport";
    stopLoaderContent('main')
}