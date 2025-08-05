                    <!-- Performance: Mark content end -->
                    <script>window.perfMetrics.mark('ContentEnd');</script>
                </div>
                <!-- / Content wrapper -->
                
                <!-- Footer -->
                <footer class="content-footer footer bg-footer-theme">
                    <div class="container-xxl">
                        <div class="footer-container d-flex align-items-center justify-content-between py-3 flex-md-row flex-column">
                            <div class="mb-2 mb-md-0">
                                ©
                                <script>
                                    document.write(new Date().getFullYear());
                                </script>
                                , Employee Management System
                            </div>
                            <div class="d-none d-lg-inline-block">
                                <a href="#" class="footer-link me-4">Support</a>
                                <a href="#" class="footer-link">Documentation</a>
                            </div>
                        </div>
                    </div>
                </footer>
                <!-- / Footer -->
                
                <div class="content-backdrop fade"></div>
            </div>
            <!-- / Layout page -->
        </div>
        
        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
        
        <!-- Drag Target Area To SlideIn Menu On Small Screens -->
        <div class="drag-target"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Performance: Core JavaScript Bundle (optimized) -->
    <script src="<?= base_url() ?>/public/dashboard/optimized/js/core.bundle.js"></script>
    
    <!-- Performance: Essential scripts with defer -->
    <script>
        // Base URL and configuration
        window.appConfig = {
            baseUrl: "<?= base_url() ?>",
            roleName: "<?= $_SESSION['employee_type'] ?? '' ?>",
            csrfToken: "<?= csrf_hash() ?>",
            employeeId: "<?= $_SESSION['employee_id'] ?? '' ?>"
        };
        
        // Performance: Lazy load heavy libraries
        window.loadDataTables = function() {
            if (!window.jQuery || !window.jQuery.fn.DataTable) {
                return import('<?= base_url() ?>/public/dashboard/optimized/js/datatables.lazy.js')
                    .then(() => {
                        console.log('DataTables loaded lazily');
                        window.perfMetrics.mark('DataTablesLoaded');
                    })
                    .catch(err => console.error('Failed to load DataTables:', err));
            }
            return Promise.resolve();
        };
        
        // Performance: Intersection Observer for lazy loading
        if ('IntersectionObserver' in window) {
            const lazyObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const target = entry.target;
                        
                        // Load DataTables when table is visible
                        if (target.classList.contains('data-table')) {
                            loadDataTables().then(() => {
                                // Initialize DataTable
                                if (window.jQuery && window.jQuery.fn.DataTable) {
                                    $(target).DataTable({
                                        responsive: true,
                                        pageLength: 25,
                                        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                                        processing: true,
                                        language: {
                                            processing: '<div class="spinner"></div> Loading...'
                                        }
                                    });
                                }
                            });
                            lazyObserver.unobserve(target);
                        }
                    }
                });
            });
            
            // Observe tables for lazy loading
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.data-table').forEach(table => {
                    lazyObserver.observe(table);
                });
            });
        }
        
        // Performance: Remove page loader when everything is ready
        window.addEventListener('load', function() {
            window.perfMetrics.mark('WindowLoaded');
            
            // Hide page loader
            const loader = document.getElementById('pageLoader');
            if (loader) {
                loader.classList.add('hidden');
                setTimeout(() => loader.remove(), 300);
            }
            
            // Performance metrics logging
            if (window.perfMetrics && console.table) {
                console.group('⚡ Performance Metrics');
                console.table(window.perfMetrics.measure());
                console.groupEnd();
            }
            
            // Send performance data to analytics (optional)
            if (window.gtag) {
                const metrics = window.perfMetrics.measure();
                window.gtag('event', 'page_performance', {
                    dom_content_loaded: metrics.domContentLoaded || 0,
                    window_loaded: metrics.WindowLoaded || 0,
                    content_painted: metrics.ContentStart || 0
                });
            }
        });
        
        // Performance: Service Worker registration (optional)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/service-worker.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    
    <!-- Performance: Load jQuery only when needed -->
    <script>
        // Load jQuery if not already loaded
        if (typeof jQuery === 'undefined') {
            const jqueryScript = document.createElement('script');
            jqueryScript.src = '<?= base_url() ?>/public/dashboard/assets/vendor/libs/jquery/jquery.js';
            jqueryScript.onload = function() {
                window.perfMetrics.mark('jQueryLoaded');
                
                // Load Bootstrap and other dependencies
                const scripts = [
                    '<?= base_url() ?>/public/dashboard/assets/vendor/js/bootstrap.js',
                    '<?= base_url() ?>/public/dashboard/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
                    '<?= base_url() ?>/public/dashboard/assets/vendor/js/menu.js'
                ];
                
                scripts.forEach((src, index) => {
                    const script = document.createElement('script');
                    script.src = src;
                    script.async = true;
                    if (index === scripts.length - 1) {
                        script.onload = () => window.perfMetrics.mark('CoreScriptsLoaded');
                    }
                    document.head.appendChild(script);
                });
            };
            document.head.appendChild(jqueryScript);
        }
    </script>
    
    <!-- Performance: Page-specific scripts loaded conditionally -->
    <?php if (isset($assetsJs) && is_array($assetsJs)): ?>
        <script>
            // Load page-specific JavaScript files lazily
            const pageScripts = <?= json_encode($assetsJs) ?>;
            
            document.addEventListener('DOMContentLoaded', function() {
                pageScripts.forEach(jsFile => {
                    const script = document.createElement('script');
                    script.src = '<?= base_url() ?>/public/dashboard/assets/js/' + jsFile + '.js';
                    script.async = true;
                    script.onload = () => console.log('Loaded:', jsFile);
                    script.onerror = () => console.error('Failed to load:', jsFile);
                    document.head.appendChild(script);
                });
            });
        </script>
    <?php endif; ?>
    
    <!-- Performance: Form validation and UX enhancements -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Enhanced form handling
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = form.querySelector('[type="submit"]');
                    if (submitBtn && !submitBtn.disabled) {
                        submitBtn.classList.add('loading');
                        submitBtn.disabled = true;
                        
                        // Re-enable after 5 seconds as fallback
                        setTimeout(() => {
                            if (submitBtn.classList.contains('loading')) {
                                submitBtn.classList.remove('loading');
                                submitBtn.disabled = false;
                            }
                        }, 5000);
                    }
                });
            });
            
            // Enhanced search with debouncing
            const searchInputs = document.querySelectorAll('input[type="search"], input[placeholder*="Search"]');
            searchInputs.forEach(input => {
                if (window.debounce) {
                    input.addEventListener('input', window.debounce(function() {
                        // Implement search functionality
                        console.log('Search:', this.value);
                    }, 300));
                }
            });
            
            // Performance: Optimize table rendering
            const tables = document.querySelectorAll('table:not(.data-table)');
            tables.forEach(table => {
                if (table.rows.length > 50) {
                    table.classList.add('table-virtualized');
                    // Implement virtual scrolling for large tables
                }
            });
            
            window.perfMetrics.mark('DOMEnhanced');
        });
        
        // Performance: Memory cleanup
        window.addEventListener('beforeunload', function() {
            // Clear intervals and timeouts
            for (let i = 1; i < 99999; i++) window.clearInterval(i);
            for (let i = 1; i < 99999; i++) window.clearTimeout(i);
            
            // Clear event listeners
            document.removeEventListener('DOMContentLoaded', arguments.callee);
        });
    </script>
    
    <!-- Performance monitoring and error reporting -->
    <script>
        // Error tracking
        window.addEventListener('error', function(e) {
            console.error('Error:', e.error);
            // Send to error tracking service
        });
        
        // Unhandled promise rejections
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            e.preventDefault();
        });
        
        // Performance monitoring
        if ('PerformanceObserver' in window) {
            try {
                // Monitor long tasks
                const longTaskObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (entry.duration > 50) {
                            console.warn('Long task detected:', entry.duration + 'ms');
                        }
                    }
                });
                longTaskObserver.observe({ entryTypes: ['longtask'] });
                
                // Monitor layout shifts
                const clsObserver = new PerformanceObserver((list) => {
                    for (const entry of list.getEntries()) {
                        if (!entry.hadRecentInput) {
                            console.log('CLS:', entry.value);
                        }
                    }
                });
                clsObserver.observe({ entryTypes: ['layout-shift'] });
                
            } catch (e) {
                // Ignore if not supported
            }
        }
    </script>
</body>
</html>