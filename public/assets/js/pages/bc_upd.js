$(function () {
    toggleRemoveButtons()
    formatMontantNotForInput()
    getClient()
    let max_ite_article = 0;
    $('#modal_view_bc select[id^="article_id_upd"]').each(function () {
        let id = $(this).attr('id'); // article_id_upd0, article_id_upd1, ...
        let num = parseInt(id.split("article_id_upd")[1]);
        if (num > max_ite_article) max_ite_article = num;
    });
    let max_ite_autre = 0;
    $('#modal_view_bc input[id^="description_upd"]').each(function () {
        let id = $(this).attr('id'); // article_id_upd0, article_id_upd1, ...
        let num = parseInt(id.split("description_upd")[1]);
        if (num > max_ite_autre) max_ite_autre = num;
    });
    let index_article = max_ite_article + 1;
    let index_autre = max_ite_autre + 1;
    $('#add_article_upd').on('click', function () {
        $("#article_titre_upd").show()
        $('#article_table_upd tbody').append(`
            <tr>
                 <input type="hidden" id="bc_id_upd${index_article}" name="article[${index_article}][bc_id_upd]" value="${$('#id_upd').val()}">
                 <td>
                <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article[${index_article}][article_id_upd]" id="article_id_upd${index_article}" style="width: 100%;" onchange="prixAutoUpd(this)">
                    <option value=""></option>
                    ${articleOptions}
                </select>
                  <label id="article_id_upd${index_article}-error" class="validation-error-label" for="article_id_upd${index_article}"></label>
            </td>
                <td><input class="form-control input-xs text-end obligatoire" id="quantite_upd${index_article}" name="article[${index_article}][quantite_upd]" type="text" onkeyup="formatMontant(this);checkQuantite(this,'upd')">
                   <label id="quantite_upd${index_article}-error" class="validation-error-label" for="quantite_upd${index_article}"></label>
                </td>
                <td><input class="form-control input-xs text-end obligatoire" id="prix_unitaire_upd${index_article}" name="article[${index_article}][prix_unitaire_upd]" type="text" onkeyup="formatMontant(this)">
                   <label id="prix_unitaire_upd${index_article}-error" class="validation-error-label" for="prix_unitaire_upd${index_article}"></label>
                </td>
                <td class="total-article-upd montant text-center" name="article[${index_article}][total_upd]">0</td>
                <td style="text-align:right"> <button type="button" style="margin-right:1.5em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-article-upd" id="del_article_upd" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="${urlProject}assets/images/supprimer.png" alt="" style="width: 20px; height: 20px;"></button></td>
            </tr>
        `);
        initialiseSelect2Modal("article_id_upd" + index_article, "modal_view_bc")
        toggleRemoveButtons()
        index_article++;
    });

    $('#add_autre_upd').on('click', function () {
        $("#autre_titre_upd").show()
        $('#autre_table_upd tbody').append(`
            <tr>
                 <input type="hidden" id="bc_id_upd${index_autre}" name="autre[${index_autre}][bc_id_upd]" value="${$('#id_upd').val()}">
                <td><input class="form-control input-xs obligatoire" id="description_upd${index_autre}" name="autre[${index_autre}][description_upd]">
                   <label id="description_upd${index_autre}-error" class="validation-error-label" for="description_upd${index_autre}"></label>
                </td>
                <td><input class="form-control input-xs" name="autre[${index_autre}][commentaire]" type="hidden" disabled></td>
                <td><input class="form-control input-xs text-end obligatoire" id="montant_upd${index_autre}" name="autre[${index_autre}][montant_upd]" type="text" value="0" onkeyup="formatMontant(this)">
                   <label id="montant_upd${index_autre}-error" class="validation-error-label" for="montant_upd${index_autre}"></label>
                </td>
                <td class="total-autre-upd montant text-center" name="autre[${index_autre}][total_upd]">0</td>
                <td style="text-align:right"> <button type="button" style="margin-right:1em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-autre-upd" id="del_autre" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="${urlProject}assets/images/supprimer.png" alt="" style="width: 20px; height: 20px;"></button></td>
            </tr>
        `);
        toggleRemoveButtons()
        index_autre++;
    });

    $('#article_table_upd').on('click', '.remove-article-upd', function () {
        $(this).closest('tr').remove();
        const $table = $('#article_table_upd');
        const $rows = $table.find('tbody tr');
        if ($rows.length == 0) {
            $("#article_titre_upd").hide()
        }
        toggleRemoveButtons()
        updateTotalArticleUpd();
        updateTotalAutreUpd();
        calculerSommeTotaleUpd()
    });

    $('#autre_table_upd').on('click', '.remove-autre-upd', function () {
        $(this).closest('tr').remove();
        const $table = $('#autre_table_upd');
        const $rows = $table.find('tbody tr');
        if ($rows.length == 0) {
            $("#autre_titre_upd").hide()
        }
        toggleRemoveButtons()
        updateTotalArticleUpd();
        updateTotalAutreUpd();
        calculerSommeTotaleUpd()
    });


    $('#article_table_upd').on('input', 'input', function () {
        updateTotalArticleUpd();
        calculerSommeTotaleUpd()
    });
    $('#autre_table_upd').on('input', 'input', function () {
        updateTotalAutreUpd();
        calculerSommeTotaleUpd()
    });

    $('#modal_view_bc').on('shown.bs.modal', function () {
        initialiseSelect2ModalByClass("select-search", "modal_view_bc");
        $('input[id^="prix_unitaire_upd"]').each(function () {
            formatMontant(this);
        });
        $('input[id^="montant_upd"]').each(function () {
            formatMontant(this);
        });
        InputDateForm("date_upd")
    });

    $('#modal_view_bc .modal-body').on('scroll', function () {
        $('.select2-container--open').each(function () {
            let selectId = $(this).prev('select').attr('id');
            if (selectId) {
                $('#' + selectId).select2('close');
                setTimeout(function () {
                    $('#' + selectId).select2('open');
                }, 200);
            }
        });
    });

    //Initialisation et réinitialisation du modal une fois qu'il est entièrement visible
    // $('#modal_view_bc').on('shown.bs.modal', function () {
    //     $(this).removeAttr('aria-hidden');
    //     // Petit délai pour que Bootstrap termine les transitions
    //     setTimeout(() => {
    //         // Désactive le focus actif résiduel
    //         document.activeElement.blur();

    //         // Réinitialisation du formulaire
    //         $("#bc_form")[0].reset();
    //         $('#article_table_upd tbody tr:not(:first)').remove();
    //         $('#autre_table_upd tbody').empty();

    //         $('#article_table_upd tbody tr:first').find('select, input').each(function () {
    //             if ($(this).is('select')) {
    //                 $(this).val("").trigger("change");
    //             } else {
    //                 $(this).val("");
    //             }
    //         });

    //         $('#article_table_upd tbody tr:first .total-article-upd').text("0");
    //         $("#autre_titre_upd").hide();

    //         index_article = 1;
    //         index_autre = 0;

    //         calculerSommeTotale();
    //         toggleRemoveButtons();

    //         // Réinitialisation sécurisée des Select2
    //         $('#modal_view_bc .select-search').each(function () {
    //             // Détruire le précédent Select2 s'il existe
    //             if ($(this).hasClass("select2-hidden-accessible")) {
    //                 $(this).select2('destroy');
    //             }
    //         });
    //         // Réinitialisation complète
    //         initialiseSelect2ModalByClass("select-search", "modal_view_bc");
    //         $('.select2-container--open .select2-search__field').focus();
    //     }, 100); // délai minimum recommandé
    // });
});


