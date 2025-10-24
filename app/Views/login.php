<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentification</title>
    <!-- App favicon -->
    <link rel="shortcut icon" href='<?= base_url("assets/images/logo/images.png") ?>'>
    <link href='<?= base_url("assets/css/icons.min.css") ?>' rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/css/bootstrap.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />
</head>

<style>
    body {
        background-color: #f4f7fa;
        font-family: 'Roboto', sans-serif;
    }

    .login-container {
        min-height: 100vh;
        /* Hauteur minimale pour occuper toute la hauteur de l'écran */
        display: flex;
        justify-content: center;
        /* Centrer horizontalement */
        align-items: center;
        /* Centrer verticalement */
    }

    .card {
        width: 100%;
        max-width: 400px;
        /* Limiter la largeur de la carte */
        padding: 20px;
        text-align: center;
    }

    /*.card-header {
        background-color: #007bff;
        color: white;
        text-align: center;
        padding: 20px;
    }*/

    .logo {
        width: 50px;
        /* Taille du logo */
        height: auto;
        margin-bottom: 15px;
        /* Espacement sous le logo */
    }

    /* Ajustement pour que l'icône et l'input soient de la même hauteur */
    .input-group-text {
        height: calc(2.25rem + 2px);
        /* Hauteur identique à celle de l'input */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .input-group .form-control {
        height: calc(2.25rem + 2px);
        /* Hauteur identique pour le champ de saisie */
    }

    /* Ajout d'un petit ajustement pour aligner parfaitement l'icône */
    .input-group .input-group-text i {
        font-size: 1.2rem;
        /* Taille de l'icône */
    }
</style>

<body>
    <div class="login-container" id="main">
        <div class=" card shadow">
            <div class="card-header">
                <!-- Ajout du logo -->
                <img src='<?= base_url("assets/images/logo/images.png") ?>' alt="AR Auto services" class="logo">
                <h4>AR Auto services</h4>
            </div>
            <div class="card-body">
                <form>
                    <div class="form-group">
                        <div class="input-group">
                            <!-- Icône à gauche du champ de saisie -->
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                            <input type="text" class="form-control" id="login" placeholder="Entrez votre login" required>
                        </div>
                        <label id="login-error" class="validation-error-label" for="login"></label>
                    </div>
                    <br>
                    <div class="form-group">
                        <div class="input-group">
                            <!-- Icône à gauche du champ de saisie -->
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" class="form-control" id="mdp" placeholder="Entrez votre mot de passe">
                            <div class="input-group-append">
                                <!-- Icône d'œil pour montrer/cacher le mot de passe -->
                                <span class="input-group-text" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>
                        <label id="mdp-error" class="validation-error-label" for="mdp"></label>
                    </div>
                    <br>
                    <div class="alert alert-danger" id="alert_login" role="alert">
                        Login ou mot de passe incorrect.
                    </div>

                    <button type="button" class="btn btn-primary btn-block" id="btn_login" onclick="checkUser()">Se connecter</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        var urlProject = "<?= base_url(); ?>";
    </script>

    <script src='<?= base_url("assets/libs/jquery/jquery.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/jquery/blockui.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/bootstrap/js/bootstrap.bundle.min.js") ?>'> </script>
    <script src="assets/js/pages/login.js?d=<?= date('YmdHis') ?>"> </script>

</body>

</html>