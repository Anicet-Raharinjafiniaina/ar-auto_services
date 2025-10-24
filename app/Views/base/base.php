<!doctype html>
<html lang="en">


<!-- Mirrored from themesdesign.in/dason-node/layouts/default/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 20 Feb 2023 13:36:46 GMT -->

<head>

    <meta charset="utf-8" />
    <title>Bienvenue</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href='<?= base_url("assets/images/favicon.ico") ?>'>

    <!-- preloader css -->
    <link rel="stylesheet" href='<?= base_url("assets/css/preloader.min.css") ?>' type="text/css" />

    <!-- Bootstrap Css -->
    <link href='<?= base_url("assets/css/bootstrap.min.css") ?>' id="bootstrap-style" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href='<?= base_url("assets/css/icons.min.css") ?>' rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href='<?= base_url("assets/css/app.min.css") ?>' id="app-style" rel="stylesheet" type="text/css" />

</head>

<body data-topbar="dark">

    <!-- <body data-layout="horizontal"> -->

    <!-- Begin page -->
    <div id="layout-wrapper">
        <header id="page-topbar">
            <div class="navbar-header">
                <div class="d-flex">
                    <!-- LOGO -->
                    <div class="navbar-brand-box">
                        <a href="index.html" class="logo logo-dark">
                            <span class="logo-sm">
                                <img src="assets/images/logo-sm.svg" alt="" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo-sm.svg" alt="" height="24"> <span class="logo-txt">Dason</span>
                            </span>
                        </a>

                        <a href="index.html" class="logo logo-light">
                            <span class="logo-sm">
                                <img src="assets/images/logo-sm.svg" alt="" height="30">
                            </span>
                            <span class="logo-lg">
                                <img src="assets/images/logo-sm.svg" alt="" height="24"> <span class="logo-txt">Dason</span>
                            </span>
                        </a>
                    </div>

                    <button type="button" class="btn btn-sm px-3 font-size-16 header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <!-- App name-->
                    <div class="position-relative"> </div>

                </div>

                <div class="d-flex">
                    <div class="dropdown  d-sm-inline-block">
                        <button type="button" class="btn header-item" id="mode-setting-btn">
                            <i data-feather="moon" class="icon-lg layout-mode-dark"></i>
                            <i data-feather="sun" class="icon-lg layout-mode-light"></i>
                        </button>
                    </div>

                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn header-item bg-soft-light border-start border-end" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <img class="rounded-circle header-profile-user" src="assets/images/users/avatar-1.jpg"
                                alt="Header Avatar">
                            <span class="d-none d-xl-inline-block ms-1 fw-medium">Paul K.</span>
                            <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <a class="dropdown-item" href="apps-contacts-profile.html"><i class="mdi mdi-face-profile font-size-16 align-middle me-1"></i> Profile</a>
                            <a class="dropdown-item" href="auth-lock-screen.html"><i class="mdi mdi-lock font-size-16 align-middle me-1"></i> Lock screen</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="auth-logout.html"><i class="mdi mdi-logout font-size-16 align-middle me-1"></i> Logout</a>
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
                                <a href="javascript: void(0);" class="has-arrow">
                                    <i class="<?= $section[0]->icone_section ?? 'fas fa-question-circle' ?>"></i>
                                    <span data-key="t-ecommerce"><?= $key_section ?></span>
                                </a>
                                <ul class="sub-menu" aria-expanded="false">
                                    <?php foreach ($section as $key_page => $value_page) : ?>
                                        <li>
                                            <a href="<?= base_url('Home') ?>" key="t-products"><?= $value_page->page ?>
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
        <div class="main-content">

            <div class="page-content">
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                                <h4 class="mb-sm-0 font-size-18">Starter Page</h4>

                                <div class="page-title-right">
                                    <ol class="breadcrumb m-0">
                                        <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                        <li class="breadcrumb-item active">Starter Page</li>
                                    </ol>
                                </div>

                            </div>
                        </div>
                    </div>
                    <!-- end page title -->

                </div> <!-- container-fluid -->
            </div>
            <!-- End Page-content -->


            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-sm-6">
                            <script>
                                document.write(new Date().getFullYear())
                            </script> © Dason.
                        </div>
                        <div class="col-sm-6">
                            <div class="text-sm-end d-none d-sm-block">
                                Design & Develop by <a href="#!" class="text-decoration-underline">Themesbrand</a>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- JAVASCRIPT -->
    <script src='<?= base_url("assets/libs/jquery/jquery.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/bootstrap/js/bootstrap.bundle.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/metismenu/metisMenu.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/simplebar/simplebar.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/node-waves/waves.min.js") ?>'></script>
    <script src='<?= base_url("assets/libs/feather-icons/feather.min.js") ?>'></script>
    <script src='<?= base_url("assets/js/bases_pages/feather-icon.init.js") ?>'></script>
    <script src='<?= base_url("assets/js/bases_pages/fontawesome.init.js") ?>'></script>
    <!-- pace js -->
    <script src='<?= base_url("assets/libs/pace-js/pace.min.js") ?>'></script>

    <script src='<?= base_url("assets/js/app.js") ?>'></script>
    <script src='<?= base_url("assets/js/main.js") ?>'></script>

</body>

<!-- Mirrored from themesdesign.in/dason-node/layouts/default/pages-starter.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 20 Feb 2023 13:36:46 GMT -->

</html>