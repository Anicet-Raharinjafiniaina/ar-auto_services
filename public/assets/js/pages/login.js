$(function () {
    $('#alert_login').hide()
    /*** icon show and hide mdp */
    const togglePassword = document.querySelector("#togglePassword");
    const passwordField = document.querySelector("#mdp");

    togglePassword.addEventListener("click", function () {
        // Change type du champ password entre 'password' et 'text'
        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        // Changer l'icône (œil ouvert ou œil fermé)
        this.querySelector("i").classList.toggle("fa-eye");
        this.querySelector("i").classList.toggle("fa-eye-slash");
    });
})

$(document).on('keydown', function (e) {
    if (e.key === 'Enter') {
        $('#btn_login').trigger('click');
    }
});

function checkUser() {
    $('#alert_login').hide()
    $(".validation-error-label").html("");
    isValid = true;
    if ($('#login').val().length === 0) {
        $('#login-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    }
    if ($('#mdp').val() == "") {
        $('#mdp-error').html('<i class= "fa fa-exclamation-circle"> <span class="text-danger font-italic">Ce champ est obligatoire.</span>');
        isValid = false;
    }
    if (isValid) {
        $("#mode-setting-btn").prop("disabled", true);
        loaderContent('main')
        $.ajax({
            url: urlProject + "Login/logIn",
            type: "POST",
            data: {
                login: $("#login").val().trim(),
                mdp: $("#mdp").val().trim()
            },
            success: function (res) {
                stopLoaderContent('main')
                if (res == 1) {
                    window.location.href = urlProject + "Acceuil";
                } else if (res == 0) {
                    $('#alert_login').show();
                    $("#mdp").val("")
                    $("#save_upd").prop("disabled", true);
                } else if (res == 2) {
                    $('#alert_login').show();
                    $('#alert_login').html("Le compte a été désactivé.");
                    $("#save_upd").prop("disabled", true);
                }
                $("#mode-setting-btn").prop("disabled", true);
            }
        });
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