// Gestion globale des modals pour éviter l'erreur aria-hidden
$(document).on('hide.bs.modal', '.modal', function () {
    // 1️⃣ Enlever le focus de tout élément actif
    if (document.activeElement) {
        document.activeElement.blur();
    }

    // 2️⃣ Fermer proprement tous les Select2 du modal
    $(this).find('select.select2-hidden-accessible').each(function () {
        if ($(this).data('select2')) { // Vérifie que Select2 est initialisé
            $(this).select2('close');
        }
    });
});


$('#modal_view_bc .modal-body').on('scroll', function () {
    $('.select2-container--open').each(function () {
        let selectId = $(this).prev('select').attr('id');
        if (selectId) {
            $('#' + selectId).select2('close');
            setTimeout(function () {
                $('#' + selectId).select2('open');
            }, 200);
        }
    });
});


function updateTotalArticleUpd() {
    $('#article_table_upd tbody tr').each(function () {
        // const qte = parseFloat($(this).find('input[name$="[quantite_upd]"]').val().replace(/\s+/g, '')) || 0;
        // const prix = parseFloat($(this).find('input[name$="[prix_unitaire_upd]"]').val().replace(/\s+/g, '')) || 0;
        // if (isNaN(qte)) qte = 0;
        // if (isNaN(prix)) prix = 0;
        let qteVal = $(this).find('input[name$="[quantite_upd]"]').val() || "0";
        let prixVal = $(this).find('input[name$="[prix_unitaire_upd]"]').val() || "0";

        const qte = parseFloat(qteVal.replace(/\s+/g, '')) || 0;
        const prix = parseFloat(prixVal.replace(/\s+/g, '')) || 0;

        let total = qte * prix;
        // Si total est un entier, pas besoin de .00
        if (Number.isInteger(total)) {
            total = total.toString();
        } else {
            total = total.toFixed(2);
        }
        $(this).find('.total-article-upd').text(total);
    });
}

