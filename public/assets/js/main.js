$(function () {
    notification()
    initDataTable()
    if ($('#theme_value').val() == 1) {
        $('#mode-setting-btn').trigger('click');
    }
    if (isMobileOrTabletView()) {
        $('#menu_value').val(0)
    }
    if ($('#menu_value').val() == 1) {
        $('#vertical-menu-btn').trigger('click');
    }
});

function initDataTable() {
    const tableId = '#datatable-buttons';

    if ($.fn.DataTable.isDataTable(tableId)) {
        $(tableId).DataTable().destroy();
    }
    $(tableId).DataTable({
        responsive: true,
        order: []
    });
}

/*** Affiche/cache les mots de passe ***/
document.querySelectorAll(".toggle-password").forEach(function (toggle) {
    toggle.addEventListener("click", function () {
        const targetSelector = this.getAttribute("data-target");
        const passwordField = document.querySelector(targetSelector);

        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        // Changer l'icône
        const icon = this.querySelector("i");
        icon.classList.toggle("fa-eye");
        icon.classList.toggle("fa-eye-slash");
    });
});


function checkObligatoire(parentClass, childClass) {
    let isValid = true;
    $(`${parentClass} ${childClass}`).each(function () {
        const input = $(this);
        const value = input.val();
        const id = input.attr("id");
        const errorLabel = $(`#${id}-error`);
        let name = id.replace(/_upd$/, "");
        name = name.replace(/_id$/, "")

        if (!value || String(value).trim() === "") {
            // errorLabel.html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ ' + name + ' est obligatoire.</span>');
            errorLabel.html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
            isValid = false;
        } else {
            errorLabel.text(""); // Clear previous error
        }
    });
    return isValid;
}

function getFormDataFromParentClass(parentClass) {
    let data = {};
    $(`${parentClass} input[name], ${parentClass} select[name], ${parentClass} textarea[name]`).each(function () {
        const name = $(this).attr("name");
        const type = $(this).attr("type");
        const tag = this.tagName.toLowerCase();
        const baseName = name.replace(/_upd$/, "");
        const val = $(this).val();

        if (type === "checkbox") {
            data[baseName] = $(this).is(":checked") ? 1 : 0;
        } else if (type === "radio") {
            if ($(this).is(":checked")) {
                data[baseName] = $(this).is(":checked") ? 1 : 0;
            }
        } else {
            data[baseName] = typeof val === 'string' ? val.trim() : val;
        }
    });
    return data;
}

/**
 * Mettre à jour la valeur de #theme_value
 */
