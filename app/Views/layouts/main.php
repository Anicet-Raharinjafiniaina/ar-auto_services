<!DOCTYPE html>
<html>
<?php
helper('menu');
$arr_menu = getMenu();
?>

<head>
    <meta charset="utf-8" />
    <title>AR Auto services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href='<?= base_url("assets/images/logo/images.png") ?>'>
    <link href='<?= base_url("assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css") ?>' rel="stylesheet" type="text/css" />
    <!-- DataTables -->
    <link href='<?= base_url("assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css") ?>' rel="stylesheet" type="text/css" />

    <link href='<?= base_url("assets/libs/sweetalert2/sweetalert2.min.css") ?>' rel="stylesheet" type="text/css" />

    <!-- Responsive datatable examples -->
    <link href='<?= base_url("assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css") ?>' rel="stylesheet" type="text/css" />
    <!-- preloader css -->
    <link rel="stylesheet" href='<?= base_url("assets/css/preloader.min.css") ?>' type="text/css" />

    <link href='<?= base_url("assets/libs/select2/select2.min.css") ?>' rel="stylesheet" type="text/css">

    <!-- Bootstrap Css -->
    <link href='<?= base_url("assets/css/bootstrap.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />

    <!-- duallistbox Css -->
    <link href='<?= base_url("assets/libs/duallistbox/duallistbox.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />

    <!-- Icons Css -->
    <link href='<?= base_url("assets/css/icons.min.css") ?>' rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href='<?= base_url("assets/css/app.min.css") ?>' id="app-style" rel="stylesheet" type="text/css" />
    <link href='<?= base_url("assets/css/style.css") ?>' id="app-style" rel="stylesheet" type="text/css" />

    <?= $this->renderSection('link') ?>
</head>

