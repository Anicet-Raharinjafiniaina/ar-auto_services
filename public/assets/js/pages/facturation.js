function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-facturation").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "Facturation/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-facturation").html(res);
            $("#modal_view_facturation").modal("show");
            getClient()
            $('.select.select-search').each(function () {
                initialiseSelect2Modal(this.id, 'modal_view_facturation');
            });
            $('input[id^="prix_unitaire_upd"]').each(function () {
                formatMontant(this);
            });
            $('input[id^="montant_upd"]').each(function () {
                formatMontant(this);
            });
            $('.montant_upd').each(function () {
                formatMontant(this);
            });
            formatMontantNotForInput()
            $('.select2-container').css({
                'pointer-events': 'none',  // empêche tout clic
                'opacity': '0.8'           // effet "grisé"
            })
                .find('.select2-selection__arrow') // cible la flèche
                .css('display', 'none');           // la cache

            if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Validation d'un BC");
            }
        }
    });
}

function viewPdf(id, action) {
    loaderContent('main')
    const url = urlProject + `Facturation/viewOrDownloadPDF?id=${id}&action=${action}`;
    window.open(url, '_blank');
    stopLoaderContent('main')
}

function getClient() {
    loaderContent('modal_view_facturation')
    $.ajax({
        url: urlProject + 'Devis/getAllClient',
        type: 'POST',
        dataType: 'json',
        data: {
            type_client: $('#type_client_upd').val()
        },
        success: function (data) {
            setDataSelected('client_upd', data, 'client_id_upd')
            stopLoaderContent('modal_view_facturation')
        }
    })
}

function validerFacture(id, validation) {
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".form-facture", ".obligatoire")
    if (isValid) {
        if (validation == 3) { // statut valider
            const modal = document.querySelector('.modal.show'); // modal parent
            if (modal) modal.inert = true; // désactive temporairement le modal
            Swal.fire({
                target: 'body',
                title: "Montant payé par le client",
                html: `
                <div class="input-group">
                    <input id="montant_paye" type="text" class="form-control text-end" placeholder="Saisir le montant" onkeyup="formatMontant(this)" value="${$('#net_a_payer_upd').val()}">
                    <div class="input-group-append">
                        <span class="input-group-text">Ar</span>
                    </div>
                </div>
            `,
                focusConfirm: false,
                allowOutsideClick: false,
                showCancelButton: true,
                confirmButtonText: 'Valider',
                cancelButtonText: 'Annuler',
                preConfirm: () => {
                    const net_a_payer = parseFloat(document.getElementById('net_a_payer_upd').value.replace(/\s+/g, ''));
                    const montant = parseFloat(document.getElementById('montant_paye').value.replace(/\s+/g, ''));
                    if (!montant || isNaN(montant) || Number(montant) <= 0 || montant > net_a_payer) {
                        Swal.showValidationMessage("Veuillez saisir un montant valide");
                        return false;
                    }
                    return montant;
                }
            }).then((result) => {
                if (modal) modal.inert = false; // réactive le modal parent
                if (result.isConfirmed) {
                    lancerValidationFacture(id, validation, result.value);
                }
            });
        } else {
            lancerValidationFacture(id, validation, null);
        }
    } else {
        Swal.fire({
            title: "Information",
            html: "Un ou plusieurs éléments sont manquants. Il est possible que certains soient inactifs ou supprimés. De ce fait, <b>il est impossible de valider/réfuser cette facture</b>.",
            icon: "warning",
            showConfirmButton: true
        });
    }
}

