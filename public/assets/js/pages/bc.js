$(function () {
    toggleRemoveButtons()
    formatMontantNotForInput()
    initialiseSelect2Modal("article_id0", "modal_ajout_bc")
    $("#autre_titre").hide()
    let index_article = 1;
    let index_autre = 0;
    $('#add_article').on('click', function () {
        $("#article_titre").show()
        $('#article_table tbody').append(`
            <tr>
                 <td>
                <select class="select select-search obligatoire" data-placeholder="Choisir un article..." name="article[${index_article}][article_id]" id="article_id${index_article}" style="width: 100%;" onchange="prixAuto(this)">
                    <option value=""></option>
                    ${articleOptions}
                </select>
                  <label id="article_id${index_article}-error" class="validation-error-label" for="article_id${index_article}"></label>
            </td>
                <td><input class="form-control input-xs text-end obligatoire" id="quantite${index_article}" name="article[${index_article}][quantite]" type="text" onkeyup="formatMontant(this);checkQuantite(this)">
                   <label id="quantite${index_article}-error" class="validation-error-label" for="quantite${index_article}"></label>
                </td>
                <td><input class="form-control input-xs text-end obligatoire" id="prix_unitaire${index_article}" name="article[${index_article}][prix_unitaire]" type="text" onkeyup="formatMontant(this)">
                   <label id="prix_unitaire${index_article}-error" class="validation-error-label" for="prix_unitaire${index_article}"></label>
                </td>
                <td class="total-article montant text-center" name="article[${index_article}][total]">0</td>
                <td style="text-align:right"> <button type="button" style="margin-right:1.5em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-article" id="del_article" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="${urlProject}assets/images/supprimer.png" alt="" style="width: 20px; height: 20px;"></button></td>
            </tr>
        `);
        initialiseSelect2Modal("article_id" + index_article, "modal_ajout_bc")
        toggleRemoveButtons()
        index_article++;
    });

    $('#add_autre').on('click', function () {
        $("#autre_titre").show()
        $('#autre_table tbody').append(`
            <tr>
                <td><input class="form-control input-xs obligatoire" id="description${index_autre}" name="autre[${index_autre}][description]">
                   <label id="description${index_autre}-error" class="validation-error-label" for="description${index_autre}"></label>
                </td>
                <td><input class="form-control input-xs" name="autre[${index_autre}][commentaire]" type="hidden"></td>
                <td><input class="form-control input-xs text-end obligatoire" id="montant${index_autre}" name="autre[${index_autre}][montant]" type="text" value="0" onkeyup="formatMontant(this)">
                   <label id="montant${index_autre}-error" class="validation-error-label" for="montant${index_autre}"></label>
                </td>
                <td class="total-autre montant text-center" name="autre[${index_autre}][total]">0</td>
                <td style="text-align:right"> <button type="button" style="margin-right:1em;background:transparent" class="btn btn-icon btn-rounded btn-xs remove-autre" id="del_autre" data-popup="tooltip" title="Supprimer" data-placement="bottom"><img src="${urlProject}assets/images/supprimer.png" alt="" style="width: 20px; height: 20px;"></button></td>
            </tr>
        `);
        toggleRemoveButtons()
        index_autre++;
    });

    $('#article_table').on('click', '.remove-article', function () {
        $(this).closest('tr').remove();
        const $table = $('#article_table');
        const $rows = $table.find('tbody tr');
        if ($rows.length == 0) {
            $("#article_titre").hide()
        }
        toggleRemoveButtons()
    });

    $('#autre_table').on('click', '.remove-autre', function () {
        $(this).closest('tr').remove();
        const $table = $('#autre_table');
        const $rows = $table.find('tbody tr');
        if ($rows.length == 0) {
            $("#autre_titre").hide()
        }
        toggleRemoveButtons()
    });


    $('#article_table').on('input', 'input', function () {
        updateTotalArticle();
        calculerSommeTotale()
    });
    $('#autre_table').on('input', 'input', function () {
        updateTotalAutre();
        calculerSommeTotale()
    });

    $("#btn-create-bc").click(function () {
        $(".validation-error-label").html("");
        $("#modal_ajout_bc").modal("show");
        $('#type_client, #client, #article_id').select2({
            dropdownParent: $('#modal_ajout_bc .modal-body')
        });
        InputDateForm("date")
        loadVehicule()
    });

    $('#modal_ajout_bc .modal-body').on('scroll', function () {
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
    $('#modal_ajout_bc').on('shown.bs.modal', function () {
        $(this).removeAttr('aria-hidden');
        // Petit délai pour que Bootstrap termine les transitions
        setTimeout(() => {
            // Désactive le focus actif résiduel
            document.activeElement.blur();

            // Réinitialisation du formulaire
            $("#bc_form")[0].reset();
            $('#article_table tbody tr:not(:first)').remove();
            $('#autre_table tbody').empty();

            $('#article_table tbody tr:first').find('select, input').each(function () {
                if ($(this).is('select')) {
                    $(this).val("").trigger("change");
                } else {
                    $(this).val("");
                }
            });

            $('#article_table tbody tr:first .total-article').text("0");
            $("#autre_titre").hide();

            index_article = 1;
            index_autre = 0;

            calculerSommeTotale();
            toggleRemoveButtons();

            // Réinitialisation sécurisée des Select2
            $('#modal_ajout_bc .select-search').each(function () {
                // Détruire le précédent Select2 s'il existe
                if ($(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2('destroy');
                }
            });
            // Réinitialisation complète
            initialiseSelect2ModalByClass("select-search", "modal_ajout_bc");
            $('.select2-container--open .select2-search__field').focus();
        }, 100); // délai minimum recommandé
    });
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


$('#modal_ajout_bc .modal-body').on('scroll', function () {
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


function updateTotalArticle() {
    $('#article_table tbody tr').each(function () {
        const qte = parseFloat($(this).find('input[name$="[quantite]"]').val().replace(/\s+/g, '')) || 0;
        const prix = parseFloat($(this).find('input[name$="[prix_unitaire]"]').val().replace(/\s+/g, '')) || 0;
        if (isNaN(qte)) qte = 0;
        if (isNaN(prix)) prix = 0;
        let total = qte * prix;
        // Si total est un entier, pas besoin de .00
        if (Number.isInteger(total)) {
            total = total.toString();
        } else {
            total = total.toFixed(2);
        }
        $(this).find('.total-article').text(total);
    });
}

function updateTotalAutre() {
    $('#autre_table tbody tr').each(function () {
        const montant = parseFloat($(this).find('input[name$="[montant]"]').val().replace(/\s+/g, '')) || 0;
        if (isNaN(montant)) montant = 0;
        let montant_total = montant;
        // Si total est un entier, pas besoin de .00
        if (Number.isInteger(montant_total)) {
            montant_total = montant_total.toString();
        } else {
            montant_total = montant_total.toFixed(2);
        }
        $(this).find('.total-autre').text(montant_total);
    });
}

$('#type_client').on('change', function () {
    loaderContent('modal_ajout_bc')
    $('select[name^="article"][name$="[article_id]"]').each(function () {
        prixAuto(this);
    });
    $.ajax({
        url: urlProject + 'Devis/getAllClient',
        type: 'POST',
        dataType: 'json',
        data: {
            type_client: $('#type_client').val()
        },
        success: function (data) {
            setDataSelect('client', data)
            stopLoaderContent('modal_ajout_bc')
        }
    })
})

$('#date').on('change', function () {
    loaderContent('modal_ajout_bc')
    $('select[name^="article"][name$="[article_id]"]').each(function () {
        prixAuto(this);
    });
    stopLoaderContent('modal_ajout_bc')
})

function prixAuto(e) {
    let article_id = $(e).val();
    if (!article_id || article_id === "0") return;    // Si article_id est vide ou 0, on sort
    let type_client = $('#type_client').val()
    let date_bc = $('#date').val()
    loaderContent('modal_ajout_bc')
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
            let match = name.match(/article\[(\d+)\]\[article_id\]/);
            if (match) {
                let index = match[1];
                $('input[name="article[' + index + '][prix_unitaire]"]').val(prix).trigger('input').trigger('change').trigger('keyup');
                updateTotalArticle();
                updateTotalAutre();
                calculerSommeTotale()
            }
            stopLoaderContent('modal_ajout_bc')
        }
    })
}

function calculerSommeTotale() {
    let sommeArticle = 0;
    let sommeAutre = 0;
    $('.total-article').each(function () {
        let val = parseFloat($(this).text().replace(/\s+/g, '')) || 0;
        sommeArticle += val;
    });
    $('.total-autre').each(function () {
        let val = parseFloat($(this).text().replace(/\s+/g, '')) || 0;
        sommeAutre += val;
    });
    let totalAvantRemise = sommeArticle + sommeAutre;
    //Affichage formaté
    $("#total").val(formatNombre(totalAvantRemise));
    calculerRemise()
    formatMontantNotForInput()
}

// Fonction formatNombre fiable pour garder les décimales
function formatNombre(n) {
    return Number.parseFloat(n).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, " ");
}

function calculerRemise() {
    loaderContent('modal_ajout_bc')
    let remiseElem = $("#remise");
    let totalElem = $("#total");
    // Nettoyage des champs
    let valeurRemise = (remiseElem.val() || "0").toString().replace(/\s+/g, '').replace(',', '.');
    let totalStr = (totalElem.val() || "0").toString().replace(/\s+/g, '').replace(',', '.');
    // Conversion en nombres
    let tauxRemise = parseFloat(valeurRemise) || 0;
    let total = parseFloat(totalStr) || 0;
    // Calcul
    let montantRemise = total * (tauxRemise / 100);
    let montantApresRemise = total - montantRemise;
    console.log("montantApresRemise : " + montantApresRemise);
    // Affichage
    $("#net_a_payer").val(formatNombre(montantApresRemise));
    stopLoaderContent('modal_ajout_bc')
}
;

function toggleRemoveButtons() {
    loaderContent('modal_ajout_bc')
    const articleRows = $('#article_table tbody tr').length;
    const autreRows = $('#autre_table tbody tr').length;
    if (articleRows <= 1 && autreRows === 0) {    // Cacher le bouton "remove-article" si 1 ligne article et 0 ligne autre
        $('.remove-article').hide();
    } else {
        $('.remove-article').show();
    }
    if (autreRows <= 1 && articleRows === 0) {    // Cacher le bouton "remove-autre" si 1 ligne autre et 0 ligne article
        $('.remove-autre').hide();
    } else {
        $('.remove-autre').show();
    }
    stopLoaderContent('modal_ajout_bc')
}

$('#bc_form').on('submit', function (e) {
    e.preventDefault(); // empêche le rechargement
    $(".validation-error-label").html("");
    isValid = checkObligatoire(".bc-form", ".obligatoire")
    if (isValid == true) {
        loaderContent('modal_ajout_bc')
        $("#save").prop("disabled", true);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (res) {
                stopLoaderContent('modal_ajout_bc')
                if (res == 1) {
                    Swal.fire({
                        title: "Création",
                        html: "Le bon de commande a été créé avec succès",
                        icon: "success",
                        showConfirmButton: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            window.location.href = urlProject + "BonDeCommande";
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
                    $("#save").prop("disabled", false);
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
                stopLoaderContent('modal_ajout_bc')
                $("#save").prop("disabled", false);
            }
        });
    }
    stopLoaderContent('modal_ajout_bc')
});

function view(id, action) {
    var t = $("#l" + id).text();
    $("#content-bc").html("");
    loaderContent('main')
    $.ajax({
        url: urlProject + "BonDeCommande/getDetail",
        type: "POST",
        data: {
            id: id,
            action: action
        },
        success: function (res) {
            stopLoaderContent('main')
            $("#content-bc").html(res);
            $("#modal_view_bc").modal("show");
            $('#total_upd').trigger('change')
            $('#net_a_payer_upd').trigger('change')
            loadVehiculeUpd()
            if (action == "voir") {
                $("#div-upd-footer").css("display", "none");
                $("#title").html("Détail du bon de commande <b>" + t + "</b>");
            } else if (action == "upd") {
                $("#div-upd-footer").css("display", "block");
                $("#title").text("Modification du bon de commande");
            }
        },
    });
}

function deleteBc(id) {
    Swal.fire({
        title: "Voulez-vous vraiment supprimer ?",
        text: "La suppression de ce BC est irréversible !",
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
                url: urlProject + "BonDeCommande/deleteBc",
                data: { id: id },
                dataType: "json" // attend une réponse JSON (1 ou 0)
            }).then(response => {
                if (response === 1) {
                    stopLoaderContent('main')
                    return true;
                } else {
                    stopLoaderContent('main')
                    throw new Error("Erreur lors de la suppression.");
                }
            }).catch(error => {
                Swal.showValidationMessage(error.message);
                stopLoaderContent('main')
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
                stopLoaderContent('main')
                location.reload(true);
            });
        }
    });
}

function loadVehicule() {
    $("#type_vehicule").typeahead({
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

function ajoutAsterisk() {
    // Si type_vehicule est vide → astérisque sur immatriculation_label
    if ($('#type_vehicule').val().trim() !== "") {
        $('#immatriculation_label').html('Immatriculation <span class="text-bold text-danger-600">*</span>');
    } else {
        $('#immatriculation_label').html('Immatriculation');
    }

    // Si immatriculation est vide → astérisque sur vehicule_label
    if ($('#immatriculation').val().trim() !== "") {
        $('#vehicule_label').html('Type de véhicule <span class="text-bold text-danger-600">*</span>');
    } else {
        $('#vehicule_label').html('Type de véhicule');
    }

    if (($('#immatriculation').val().trim() === "") && ($('#type_vehicule').val().trim() === "")) {
        $('#vehicule_label').html('Type de véhicule');
        $('#immatriculation_label').html('Immatriculation');
    }
}
$('#type_vehicule, #immatriculation').on('keyup', ajoutAsterisk);