$('#mode-setting-btn').click(function (e) {
    if (!e.originalEvent) { // Ignorer si le clic n'est pas un clic utilisateur (clic simulé)
        return;
    }

    var currentValue = parseInt($('#theme_value').val(), 10);
    var newValue = currentValue === 0 ? 1 : 0;
    $('#theme_value').val(newValue);

    $.ajax({
        url: urlProject + "User/changeTheme",
        type: "POST",
        data: { data: $('#theme_value').val() },
        success: function (res) {

        },
        error: function (xhr, status, error) {
            Swal.fire({
                title: "Erreur",
                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
});

/**
 * Mettre à jour la valeur de #menu_value
 */
$('#vertical-menu-btn').click(function (e) {
    if (!e.originalEvent || isMobileOrTabletView()) { // Ignorer si le clic n'est pas un clic utilisateur (clic simulé)
        // $('#menu_value').val(0)
        return;
    }

    var currentValue = parseInt($('#menu_value').val(), 10);
    var newValue = currentValue === 0 ? 1 : 0;
    $('#menu_value').val(newValue);

    $.ajax({
        url: urlProject + "User/changeMenu",
        type: "POST",
        data: { data: $('#menu_value').val() },
        success: function (res) {

        },
        error: function (xhr, status, error) {
            Swal.fire({
                title: "Erreur",
                html: "Erreur dans la base de données. Merci de réessayer plus tard.",
                icon: "error",
                timer: 2000,
                showConfirmButton: false,
            });
        }
    });
});

function isMobileOrTabletView() {
    return (
        /Mobi|Android|iPhone|iPad|iPod|BlackBerry|Windows Phone/i.test(navigator.userAgent) ||
        window.innerWidth <= 992 // seuil à adapter selon ton design
    );
}

function inputPhoneNumber(e) {
    var v = $("#" + e.id).val()
    var cleaned = v.replace(/[^0-9+\-\/ ]/g, '');
    $("#" + e.id).val(cleaned);
}

function modalMpd() {
    loaderContent('main')
    $("#modal_mdp_user").modal("show");
    $("#mdp").val("");
    $("#new_mdp").val("");
    $("#conf_mdp").val("");
    $(".validation-error-label").html("");
    stopLoaderContent('main')
}

function changeMdp() {
    isValid = checkObligatoire(".mpd-content", ".obligatoire")
    if (isValid == true) {
        if ($("#new_mdp").val() != $("#conf_mdp").val()) {
            Swal.fire({
                title: "Nouveau mot de passe!",
                html: "Veuillez bien vérifier le nouveau mot de passe et la confirmation de mot depasse, svp.",
                icon: "warning",
                showConfirmButton: true,
            });
        } else if ($("#new_mdp").val().length <= 4) {
            Swal.fire({
                title: "Nouveau mot de passe!",
                html: "Le nouveau mot de passe doit comporter au moins 4 caractères.",
                icon: "warning",
                showConfirmButton: true,
            });
        } else {
            $("save_mdp").prop("disabled", true);
            let arr_data = getFormDataFromParentClass(".mpd-content")
            loaderContent('modal_mdp_user')
            $.ajax({
                url: urlProject + "User/changeMdp",
                type: "POST",
                data: { data: arr_data },
                success: function (res) {
                    stopLoaderContent('modal_mdp_user')
                    $("save_mdp").prop("disabled", false);
                    if (res == 1) {
                        Swal.fire({
                            title: "Changement mot de passe.",
                            html: "Le mot de passe a été changé avec succès.",
                            icon: "success",
                            showConfirmButton: true
                        }).then(function (result) {
                            if (result.isConfirmed) {
                                window.location.href = urlProject + "Login/logout";
                            }
                        });
                    } else if (res == 2) {
                        Swal.fire({
                            title: "Changement mot de passe.",
                            html: "Le mot de passe actuel est incorrect.",
                            icon: "warning",
                            showConfirmButton: true
                        })
                    }
                    else {
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
                    stopLoaderContent('modal_mdp_user')
                    $("save_mdp").prop("disabled", false);
                }
            });
        }
    }
}

function isFormatDateValideFr(e) {
    let idSelector = e.id;
    var dateString = $("#" + idSelector).val();
    if (dateString != "") {
        var regex = /^(\d{2})\/(\d{2})\/(\d{4})$/; // Vérifier si la chaîne de caractères est au format "jj/mm/aaaa"
        if (!regex.test(dateString) && $("#" + idSelector).val() != "") {
            Swal.fire({
                title: "Information",
                html: "Format de date invalide. Veuillez svp utiliser le format JJ/MM/AAAA (Ex : 01/01/2025).",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#21a89f",
            });
            $("#" + idSelector).val("");
            return false;
        }
        var [_, day, month, year] = dateString.match(regex);
        // Convertir les parties de la date en nombres
        day = parseInt(day, 10);
        month = parseInt(month, 10) - 1; // Mois est 0-indexé (janvier = 0, février = 1, etc.)
        year = parseInt(year, 10);
        var date = new Date(year, month, day); // Créer un objet Date avec la date fournie

        // Vérifier si l'année, le mois et le jour correspondent après la conversion (pour gérer les cas comme 31/02/2021)
        if (
            date.getFullYear() === year &&
            date.getMonth() === month &&
            date.getDate() === day
        ) {
            return true;
        } else {
            Swal.fire({
                title: "Information",
                html: "Date incorrecte, veuillez vérifier la date ou utiliser le calendrier du champ.",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
            });
            $("#" + idSelector).val("");
            return false;
        }
    }
}

function isFormatDateValideEn(e) {
    let idSelector = e.id;
    var dateString = $("#" + idSelector).val();

    if (dateString !== "") {
        var regex = /^(\d{4})-(\d{2})-(\d{2})$/; // Format "YYYY-MM-DD"

        if (!regex.test(dateString)) {
            Swal.fire({
                title: "Information",
                html: "Format de date invalide.",
                //  html:"Format de date invalide. Veuillez utiliser le format YYYY-MM-DD (Ex : 2025-01-01).",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
            });
            $("#" + idSelector).val("");
            return false;
        }

        var [_, year, month, day] = dateString.match(regex);
        year = parseInt(year, 10);
        month = parseInt(month, 10) - 1; // JavaScript: mois de 0 à 11
        day = parseInt(day, 10);

        var date = new Date(year, month, day);

        // Validation stricte : les composantes doivent rester identiques après conversion
        if (
            date.getFullYear() === year &&
            date.getMonth() === month &&
            date.getDate() === day
        ) {
            return true;
        } else {
            Swal.fire({
                title: "Information",
                html: "Date incorrecte, veuillez vérifier la date ou utiliser le calendrier du champ.",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
                confirmButtonColor: "#21a89f",
            });
            $("#" + idSelector).val("");
            return false;
        }
    }
}


function initializeDate() {
    $(".pickadate").pickadate({
        firstDay: 1,
        monthsFull: [
            "Janvier",
            "Février",
            "Mars",
            "Avril",
            "Mai",
            "Juin",
            "Juillet",
            "Août",
            "Septembre",
            "Octobre",
            "Novembre",
            "Décembre",
        ],
        weekdaysShort: ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"],
        today: "",
        clear: "",
        close: "Fermer",
        format: "dd/mm/yyyy",
        formatSubmit: "yyyy-mm-dd",
        editable: true,
        selectMonths: true,
        selectYears: 40,
    });
}

function numberOnly(e) {
    var v = $("#" + e.id).val()
    var cleaned = v.replace(/[^0-9]/g, '');
    $("#" + e.id).val(cleaned);
}

function numberDecimal(el) {
    try {
        // position du caret avant modification
        const selStart = el.selectionStart;
        const selEnd = el.selectionEnd;

        const raw = el.value;
        const hadTrailingSeparator = /[.,]$/.test(raw);

        // remplace toutes les virgules par des points (immédiatement)
        let v = raw.replace(/,/g, '.');

        // supprime tout sauf chiffres et points
        v = v.replace(/[^0-9.]/g, '');

        // ne garder qu'un seul point : la première occurrence
        const parts = v.split('.');
        if (parts.length > 1) {
            const intPart = parts.shift();
            // concatène le reste (pour supprimer les autres points)
            let decPart = parts.join('');
            // limite à 2 chiffres après la virgule
            decPart = decPart.substring(0, 2);
            // si l'utilisateur vient de taper '.' en fin et n'a pas encore saisi de décimales,
            // on autorise temporairement le '.' final (pour ne pas gêner la saisie)
            if (decPart.length === 0 && hadTrailingSeparator) {
                v = intPart + '.';
            } else if (decPart.length > 0) {
                v = intPart + '.' + decPart;
            } else {
                v = intPart; // pas de décimales
            }
        } else {
            // pas de point
            v = parts[0];
        }

        // calcule nouvelle position du caret pour la repositionner correctement
        // On prend la différence de longueur entre nouvelle valeur et ancienne (après normalisation des virgules)
        const normalizedRaw = raw.replace(/,/g, '.').replace(/[^0-9.]/g, '');
        const delta = v.length - normalizedRaw.length;

        el.value = v;

        // repositionne le caret (limité aux bornes valides)
        const newPos = Math.max(0, Math.min(v.length, (selEnd != null ? selEnd : selStart) + delta));
        el.setSelectionRange(newPos, newPos);
    } catch (err) {
        // si l'input n'autorise pas selectionStart (ex: certains éléments), on se contente de réaffecter la valeur
        el.value = el.value.replace(/,/g, '.').replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1').replace(/^(\d+)(\.(\d{0,2}))?.*$/, (m, a, b) => a + (b ? b.substring(0, 3) : ''));
    }
}


function initializeSelect() {
    $(".select-search").select2({
        allowClear: false,
        width: "100%",
        language: {
            searching: function () {
                return "Tapez un texte...";
            },
            noResults: function () {
                return "Aucun résultat trouvé";
            },
        },
    });
}

function initialiseSelect2Modal(id, id_modal) {
    $('#' + id).select2({
        allowClear: false,
        width: "100%",
        dropdownParent: $('#' + id_modal),
        language: {
            searching: function () {
                return "Tapez un texte...";
            },
            noResults: function () {
                return "Aucun résultat trouvé";
            },
        },
    }).on("select2:open", function () {
        // Ajouter la loupe uniquement si elle n'existe pas encore
        let searchBox = $('.select2-container--open .select2-search');
        if (searchBox.find(".fa-search").length === 0) {
            searchBox.css("position", "relative");
            searchBox.prepend(`
                <i class="fa fa-search" 
                   style="position:absolute; left:15px; top:50%; transform:translateY(-50%); color:#888;"></i>
            `);
            searchBox.find("input").css("padding-left", "25px");
        }
    });
}

function initialiseSelect2ModalByClass(classSelect, id_modal) {
    $('.' + classSelect).each(function () {
        // Empêche la double initialisation
        if (!$(this).hasClass("select2-hidden-accessible")) {
            $(this).select2({
                allowClear: false,
                width: "100%",
                dropdownParent: $('#' + id_modal),
                language: {
                    searching: function () {
                        return "Tapez un texte...";
                    },
                    noResults: function () {
                        return "Aucun résultat trouvé";
                    },
                },
            });
        }
    });
}

function setDataSelect(selector, result) {
    $('#' + selector).html('')
    let option = '<option value=""></option>'
    for (let item of result) {
        option += '<option value="' + item.id + '">' + item.text + '</option>'
    }
    $('#' + selector).html(option).trigger('change')
}

function setDataSelected(selector, result, id_to_select) {
    $('#' + selector).html('')
    let m = $('#' + id_to_select).val()
    let selected = ''
    let option = '<option value=""></option>'
    for (let item of result) {
        if (m == item.id) {
            selected = 'selected = "selected"'
        } else {
            selected = ''
        }
        option += '<option value="' + item.id + '" ' + selected + '>' + item.text + '</option>'
    }
    $('#' + selector).html(option).trigger('change')
}

function initializeDuallistBox(id) {
    if (!$('#' + id).data('dualListBoxInitialized')) {
        $('#' + id).bootstrapDualListbox({
            selectorMinimalHeight: 150,
            filterPlaceHolder: "Rechercher",
            nonSelectedListLabel: "Disponibles",
            selectedListLabel: "Sélectionnées",
            infoTextEmpty: "Liste vide",
            infoText: "Afficher tout {0}",
            filterTextClear: "Afficher tout",
            infoTextFiltered: '<span class="label label-warning">Filtré</span> {0} à {1}',
            showSelectAll: true
        });
        $('#' + id).data('dualListBoxInitialized', true); // Pour éviter double init
    }
}

function compareTwoDate(input_debut, input_fin, name_debut, name_fin) {
    var date1 = $("#" + input_debut).val(); // Valeur de la première date
    var date2 = $('#' + input_fin).val(); // Valeur de la deuxième date
    if (date1 !== "" && date2 !== "") {
        // Vérifier si les deux champs sont remplis
        var date1Obj = new Date(date1.split("/").reverse().join("/")); // Convertir la première date en objet Date
        var date2Obj = new Date(date2.split("/").reverse().join("/")); // Convertir la deuxième date en objet Date
        if (date1Obj > date2Obj) {
            // Comparer les dates
            // return false; // La date 1 est supérieure à la date 2
            Swal.fire({
                title: "Information",
                html:
                    "La <b>" +
                    name_fin +
                    "</b> doit être supérieure à la <b>" +
                    name_debut +
                    "</b>. Veuillez vérifier et resaisir la <b>" +
                    name_fin +
                    "</b>, svp.",
                icon: "warning",
                showConfirmButton: true,
                confirmButtonText: "OK",
            });
            $("#" + input_fin).val("");
            return false;
        } else {
            return true; // La date 1 n'est pas supérieure à la date 2
        }
    }
}

/** pour le pourcentage */
function formatNumberInput(e) {
    var value = $(e).val();  // Utilisation de `$(e)` pour accéder à l'élément directement
    value = value.replace(',', '.'); // Remplacer la virgule par un point
    if (value === '') {     // On ne fait rien ici, pour permettre la saisie
        return;
    }
    let regex = /^(\d{1,2}(\.\d{0,2})?|100)$/; // Valider la valeur : 0-100 avec 2 décimales
    // Si la valeur est valide et dans la plage 0-100
    if (regex.test(value) && parseFloat(value) <= 100) {
        $(e).val(value); // Mettre à jour la valeur de l'input
    } else {
        // Si la valeur n'est pas valide, ne pas changer l'input
        $(e).val(value.slice(0, -1)); // Enlever le dernier caractère en cas de valeur invalide
    }
}

function formatMontant(e) {
    let $el = $(e);
    let value = $el.val();
    value = value.replace(/[^0-9.,]/g, '');  // supprimer tout sauf chiffres, virgule et point
    if (value.startsWith(',') || value.startsWith('.')) {   // enlever virgule ou point au début
        value = value.substring(1);
    }
    value = value.replace(',', '.');    // remplacer la première virgule par un point
    let parts = value.split('.'); // ne garder qu'un seul point
    parts[0] = parts[0].replace(/^0+/, '') || '0'; // enlever zéros en trop
    if (parts.length > 2) {
        parts = [parts[0], parts[1]]; // garder une seule partie décimale
    }
    if (parts[1]) { // limiter à 2 chiffres après le point
        parts[1] = parts[1].substring(0, 2);
    }
    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ' ');    // ajouter séparateur de milliers sur la partie entière
    $el.val(parts.join('.'));    // remettre dans l'input
}

function formatMontantNotForInput() {
    $('.montant').each(function () {
        let txt = $(this).text()
            .replace(/Ar/i, '')
            .replace(/\s+/g, '')       // Supprimer tous les espaces
            .replace(',', '.')         // Remplacer la virgule par un point
            .trim();

        let num = parseFloat(txt);
        if (!isNaN(num)) {
            let intPart = Math.trunc(num).toString();
            let decPart = num.toFixed(2).split('.')[1];

            // Format millier avec espace
            let formattedInt = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');

            // Si nombre entier, pas de décimale affichée
            let final = (num % 1 === 0)
                ? `${formattedInt} <i>Ar</i>`
                : `${formattedInt}.${decPart} <i>Ar</i>`;

            $(this).html(final);
        }
    });
}


function setZeroIfEmpty(e) {
    if ($(e).val().trim() === '') {
        $(e).val('0');
    }
}

function loaderContent(id) {
    $("#" + id).block({
        message: `
      <div style="padding: 30px 0; text-align:center;">
        <div class="double-ring-spinner"></div>
        <div style="margin-top:12px; font-size:16px; color:#fff;">Chargement en cours...</div>
      </div>
    `,
        overlayCSS: {
            backgroundColor: "#1B2024",
            opacity: 0.3,
            cursor: "wait",
        },
        css: {
            border: 0,
            padding: 0,
            backgroundColor: "transparent",
            color: "#fff",
        },
    });
}


function stopLoaderContent(id) {
    $("#" + id).unblock();
}

function numberAndSpaceOnly(e) {
    var v = $("#" + e.id).val()
    var cleaned = v.replace(/[^0-9 ]/g, '');
    $("#" + e.id).val(cleaned);
}

function majuscule(e) {
    var v = $("#" + e.id).val()
    var cleaned = v.toUpperCase();
    $("#" + e.id).val(cleaned);
}

/** si l'un est reseingé, l'autre deviendra obligatoire */
function checkRequiredFields(input1, input2) {
    const val1 = $('#' + input1).val().trim();
    const val2 = $('#' + input2).val().trim();

    if (val1 !== "" && val2 === "") {
        $('#' + input2).addClass("obligatoire");
    } else if (val2 !== "" && val1 === "") {
        $('#' + input1).addClass("obligatoire");
    } else {
        $('#' + input1).removeClass("obligatoire");
        $('#' + input2).removeClass("obligatoire");
    }
}

/** vérification quantité par article en stock */
function checkQuantite(e, upd = null) {
    var quantite_saisie = parseFloat($('#' + e.id).val());
    var index = e.id.replace(/\D/g, "")
    if (upd == null) {
        article_id = $('#article_id' + index).val()
    } else {
        article_id = $('#article_id_upd' + index).val()
    }
    $.ajax({
        url: urlProject + 'Tools/getQuantite',
        type: 'POST',
        dataType: 'json',
        data: {
            article_id: article_id,
        },
        success: function (quantite_stock) {
            if (quantite_saisie > quantite_stock) {
                Swal.fire({
                    title: "Information",
                    html: "<b>Stock insuffisant</b>, il ne reste plus que <b>" + quantite_stock + "</b> en stock pour cet article.",
                    icon: "warning",
                    showConfirmButton: true,
                });
                $('#' + e.id).val("")
            }
        }
    })
}

function notification() {
    $.ajax({
        url: urlProject + "Notification/notification",
        type: "GET",
        dataType: "json",
        success: function (data) {
            var nb_article = data[0].length;
            var nb_client = data[1].length;
            var html = "";
            if (nb_article != 0 || nb_client != 0) {
                $("#nb_notif").text(nb_article + nb_client); // maj du badge
            }
            if (nb_article != 0) {
                data[0].forEach(function (item) {
                    html += `
                        <a href="#!" class="text-reset notification-item">
                            <div class="d-flex" onclick="viewArticleNotif('${item.id}')">
                                <div class="flex-shrink-0 me-3">
                                    <img src="data:image/png;base64,${item.photo}" 
                                         class="rounded-circle avatar-sm" alt="user-pic">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">${item.reference} - ${item.libelle}</h6>
                                    <div class="font-size-13 text-muted">
                                        <p class="mb-1">Stock au seuil minimum :<b> ${item.seuil_min}</b><br>
                                        Quantité en stock : <b>${item.quantite}</b> </p>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </a>`;
                });
            } if (nb_client != 0) {
                data[1].forEach(function (item_c) {
                    html += `
                        <a href="#!" class="text-reset notification-item">
                            <div class="d-flex" onclick="viewFactureNotif('${item_c.id}')">
                                <div class="flex-shrink-0 me-3">                                  
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1"> Facture FA-${item_c.num_facture.toString().padStart(4, "0")} du client ${item_c.nom}</h6>
                                    <div class="font-size-13 text-muted">
                                        <p class="mb-1">Il reste <b>${Number(item_c.restant_du).toLocaleString('fr-FR')} Ar</b> non payé.</p>
                                    </div>
                                </div>
                            </div>
                        </a>`;
                });
            } if (html == "") {
                html = '<p class="p-3 mb-0 text-center text-muted">Aucune notification</p>';
            }
            $("#text_content").html(html);
        },
        error: function () {
            console.error("Erreur lors du chargement des notifications");
        }
    });
}

setInterval(notification, 60000);

function viewArticleNotif(id) {
    var action = 'voir';
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
            }
        }
    });
}

function viewFactureNotif(id) {
    var action = 'view';
    loaderContent('main')
    const url = urlProject + `Facturation/viewOrDownloadPDF?id=${id}&action=${action}`;
    window.open(url, '_blank');
    stopLoaderContent('main')
}

function InputDateForm(id) {
    const input = document.getElementById(id);
    const currentValue = input.value.trim();
    let defaultDate;

    if (currentValue === "") {
        // Si le champ est vide → date du jour
        defaultDate = new Date();
    } else {
        // Si la valeur est au format dd/mm/yyyy → la convertir en Date
        const parts = currentValue.split("/");
        if (parts.length === 3) {
            const jour = parseInt(parts[0], 10);
            const mois = parseInt(parts[1], 10) - 1; // mois commence à 0
            const annee = parseInt(parts[2], 10);
            defaultDate = new Date(annee, mois, jour);
        } else {
            // fallback si format incorrect
            defaultDate = new Date();
        }
    }

    flatpickr("#" + id, {
        dateFormat: "d/m/Y",  // affichage format français
        defaultDate: defaultDate,
    });
}


/*** Pour gérer le SPA */
$(document).ready(function () {
    function loadPage(url, addToHistory = true) {
        $('#content-page').fadeOut(100, function () {
            $('#content-page').html(loaderContentPage()).fadeIn(100);
        });
        $.ajax({
            url: url,
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function (data) {
                $('#content-page').fadeOut(100, function () {
                    $('#content-page').html(data).fadeIn(100, function () {
                        // Ici le fadeIn est terminé, le DOM est prêt et visible
                        $('#content-page').find('script').each(function () {
                            $.globalEval(this.text || this.textContent || this.innerHTML || '');
                        });

                        initDataTable();
                        setTimeout(function () {
                            activateMenuByUrl();
                        }, 50);
                    });

                    // Mettre à jour le titre si présent
                    var newTitle = $('#ajax-title').data('title');
                    if (newTitle) {
                        $('.page-title-box h4').text(newTitle);
                    }
                });

                if (addToHistory) {
                    history.pushState({
                        url: url
                    }, '', url);
                }
            },
            error: function () {
                $('#content-page').html('<div class="alert alert-danger text-center mt-3">Erreur de chargement</div>');
            }
        });
    }

    // expose globalement
    window.loadPage = loadPage;

    $(document).on('click', 'a.nav-link, a.menu-link', function (e) {
        e.preventDefault();
        const url = $(this).attr('href');
        if (url && !url.startsWith('#')) {
            loadPage(url);
        }
    });

    window.onpopstate = function (event) {
        if (event.state && event.state.url) {
            loadPage(event.state.url, false);
        }
    };
});

function loaderContentPage() {
    return `
    <div style="
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center; /* centre verticalement */
        align-items: center;     /* centre horizontalement */
        height: 60vh;           /* prend toute la hauteur de l'écran */
        width: 100%;             /* prend toute la largeur */
        text-align: center;
        background-color: transparent;
    ">
        <div style="
            display: inline-block;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 6px solid #A9A9A9 ;
            border-color: #A9A9A9  transparent #A9A9A9  transparent;
            animation: spin 1.2s linear infinite;
            margin-bottom: 12px;
            background-color: transparent;
        "></div>
        <div style="font-size:16px; color:#A9A9A9 ;">Chargement en cours...</div>
    </div>
    <style>
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    `;
}
/*** /Pour gérer le SPA */


function activateMenuByUrl() {
    let currentUrl = window.location.origin + window.location.pathname;
    // ou window.location.href si tu veux inclure les paramètres GET

    // 1. Supprimer toutes les classes d’activation
    $('#side-menu a').removeClass('active');
    $('#side-menu li').removeClass('mm-active');
    $('#side-menu ul').removeClass('mm-show');

    // 2. Trouver le lien correspondant
    let $activeLink = $('#side-menu a[href="' + currentUrl + '"]');

    if ($activeLink.length) {

        // Ajouter la classe active sur le lien
        $activeLink.addClass('active');

        // 3. Activer les <li> parents
        $activeLink.closest('li').addClass('mm-active');

        // 4. Ouvrir sa section parente
        $activeLink.closest('ul').addClass('mm-show');

        // 5. Activer aussi le parent supérieur (le menu principal)
        $activeLink.closest('ul').closest('li').addClass('mm-active');
    }
}
