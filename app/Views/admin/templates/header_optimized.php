<!DOCTYPE html>
<html lang="en" class="light-style layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-theme="theme-default" data-assets-path="<?= base_url() ?>/public/dashboard/optimized/" data-template="vertical-menu-template">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    
    <title><?= isset($pageTitle) ? $pageTitle . ' | ' : '' ?>Employee Management System</title>
    <meta name="description" content="<?= isset($metaDescription) ? $metaDescription : 'Employee Management System - Efficient and Modern' ?>" />
    
    <!-- Performance: DNS Prefetch for external resources -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.datatables.net">
    
    <!-- Performance: Preconnect for critical external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= base_url() ?>/public/dashboard/assets/img/favicon/favicon.ico" />
    
    <!-- Performance: Preload critical CSS -->
    <link rel="preload" href="<?= base_url() ?>/public/dashboard/optimized/critical/critical.min.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="<?= base_url() ?>/public/dashboard/optimized/critical/critical.min.css"></noscript>
    
    <!-- Performance: Critical CSS inlined -->
    <style>
        /* Critical CSS - Above the fold styles - Optimized for Core Web Vitals */
        body,html{margin:0;padding:0;font-family:system-ui,-apple-system,"Segoe UI",sans-serif;line-height:1.6}
        .layout-wrapper{min-height:100vh;display:flex;flex-direction:column}
        .layout-navbar{background:#fff;box-shadow:0 2px 6px rgba(67,89,113,.12);position:sticky;top:0;z-index:1001}
        .layout-menu{width:260px;background:#f8f9fa;border-right:1px solid #ddd}
        .layout-page{flex:1;padding:1.5rem}
        .content-wrapper{max-width:1400px;margin:0 auto}
        .card{background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(67,89,113,.12);margin-bottom:1.5rem}
        .card-header{padding:1.25rem;border-bottom:1px solid #f0f2f4;font-weight:600}
        .card-body{padding:1.25rem}
        .btn{display:inline-flex;align-items:center;padding:.5rem 1rem;border:1px solid transparent;border-radius:6px;font-size:.875rem;font-weight:500;text-decoration:none;transition:all .2s ease}
        .btn-primary{background:#7c3aed;color:#fff;border-color:#7c3aed}
        .btn-primary:hover{background:#6d28d9;border-color:#6d28d9}
        .table{width:100%;border-collapse:collapse;margin-bottom:1rem}
        .table th,.table td{padding:.75rem;text-align:left;border-bottom:1px solid #e5e7eb}
        .table th{background:#f9fafb;font-weight:600;color:#374151}
        .loading{opacity:.6;pointer-events:none}
        .sr-only{position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important}
        @media (max-width:768px){.layout-menu{width:100%;position:fixed;transform:translateX(-100%);transition:transform .3s ease}.layout-menu.show{transform:translateX(0)}.layout-page{padding:1rem}}
        
        /* Loading spinner for better perceived performance */
        .page-loader{position:fixed;top:0;left:0;width:100%;height:100%;background:#fff;z-index:9999;display:flex;align-items:center;justify-content:center}
        .page-loader.hidden{display:none}
        .spinner{width:40px;height:40px;border:4px solid #f3f3f3;border-top:4px solid #7c3aed;border-radius:50%;animation:spin 1s linear infinite}
        @keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}
    </style>
    
    <!-- Performance: Load non-critical fonts with swap -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Performance: Preload core JavaScript -->
    <link rel="preload" href="<?= base_url() ?>/public/dashboard/optimized/js/core.bundle.js" as="script">
    
    <!-- Performance: Core JavaScript loaded early -->
    <script>
        // Performance monitoring
        window.perfMetrics = {
            startTime: Date.now(),
            marks: {},
            mark: function(name) {
                this.marks[name] = Date.now() - this.startTime;
            },
            measure: function() {
                return this.marks;
            }
        };
        
        // Mark critical points
        window.perfMetrics.mark('HeadStart');
        
        // Critical error handling
        window.addEventListener('error', function(e) {
            console.error('JavaScript Error:', e.error);
            // You can send this to your logging service
        });
        
        // Performance observer for Core Web Vitals
        if ('PerformanceObserver' in window) {
            try {
                const observer = new PerformanceObserver((list) => {
                    list.getEntries().forEach((entry) => {
                        if (entry.entryType === 'navigation') {
                            window.perfMetrics.marks.domContentLoaded = entry.domContentLoadedEventEnd - entry.domContentLoadedEventStart;
                            window.perfMetrics.marks.loadComplete = entry.loadEventEnd - entry.loadEventStart;
                        }
                    });
                });
                observer.observe({ entryTypes: ['navigation'] });
            } catch (e) {
                // Ignore if not supported
            }
        }
    </script>
    
    <!-- Performance: Lazy load non-critical CSS -->
    <script>
        function loadCSS(href, before, media) {
            var doc = window.document;
            var ss = doc.createElement("link");
            var ref;
            if (before) {
                ref = before;
            } else {
                var refs = (doc.body || doc.getElementsByTagName("head")[0]).childNodes;
                ref = refs[refs.length - 1];
            }
            var sheets = doc.styleSheets;
            ss.rel = "stylesheet";
            ss.href = href;
            ss.media = "only x";
            function ready(cb) {
                if (doc.body) {
                    return cb();
                }
                setTimeout(function() {
                    ready(cb);
                });
            }
            ready(function() {
                ref.parentNode.insertBefore(ss, (before ? ref : ref.nextSibling));
            });
            var onloadcssdefined = function(cb) {
                var resolvedHref = ss.href;
                var i = sheets.length;
                while (i--) {
                    if (sheets[i].href === resolvedHref) {
                        return cb();
                    }
                }
                setTimeout(function() {
                    onloadcssdefined(cb);
                });
            };
            function loadCB() {
                if (ss.addEventListener) {
                    ss.removeEventListener("load", loadCB);
                }
                ss.media = media || "all";
            }
            if (ss.addEventListener) {
                ss.addEventListener("load", loadCB);
            }
            ss.onloadcssdefined = onloadcssdefined;
            onloadcssdefined(loadCB);
            return ss;
        }
        
        // Load non-critical CSS asynchronously
        window.addEventListener('DOMContentLoaded', function() {
            loadCSS('<?= base_url() ?>/public/dashboard/assets/vendor/fonts/materialdesignicons.css');
            loadCSS('<?= base_url() ?>/public/dashboard/assets/vendor/fonts/fontawesome.css');
            loadCSS('<?= base_url() ?>/public/dashboard/assets/vendor/css/rtl/core.css');
            loadCSS('<?= base_url() ?>/public/dashboard/assets/vendor/css/rtl/theme-default.css');
        });
    </script>
</head>

<body>
    <!-- Performance: Page loader for better perceived performance -->
    <div class="page-loader" id="pageLoader">
        <div class="spinner"></div>
    </div>
    
    <!-- Performance: Mark body start -->
    <script>window.perfMetrics.mark('BodyStart');</script>
    
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
                <div class="app-brand demo">
                    <a href="<?= base_url('/admin') ?>" class="app-brand-link">
                        <span class="app-brand-logo demo">
                            <!-- Your logo here -->
                        </span>
                        <span class="app-brand-text demo menu-text fw-bold ms-2">EMS</span>
                    </a>
                    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                        <i class="mdi mdi-close align-middle"></i>
                    </a>
                </div>
                
                <div class="menu-inner-shadow"></div>
                
                <!-- Navigation menu -->
                <ul class="menu-inner py-1">
                    <!-- Dashboard -->
                    <li class="menu-item <?= (isset($activeMenu) && $activeMenu == 'dashboard') ? 'active' : '' ?>">
                        <a href="<?= base_url('/admin') ?>" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-home-outline"></i>
                            <div data-i18n="Dashboards">Dashboard</div>
                        </a>
                    </li>
                    
                    <!-- Employees -->
                    <li class="menu-item <?= (isset($activeMenu) && ($activeMenu == 'employee' || $activeMenu == 'showemployee')) ? 'open' : '' ?>">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class="menu-icon tf-icons mdi mdi-account-outline"></i>
                            <div data-i18n="Employees">Employees</div>
                        </a>
                        <ul class="menu-sub">
                            <li class="menu-item <?= (isset($activeMenu) && $activeMenu == 'employee') ? 'active' : '' ?>">
                                <a href="<?= base_url('/employee') ?>" class="menu-link">
                                    <div data-i18n="List">All Employees</div>
                                </a>
                            </li>
                            <?php if (isset($_SESSION['employee_type']) && $_SESSION['employee_type'] == 'employee'): ?>
                            <li class="menu-item <?= (isset($activeMenu) && $activeMenu == 'showemployee') ? 'active' : '' ?>">
                                <a href="<?= base_url('/showemployee') ?>" class="menu-link">
                                    <div data-i18n="Profile">My Profile</div>
                                </a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    
                    <!-- Departments -->
                    <?php if (isset($_SESSION['employee_type']) && $_SESSION['employee_type'] == 'admin'): ?>
                    <li class="menu-item <?= (isset($activeMenu) && $activeMenu == 'department') ? 'active' : '' ?>">
                        <a href="<?= base_url('/department') ?>" class="menu-link">
                            <i class="menu-icon tf-icons mdi mdi-office-building-outline"></i>
                            <div data-i18n="Departments">Departments</div>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </aside>
            <!-- / Menu -->
            
            <!-- Layout container -->
            <div class="layout-page">
                <!-- Navbar -->
                <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                            <i class="mdi mdi-menu mdi-24px"></i>
                        </a>
                    </div>
                    
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                        <!-- Search -->
                        <div class="navbar-nav align-items-center">
                            <div class="nav-item d-flex align-items-center">
                                <i class="mdi mdi-magnify mdi-24px lh-0"></i>
                                <input type="text" class="form-control border-0 shadow-none bg-body" placeholder="Search..." aria-label="Search..." />
                            </div>
                        </div>
                        <!-- /Search -->
                        
                        <ul class="navbar-nav flex-row align-items-center ms-auto">
                            <!-- User -->
                            <li class="nav-item navbar-dropdown dropdown-user dropdown">
                                <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                    <div class="avatar avatar-online">
                                        <img src="<?= base_url() ?>/public/dashboard/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="#">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 me-3">
                                                    <div class="avatar avatar-online">
                                                        <img src="<?= base_url() ?>/public/dashboard/assets/img/avatars/1.png" alt class="w-px-40 h-auto rounded-circle" />
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <span class="fw-medium d-block"><?= $_SESSION['employee_name'] ?? 'User' ?></span>
                                                    <small class="text-muted"><?= ucfirst($_SESSION['employee_type'] ?? 'Employee') ?></small>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('/showemployee') ?>">
                                            <i class="mdi mdi-account-outline me-2"></i>
                                            <span class="align-middle">My Profile</span>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('/logout') ?>">
                                            <i class="mdi mdi-logout me-2"></i>
                                            <span class="align-middle">Log Out</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <!--/ User -->
                        </ul>
                    </div>
                </nav>
                <!-- / Navbar -->
                
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Performance: Mark content start -->
                    <script>window.perfMetrics.mark('ContentStart');</script>