function updateTotalAutreUpd() {
    $('#autre_table_upd tbody tr').each(function () {
        const montant = parseFloat($(this).find('input[name$="[montant_upd]"]').val().replace(/\s+/g, '')) || 0;
        if (isNaN(montant)) montant = 0;
        let montant_total = montant;
        // Si total est un entier, pas besoin de .00
        if (Number.isInteger(montant_total)) {
            montant_total = montant_total.toString();
        } else {
            montant_total = montant_total.toFixed(2);
        }
        $(this).find('.total-autre-upd').text(montant_total);
    });
}

$('#type_client_upd').on('change', function () {
    loaderContent('modal_view_bc')
    $('select[name^="article"][name$="[article_id_upd]"]').each(function () {
        prixAutoUpd(this);
    });
    $.ajax({
        url: urlProject + 'Devis/getAllClient',
        type: 'POST',
        dataType: 'json',
        data: {
            type_client: $('#type_client_upd').val()
        },
        success: function (data) {
            setDataSelect('client_upd', data)
            stopLoaderContent('modal_view_bc')
        }
    })
})

$('#date_upd').on('change', function () {
    loaderContent('modal_view_bc')
    $('select[name^="article"][name$="[article_id_upd]"]').each(function () {
        prixAutoUpd(this);
    });
    stopLoaderContent('modal_view_bc')
})

function getClient() {
    loaderContent('modal_view_bc')
    $.ajax({
        url: urlProject + 'Devis/getAllClient',
        type: 'POST',
        dataType: 'json',
        data: {
            type_client: $('#type_client_upd').val()
        },
        success: function (data) {
            setDataSelected('client_upd', data, 'client_id_upd')
            stopLoaderContent('modal_view_bc')
        }
    })
}

function prixAutoUpd(e) {
    let article_id = $(e).val();
    console.log(article_id);
    if (!article_id || article_id === "0") return;    // Si article_id est vide ou 0, on sort
    let type_client = $('#type_client_upd').val()
    let date_bc = $('#date_upd').val()
    loaderContent('modal_view_bc')
    $.ajax({
        url: urlProject + 'BonDeCommande/getPrixArticle',
        type: 'POST',
        dataType: 'json',
        data: {
            article_id: article_id,
            type_client: type_client,
            date: date_bc
        },
        success: function (prix) {
            let name = e.name;
            let match = name.match(/article\[(\d+)\]\[article_id_upd\]/);
            if (match) {
                let index = match[1];
                $('input[name="article[' + index + '][prix_unitaire_upd]"]').val(prix).trigger('input').trigger('change').trigger('keyup');
                updateTotalArticleUpd();
                updateTotalAutreUpd();
                calculerSommeTotaleUpd()
            }
            stopLoaderContent('modal_view_bc')
        },
        error: function (xhr, status, error) {
            stopLoaderContent('modal_view_bc')
        }
    })
}

function calculerSommeTotaleUpd() {
    let sommeArticle = 0;
    let sommeAutre = 0;
    $('.total-article-upd').each(function () {
        let val = parseFloat($(this).text().replace(/\s+/g, '')) || 0;
        sommeArticle += val;
    });
    $('.total-autre-upd').each(function () {
        let val = parseFloat($(this).text().replace(/\s+/g, '')) || 0;
        sommeAutre += val;
    });
    let totalAvantRemise = sommeArticle + sommeAutre;
    //Affichage formaté
    $("#total_upd").val(formatNombre(totalAvantRemise));
    calculerRemiseUpd()
    formatMontantNotForInput()
}

