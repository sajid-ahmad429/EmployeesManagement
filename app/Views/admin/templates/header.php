<!DOCTYPE html>

<html
  lang="en"
  class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?= base_url() ?>/public/dashboard/assets/"
  data-template="vertical-menu-template">
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard - Analytics | Materialize - Material Design HTML Admin Template</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>/public/dashboard/assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
      rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/fonts/materialdesignicons.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/fonts/fontawesome.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/fonts/flag-icons.css" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/node-waves/node-waves.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/css/rtl/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/css/rtl/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/typeahead-js/typeahead.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/apex-charts/apex-charts.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/swiper/swiper.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/select2/select2.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/libs/@form-validation/umd/styles/index.min.css" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/css/pages/cards-statistics.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/css/pages/cards-analytics.css" />
    <link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/assets/vendor/css/pages/page-profile.css" />
    <link href="https://cdn.datatables.net/v/bs5/jszip-2.5.0/dt-1.13.4/b-2.3.6/b-colvis-2.3.6/b-html5-2.3.6/b-print-2.3.6/cr-1.6.2/fc-4.2.2/fh-3.3.2/r-2.4.1/sc-2.1.1/sp-2.1.2/sl-1.6.2/sr-1.2.2/datatables.min.css" rel="stylesheet"/>
    
    <!-- Page CSS -->
    
    
    <!-- Helpers -->
    <script src="<?= base_url() ?>/public/dashboard/assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="<?= base_url() ?>/public/dashboard/assets/vendor/js/template-customizer.js"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?= base_url() ?>/public/dashboard/assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.19/dist/sweetalert2.all.min.js"></script>
  </head>

  <style>
    btn-group> :not(.btn-check:first-child)+.btn,
    .btn-group>.btn-group:not(:first-child) {
        margin-left: -1px;
    }

    div.dt-buttons {
        position: initial;
        /*  display: none !important;*/
    }

    #datable_companyList_wrapper .row {
        margin-top: 5px;
    }

    /* General form element error styling */
    form .errborder,
    form .errborder:before,
    form .errborder::before {
        border-width: 2px;
        border-color: #ff4d49 !important;
    }

    /* Form label error styling */
    form .form-label.errborder {
        border-width: 2px;
        border-color: #ff4d49;
        box-shadow: 0 0 0 2px rgba(255, 77, 73, 0.4) !important;
    }

    /* Select2 single select error styling */
    form select.errborder ~ .select2 .select2-selection {
        border-width: 2px;
        border-color: #ff4d49;
    }

    /* Select2 multiple select error styling */
    form select.errborder ~ .select2 .select2-selection--multiple {
        border-width: 2px;
        border-color: #ff4d49 !important;
    }

    /* Select picker button error styling */
    form select.selectpicker.errborder ~ .btn {
        border-width: 2px;
        border-color: #ff4d49 !important;
    }

    /* Floating labels error styling for select picker and select2 */
    form .form-floating:has(.selectpicker.errborder) label,
    form .form-floating:has(.select2.errborder) label {
        color: #ff4d49 !important;
    }

    /* Select2 single select error styling with errborder class */
    select.errborder + .select2-container--default.select2-container--focus .select2-selection,
    select.errborder + .select2-container--default.select2-container--open .select2-selection {
        border-width: 2px;
        border-color: #e21e1e !important;
        border-radius: 7px !important;
    }
    .table > :not(caption) > * > * {
    padding: 0.65rem 0.2rem !important;
}
    .errborder{    
        border: 2px solid #e21e1e !important;
        box-shadow: 1px 1px 1px #e29292;
    }
    blink {  
        -webkit-animation-name: blink; 
        -webkit-animation-iteration-count: infinite !important; 
        -webkit-animation-timing-function: cubic- 
            bezierr(1.0,0,0,1.0) !important;
        -webkit-animation-duration: 2s !important;
    }
    .select2-search--inline textarea{
        display: none;
    }
    .err_warning{
        font-weight: 550 !important;
        margin-top: 8px !important;
        color: #f26c6c;
        font-size: 15px;
        display: block;
        z-index: 100000;
        /*animation-name: blink ;*/
        animation-duration: 1s ;
        animation-timing-function: step-end ;
        animation-iteration-count: infinite ;
        animation-direction: alternate ;
    }

    @keyframes blink { 
        50% { border-color:#fff ; } 
    }
    .blink {
        -webkit-animation: blink 1.5s linear infinite;
        -moz-animation: blink 1.5s linear infinite;
        -ms-animation: blink 1.5s linear infinite;
        -o-animation: blink 1.5s linear infinite;
        animation: blink 1.5s linear infinite;
    }
    @-webkit-keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 1; }
        50.01% { opacity: 0; }
        100% { opacity: 0; }
    }
    @-moz-keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 1; }
        50.01% { opacity: 0; }
        100% { opacity: 0; }
    }
    @-ms-keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 1; }
        50.01% { opacity: 0; }
        100% { opacity: 0; }
    }
    @-o-keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 1; }
        50.01% { opacity: 0; }
        100% { opacity: 0; }
    }
    @keyframes blink {
        0% { opacity: 1; }
        50% { opacity: 1; }
        50.01% { opacity: 0; }
        100% { opacity: 0; }
    }
  </style>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">

      <?php echo view('admin/templates/sidebar'); ?>

      <div class="layout-page">
      <?php echo view('admin/templates/navbar'); ?>