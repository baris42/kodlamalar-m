<?php

//include the user class, pass in the database connection
require_once 'includes/config.php';



//giriş yapılmış mı ?

if( !$user->is_logged_in() ){ header('Location: index.php'); exit(); }

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully featured admin theme which can be used to build CRM, CMS, etc.">
    <meta name="author" content="Coderthemes">

    <link rel="shortcut icon" href="assets/images/favicon_1.ico">

    <title><?php echo SITEADI; ?> || Yönetim Paneli </title>

    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/plugins/morris/morris.css">
    <link href="assets/plugins/custombox/css/custombox.css" rel="stylesheet">
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/core.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/components.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/pages.css" rel="stylesheet" type="text/css" />
    <link href="assets/css/responsive.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/dropzone.css" />


    <!-- HTML5 Shiv and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

    <script src="assets/js/modernizr.min.js"></script>

</head>


<body class="fixed-left">

<!-- Begin page -->
<div id="wrapper">

    <!-- Top Bar Start -->
    <div class="topbar">

        <!-- LOGO -->
        <div class="topbar-left">
            <div class="text-center">
               <a href="index.php" class="logo"><i class="icon-magnet icon-c-logo"></i><span>Yönetim<i class="md
               md-album"></i>Paneli</span></a>
                <!-- Image Logo here -->
                <!-- <a href="index.html" class="logo">
               <i class="icon-c-logo"> <img src="assets/images/logo_sm.png" height="42"/> </i>
                <span><img src="assets/images/logo_light.png" height="20"/></span>
                </a> -->
            </div>
        </div>

        <!-- Button mobile view to collapse sidebar menu -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container">
                <div class="">
                    <div class="pull-left">
                        <button class="button-menu-mobile open-left waves-effect waves-light">
                            <i class="md md-menu"></i>
                        </button>
                        <span class="clearfix"></span>
                    </div>



                    <form role="search" class="navbar-left app-search pull-left hidden-xs">
                        <input type="text" placeholder="Search..." class="form-control">
                        <a href=""><i class="fa fa-search"></i></a>
                    </form>


                    <ul class="nav navbar-nav navbar-right pull-right">

                        <li class="hidden-xs">
                            <a href="#" id="btn-fullscreen" class="waves-effect waves-light"><i class="icon-size-fullscreen"></i></a>
                        </li>

                        <li class="dropdown top-menu-item-xs">
                            <a href="" class="dropdown-toggle profile waves-effect waves-light"
                               data-toggle="dropdown" aria-expanded="true"><img src="assets/images/users/avatar-2.jpg"
                               alt="user-img" class="img-circle"> </a>
                            <ul class="dropdown-menu">
                                <li><a href="javascript:void(0)"><i class="ti-user m-r-10 text-custom"></i>
                                        Yönetici Ayarları</a></li>


                                <li class="divider"></li>
                                <li><a href="logout.php"><i class="ti-power-off m-r-10 text-danger"></i> Çıkış
                                        Yap</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
            </div>
        </div>
    </div>
    <!-- Top Bar End -->


    <!-- ========== Left Sidebar Start ========== -->

    <div class="left side-menu">
        <div class="sidebar-inner slimscrollleft">
            <!--- Divider -->
            <div id="sidebar-menu">
                <ul>

                    <li class="text-muted menu-title">Menü</li>

                    <li class="has_sub">
                        <a href="anasayfa.php" class="waves-effect"><i class="ti-home"></i> <span> Anasayfa
                            </span> <span class="menu-arrow"></span></a>

                    </li>

                    <li class="has_sub">
                        <a href="slider.php" class="waves-effect"><i class="ti-paint-bucket"></i> <span>
                                Slider Yönetimi </span> <span class="menu-arrow"></span> </a>

                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-spray"></i> <span> Ürün
                                Yönetimi</span> <span class="menu-arrow"></span> </a>
                        <ul class="list-unstyled">

                            <li><a href="urun_kategori.php">Kategoriler</a></li>
                            <li><a href="urun.php">Ürünler</a></li>

                        </ul>
                    </li>
                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-light-bulb"></i><span
                                    class="menu-arrow"></span><span> Tasarım Yönetimi</span> </a>
                        <ul class="list-unstyled">

                            <li><a href="tasarim_kategori.php">Kategoriler</a></li>
                            <li><a href="tasarim.php">Tasarım</a></li>

                        </ul>
                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-pencil-alt"></i><span> Dosyalar
                            </span> <span class="menu-arrow"></span></a>

                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-menu-alt"></i><span>Site
                                Ayarları
                            </span> <span class="menu-arrow"></span></a>

                    </li>

                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-bar-chart"></i><span
                                    class="menu-arrow"></span><span> Ödeme Ayarları </span></a>

                    </li>

                    <li class="text-muted menu-title">Sipariş Sistemi</li>



                    <li class="has_sub">
                        <a href="javascript:void(0);" class="waves-effect"><i class="ti-shopping-cart"></i><span
                                    class="menu-arrow"></span><span> Siparişler</span></a>

                    </li>

                </ul>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
    <!-- Left Sidebar End -->



    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->