// Fonction formatNombre fiable pour garder les décimales
function formatNombre(n) {
    return Number.parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function calculerRemiseUpd() {
    loaderContent('modal_view_bc')
    let remiseElem = $("#remise_upd");
    let totalElem = $("#total_upd");
    // Nettoyage des champs
    let valeurRemise = (remiseElem.val() || "0").toString().replace(/\s+/g, '').replace(',', '.');
    let totalStr = (totalElem.val() || "0").toString().replace(/\s+/g, '').replace(',', '.');
    // Conversion en nombres
    let tauxRemise = parseFloat(valeurRemise) || 0;
    let total = parseFloat(totalStr) || 0;
    // Calcul
    let montantRemise = total * (tauxRemise / 100);
    let montantApresRemise = total - montantRemise;
    // Affichage
    $("#net_a_payer_upd").val(formatNombre(montantApresRemise));
    stopLoaderContent('modal_view_bc')
}
;

function toggleRemoveButtons() {
    loaderContent('modal_view_bc')
    const articleRows = $('#article_table_upd tbody tr').length;
    const autreRows = $('#autre_table_upd tbody tr').length;
    if (articleRows <= 1 && autreRows === 0) {    // Cacher le bouton "remove-article-upd" si 1 ligne article et 0 ligne autre
        $('.remove-article-upd').hide();
    } else {
        $('.remove-article-upd').show();
    }
    if (autreRows <= 1 && articleRows === 0) {    // Cacher le bouton "remove-autre-upd" si 1 ligne autre et 0 ligne article
        $('.remove-autre-upd').hide();
    } else {
        $('.remove-autre-upd').show();
    }
    stopLoaderContent('modal_view_bc')
}

function loadVehiculeUpd() {
    $("#type_vehicule_upd").typeahead({
        source: function (query, process) {
            return $.post(
                "Devis/getVehicule",
                {
                    c: query,
                },
                function (data) {
                    data = $.parseJSON(data);
                    console.log();
                    return process(data);
                }
            );
        },
    });
}

function ajoutAsteriskUpd() {
    // Si type_vehicule est vide → astérisque sur immatriculation_label
    if ($('#type_vehicule_upd').val().trim() !== "") {
        $('#immatriculation_upd_label').html('Immatriculation <span class="text-bold text-danger-600">*</span>');
    } else {
        $('#immatriculation_upd_label').html('Immatriculation');
    }

    // Si immatriculation est vide → astérisque sur vehicule_label
    if ($('#immatriculation_upd').val().trim() !== "") {
        $('#vehicule_label').html('Type de véhicule <span class="text-bold text-danger-600">*</span>');
    } else {
        $('#vehicule_label').html('Type de véhicule');
    }

    if (($('#immatriculation_upd').val().trim() === "") && ($('#type_vehicule_upd').val().trim() === "")) {
        $('#vehicule_label').html('Type de véhicule');
        $('#immatriculation_upd_label').html('Immatriculation');
    }
}
$('#type_vehicule_upd, #immatriculation_upd').on('keyup', ajoutAsteriskUpd);

$('#bc_form_upd').on('submit', function (e) {
    e.preventDefault(); // empêche le rechargement
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".modifier-bc-content", ".obligatoire")
    if (isValid == true) {
        let form = this; // 🔹 référence au formulaire
        Swal.fire({
            title: "Modification",
            html: "Voulez-vous vraiment procéder à la modification?",
            icon: "warning",
            showConfirmButton: true,
            showCancelButton: true, confirmButtonText: 'Oui',
            cancelButtonText: 'Annuler',
        }).then(function (result) {
            if (result.isConfirmed) {
                loaderContent('modal_view_bc')
                $("#save_upd").prop("disabled", true);
                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: $(form).serialize(),
                    dataType: 'json',
                    success: function (res) {
                        stopLoaderContent('modal_view_bc')
                        if (res == 1) {
                            Swal.fire({
                                title: "Modification",
                                html: "Modification faite avec succès.",
                                icon: "success",
                                showConfirmButton: true,
                            }).then(function (result) {
                                if (result.isConfirmed) {
                                    window.location.href = urlProject + "BonDeCommande";
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
                        stopLoaderContent('modal_view_bc')
                        $("#save_upd").prop("disabled", false);
                    }
                });
            }
        });
    }
    stopLoaderContent('modal_view_bc')
});