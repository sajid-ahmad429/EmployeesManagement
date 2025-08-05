// Core JavaScript Bundle - Essential functionality
(function() {
    'use strict';
    
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
    
    // Lazy loading utility
    window.lazyLoad = function(selector, callback) {
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        callback(entry.target);
                        observer.unobserve(entry.target);
                    }
                });
            });
            
            document.querySelectorAll(selector).forEach(function(el) {
                observer.observe(el);
            });
        } else {
            // Fallback for older browsers
            document.querySelectorAll(selector).forEach(callback);
        }
    };
    
    // Debounce utility for search and form inputs
    window.debounce = function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };
    
    // Initialize core functionality
    document.addEventListener('DOMContentLoaded', function() {
        window.perfMetrics.mark('DOMContentLoaded');
        
        // Add loading states to forms
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('[type="submit"]');
                if (submitBtn) {
                    submitBtn.classList.add('loading');
                    submitBtn.disabled = true;
                }
            });
        });
    });
    
    window.perfMetrics.mark('CoreScriptLoaded');
})();