function lancerValidationFacture(id, validation, montant) {
    loaderContent('modal_view_facturation')
    $("#valider, #annuler, #refuser").prop("disabled", true);
    var libelle = validation == 2 ? "Refus" : validation == 3 ? "Validation" : "";
    $.ajax({
        url: urlProject + "Facturation/validerFacture",
        type: "POST",
        data: {
            id: id,
            validation: validation,
            montant: montant
        },
        success: function (res) {
            stopLoaderContent('modal_view_facturation')
            $("#valider, #annuler, #refuser").prop("disabled", false);
            try {
                res = JSON.parse(res);
            } catch (e) {
                console.error("Erreur JSON :", e, res);
            }

            if (Array.isArray(res)) {
                Swal.fire({
                    title: "Information",
                    html: "<b>Stock insuffisant</b> : il ne reste plus que <b>" + res[2] + "</b> en stock pour l'article <b>" + res[1] + "</b>.",
                    icon: "warning",
                    showConfirmButton: true
                });
            } else {
                const val = Number(res);
                if (val == 1) {
                    Swal.fire({
                        title: "Validation facture",
                        html: libelle + " facture effectuée avec succès.",
                        icon: "success",
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $('#modal_view_facturation').modal('hide');
                            loadPage(urlProject + "Facturation", true)
                        }
                    });
                } else if (val == 0) {
                    Swal.fire({
                        title: "Refus",
                        html: "La facture n'a pas été validée.",
                        icon: "error",
                        showConfirmButton: true
                    });
                } else {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        },
        error: function () {
            Swal.fire({
                title: "Erreur",
                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
            });
            stopLoaderContent('modal_view_facturation')
            $("#valider, #annuler, #refuser").prop("disabled", false);
        }
    });
}


function exporter() {
    loaderContent('main')
    window.location.href = urlProject + "Facturation/doExport";
    stopLoaderContent('main')
}
/**
 * Dévalidation facture
 */
function viewDevalidation(id, action) {
    var t = $("#l" + id).text();
    $("#content-devalidation").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "DevalidationFacture/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-devalidation").html(res);
            $("#modal_view_devalidation").modal("show");
            getClient()
            $('.select.select-search').each(function () {
                initialiseSelect2Modal(this.id, 'modal_view_devalidation');
            });
            $('input[id^="prix_unitaire_upd"]').each(function () {
                formatMontant(this);
            });
            $('input[id^="montant_upd"]').each(function () {
                formatMontant(this);
            });
            $('.montant_upd').each(function () {
                formatMontant(this);
            });
            formatMontantNotForInput()
            $('.select2-container').css({
                'pointer-events': 'none',  // empêche tout clic
                'opacity': '0.8'           // effet "grisé"
            })
                .find('.select2-selection__arrow') // cible la flèche
                .css('display', 'none');           // la cache

            if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Validation d'un BC");
            }
        }
    });
}

function devalidationFacture(id) {
    Swal.fire({
        title: "Voulez-vous vraiment dévalider la facture ?",
        text: "Cette action est irréversible.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, dévalider",
        cancelButtonText: "Annuler",
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            loaderContent('modal_view_devalidation');
            $("#devalider, #annuler").prop("disabled", true);
            $.ajax({
                url: urlProject + "DevalidationFacture/devaliderFacture",
                type: "POST",
                data: {
                    id: id,
                    validation: 0 // ou la valeur appropriée
                },
                success: function (res) {
                    stopLoaderContent('modal_view_devalidation');
                    $("#devalider, #annuler").prop("disabled", false);

                    if (res == 1) {
                        Swal.fire({
                            title: "Dévalidation facture",
                            html: "Dévalidation effectuée avec succès.",
                            icon: "success",
                            showConfirmButton: true
                        }).then(() => {
                            $('#modal_view_devalidation').modal('hide');
                            loadPage(urlProject + "DevalidationFacture", true)
                        });
                    } else {
                        Swal.fire({
                            title: "Erreur",
                            html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                            icon: "error",
                            timer: 2000,
                            showConfirmButton: false
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: "Erreur",
                        html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                        icon: "error",
                        timer: 2000,
                        showConfirmButton: false
                    });
                    stopLoaderContent('modal_view_devalidation');
                    $("#devalider, #annuler").prop("disabled", false);
                }
            });
        }
    });
}