<style>
    .menu-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .menu-hover:hover {
        transform: scale(1.05);
        /* box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2); */
        box-shadow:
            0 -2px 4px rgba(0, 0, 0, 0.1),
            0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .image-upload>input {
        display: none;
    }

    .image-upload img {
        width: 150px;
        height: 150px;
        cursor: pointer;
        object-fit: cover;
        border-radius: 5px;
        border: 2px solid #ddd;
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

<body data-topbar="dark">

    <!-- <body data-layout="horizontal"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href='<?= base_url("Acceuil") ?>' class="logo logo-dark">
                            <span class="logo-sm">
                                <img src='<?= base_url("assets/images/logo/images.png") ?>' alt="" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src='<?= base_url("assets/images/logo/images.png") ?>' alt="" height="24"> <span class="logo-txt">AR</span>
                            </span>
                        </a>

                        <a href='<?= base_url("Acceuil") ?>' class="logo logo-light">
                            <span class="logo-sm">
                                <img src='<?= base_url("assets/images/logo/images.png") ?>' alt="" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src='<?= base_url("assets/images/logo/images.png") ?>' alt="" height="24"> <span class="">AR Auto-services</span>
                            </span>
                        </a>
                    </div>

                    <div> <input type="hidden" id="menu_value" value="<?= session()->get('menu') ?? 0 ?>">
                        <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                            <i class="fa fa-fw fa-bars"></i>
                        </button>
                    </div>


                    <!-- App name-->
                    <div class="position-relative"> </div>

                </div>


                <div class="d-flex">
                    <div class="dropdown d-inline-block" id="notif_content">
                        <button type="button" class="btn header-item noti-icon position-relative" id="page-header-notifications-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i data-feather="bell" class="icon-lg"></i>
                            <span class="badge bg-danger rounded-pill" id="nb_notif"></span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications-dropdown">
                            <div data-simplebar style="max-height: 380px;">
                                <a href="#!" class="text-reset notification-item">
                                    <div class="d-flex">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2 text-center">Notifications</h6>
                                            <div class="font-size-13 text-muted">
                                                <p class="mb-1" id="text_content"></p>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="dropdown  d-sm-inline-block">
                        <input type="hidden" id="theme_value" value="<?= session()->get('theme') ?? 0 ?>">
                        <button type="button" class="btn header-item" id="mode-setting-btn">
                            <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                            <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user"></i>
                            <span class="d-none d-xl-inline-block ms-1 fw-medium"><?= session()->get('login') . " - " . session()->get('nom') ?? "" ?></span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <div class="d-flex align-items-center px-3 py-2 d-xl-none">
                                <i class="mdi mdi-account font-size-16 me-2"></i>
                                <span class="fw-medium"><i><u>
                                            <?= session()->get('login') . " - " . session()->get('nom') ?? "" ?>
                                    </i></u> </span>
                            </div>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" onclick="modalMpd()"><i class="mdi mdi-lock font-size-16 align-middle me-1"></i> Changer le mot de passe</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="<?= base_url("/Login/logout") ?>"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Se déconnecter</a>
                        </div>
                    </div>

                </div>
            </div>
        </header>

        <!-- ========== Left Sidebar Start ========== -->
        <div class="vertical-menu">
            <div data-simplebar class="h-100">
                <!--- Sidemenu -->
                <div id="sidebar-menu">
                    <!-- Left Menu Start -->
                    <ul class="metismenu list-unstyled" id="side-menu">
                        <li class="menu-title" data-key="t-menu">Menu</li>
                        <?php foreach ($arr_menu as $key_section => $section) : ?>
                            <li>
                                <a href="javascript: void(0);" class="has-arrow menu-hover">
                                    <img src="data:image/png;base64,<?= $section[0]->image ?>" style="width:25px; padding-bottom:10px;">
                                    <span><?= $key_section ?></span>
                                </a>
                                <ul class="sub-menu menu-hover" aria-expanded="false">
                                    <?php foreach ($section as $key_page => $value_page) : ?>
                                        <li>
                                            <a href="<?= base_url("$value_page->lien") ?>" class="menu-hover nav-link" key="t-products"><?= $value_page->page ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <!-- Sidebar -->
            </div>
        </div>
        <!-- Left Sidebar End -->



        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->

        <div class="main-content" id="main">
            <div class="page-content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 id="titre_page" class="mb-sm-0 font-size-18"><?= esc($titre ?? '') ?></h4>
                            </div>
                        </div>
                    </div>
                    <div id="content-page" class="container-fluid bg-light p-3">
                        <?= $this->renderSection('content') ?>
                    </div>
                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->

            <!-- changement mdp  -->
            <div id=" modal_mdp_user" class="modal fade">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="myModalLabel">Changement mot de passe</h5>
                            <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <form class="form-validate-jquery mpd-content">
                                <div class="form-group">
                                    <label>Actuel mot de passe <span class="text-bold text-danger-600">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control obligatoire" id="mdp" name="mdp" placeholder="Entrez votre mot de passe">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#mdp">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <label id="mdp-error" class="validation-error-label" for="mdp"></label>
                                </div>

                                <div class="form-group">
                                    <label>Nouveau mot de passe <span class="text-bold text-danger-600">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control obligatoire" id="new_mdp" name="new_mdp" placeholder="Entrez le nouveau mot de passe">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#new_mdp">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <label id="new_mdp-error" class="validation-error-label" for="new_mdp"></label>
                                </div>

                                <div class="form-group">
                                    <label>Confirmation nouveau mot de passe <span class="text-bold text-danger-600">*</span></label>
                                    <div class="input-group">
                                        <input type="password" class="form-control obligatoire" id="conf_mdp" name="conf_mdp" placeholder="Confirmer le nouveau mot de passe">
                                        <div class="input-group-append">
                                            <span class="input-group-text toggle-password" data-target="#conf_mdp">
                                                <i class="fas fa-eye"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <label id="conf_mdp-error" class="validation-error-label" for="conf_mdp"></label>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-success btn-xs btn-sm" style="background-color:#21a89f;" id="save_mdp" onclick="changeMdp()">Enregistrer</button>
                                </div>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
            <!-- /changement mdp -->


            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © KME
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="#!" class="text-decoration-underline">KME</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

        <!-- Visualiser article -->
        <div id="modal_view_article" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="title">Détail de l'article</h5>
                        <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="content-article"></div>
                </div>
            </div>
        </div>
        <!-- /Visualiser article -->

        <!-- Visualiser facture_notif -->
        <div id="modal_view_facture_notif" class="modal fade">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="title">Détail de la facture</h5>
                        <button type="button" class="btn-close float-right" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" id="content-facture-notif"></div>
                </div>
            </div>
        </div>
        <!-- /Visualiser facture_notif -->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src='<?= base_url("assets/libs/jquery/jquery.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/jquery/blockui.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/bootstrap/js/bootstrap.bundle.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/metismenu/metisMenu.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/simplebar/simplebar.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/node-waves/waves.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/feather-icons/feather.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/node-waves/waves.min.js") ?>'></script>
    <script src='<?= base_url("assets/js/bases_pages/feather-icon.init.js") ?>'></script>
    <script src='<?= base_url("assets/js/bases_pages/fontawesome.init.js") ?>'></script>
    <!-- pace js -->
    <script src='<?= base_url("assets/libs/pace-js/pace.min.js") ?>'></script>

    <!-- Required datatable js -->
    <script src='<?= base_url("assets/libs/datatables.net/js/jquery.dataTables.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js") ?>'></script>
    <!-- Buttons examples -->
    <script src='<?= base_url("assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/jszip/jszip.min.js") ?>'></script>

    <!-- Responsive examples -->
    <script src='<?= base_url("assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js") ?>'></script>

    <!-- Datatable init js -->
    <script src='<?= base_url("assets/js/datatable/datatables.init.js") ?>'></script>

    <script type="text/javascript" src='<?= base_url("assets/libs/select2/select2.min.js") ?>'></script>

    <!-- Sweet Alerts js -->
    <script src='<?= base_url("assets/libs/sweetalert2/sweetalert2.min.js") ?>'></script>
    <!-- Sweet alert init js-->
    <script src='<?= base_url("assets/js/bases_pages/sweetalert.init.js") ?>'></script>

    <script src=' <?= base_url("assets/js/app.js") ?>'></script>
    <script src='<?= base_url("assets/js/main.js") ?>'></script>

    <script>
        var urlProject = "<?= base_url(); ?>";
    </script>

    <?= $this->renderSection('script') ?>
</body>

</html>