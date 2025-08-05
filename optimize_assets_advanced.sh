#!/bin/bash

# Advanced Asset Optimization Script for Employee Management System
# Implements modern performance optimization techniques

echo "🚀 Starting advanced asset optimization..."

# Create optimized directories
mkdir -p public/dashboard/optimized/{css,js,critical,lazy,chunks}
mkdir -p public/dashboard/optimized/fonts
mkdir -p public/dashboard/optimized/images

# Function to install dependencies if not available
check_dependencies() {
    if ! command -v node &> /dev/null; then
        echo "❌ Node.js not found. Please install Node.js first."
        exit 1
    fi
    
    # Install critical tools
    if ! npm list -g uglify-js &> /dev/null; then
        echo "📦 Installing uglify-js..."
        npm install -g uglify-js
    fi
    
    if ! npm list -g clean-css-cli &> /dev/null; then
        echo "📦 Installing clean-css-cli..."
        npm install -g clean-css-cli
    fi
}

# Advanced CSS optimization
optimize_css() {
    local input_file=$1
    local output_file=$2
    
    echo "🎨 Optimizing CSS: $(basename "$input_file")"
    
    # Use clean-css for advanced optimization
    if command -v cleancss &> /dev/null; then
        cleancss --level 2 --compatibility ie8 --format beautify \
                 --inline local \
                 --output "$output_file" \
                 "$input_file"
    else
        # Fallback to sed-based optimization
        sed -e 's/\/\*[^*]*\*\///g' \
            -e 's/^[[:space:]]*//' \
            -e 's/[[:space:]]*$//' \
            -e '/^$/d' \
            -e 's/[[:space:]]*{[[:space:]]*/{ /g' \
            -e 's/[[:space:]]*}[[:space:]]*/} /g' \
            -e 's/[[:space:]]*;[[:space:]]*/; /g' \
            -e 's/[[:space:]]*,[[:space:]]*/,/g' \
            -e 's/[[:space:]]*:[[:space:]]*/:/g' \
            "$input_file" > "$output_file"
    fi
}

# Advanced JavaScript optimization
optimize_js() {
    local input_file=$1
    local output_file=$2
    
    echo "⚡ Optimizing JS: $(basename "$input_file")"
    
    # Use uglify-js for advanced optimization
    if command -v uglifyjs &> /dev/null; then
        uglifyjs "$input_file" \
                 --compress drop_console=true,drop_debugger=true,dead_code=true \
                 --mangle reserved=['$','jQuery','CodeIgniter'] \
                 --output "$output_file"
    else
        # Fallback to basic optimization
        sed -e '/^[[:space:]]*\/\//d' \
            -e 's/\/\*[^*]*\*\///g' \
            -e 's/^[[:space:]]*//' \
            -e 's/[[:space:]]*$//' \
            -e '/^$/d' \
            "$input_file" > "$output_file"
    fi
}

# Create critical CSS bundle (above-the-fold styles)
create_critical_css() {
    echo "🎯 Creating critical CSS bundle..."
    
    cat > "public/dashboard/optimized/critical/critical.min.css" << 'EOF'
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
EOF
}

# Create JavaScript bundles
create_js_bundles() {
    echo "📦 Creating JavaScript bundles..."
    
    # Core bundle (essential scripts)
    cat > "public/dashboard/optimized/js/core.bundle.js" << 'EOF'
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
EOF
}

# Create resource hints file
create_resource_hints() {
    echo "🔗 Creating resource hints..."
    
    cat > "public/dashboard/optimized/resource-hints.html" << 'EOF'
<!-- Resource Hints for Performance Optimization -->
<!-- DNS Prefetch for external resources -->
<link rel="dns-prefetch" href="//fonts.googleapis.com">
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link rel="dns-prefetch" href="//cdn.datatables.net">

<!-- Preconnect for critical external resources -->
<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<!-- Preload critical resources -->
<link rel="preload" href="/public/dashboard/optimized/critical/critical.min.css" as="style">
<link rel="preload" href="/public/dashboard/optimized/js/core.bundle.js" as="script">

<!-- Prefetch likely next page resources -->
<link rel="prefetch" href="/public/dashboard/optimized/js/datatables.lazy.js">
<link rel="prefetch" href="/public/dashboard/optimized/css/vendor.bundle.css">
EOF
}

# Optimize large vendor files
optimize_vendors() {
    echo "📚 Optimizing vendor libraries..."
    
    # Optimize DataTables (huge file)
    if [ -f "public/dashboard/dist/libs/datatables-bs5/datatables-bootstrap5.js" ]; then
        echo "🗂️ Optimizing DataTables (11.6MB file)..."
        optimize_js "public/dashboard/dist/libs/datatables-bs5/datatables-bootstrap5.js" \
                   "public/dashboard/optimized/js/datatables.min.js"
        
        # Create a lazy-loaded version
        echo "// DataTables Lazy Loader" > "public/dashboard/optimized/js/datatables.lazy.js"
        echo "window.loadDataTables = function() {" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "  if (!window.jQuery.fn.DataTable) {" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "    const script = document.createElement('script');" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "    script.src = '/public/dashboard/optimized/js/datatables.min.js';" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "    document.head.appendChild(script);" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "  }" >> "public/dashboard/optimized/js/datatables.lazy.js"
        echo "};" >> "public/dashboard/optimized/js/datatables.lazy.js"
    fi
    
    # Optimize large CSS files
    for css_file in $(find public/dashboard/dist -name "*.css" -size +200k); do
        relative_path=${css_file#public/dashboard/dist/}
        output_path="public/dashboard/optimized/css/${relative_path}"
        output_dir=$(dirname "$output_path")
        mkdir -p "$output_dir"
        
        optimize_css "$css_file" "$output_path"
    done
    
    # Optimize large JS files (>500KB)
    for js_file in $(find public/dashboard/dist -name "*.js" -size +500k ! -name "*datatables*"); do
        relative_path=${js_file#public/dashboard/dist/}
        output_path="public/dashboard/optimized/js/${relative_path}"
        output_dir=$(dirname "$output_path")
        mkdir -p "$output_dir"
        
        optimize_js "$js_file" "$output_path"
    done
}

# Create performance manifest
create_performance_manifest() {
    echo "📋 Creating performance manifest..."
    
    cat > "public/dashboard/optimized/performance-manifest.json" << EOF
{
  "version": "1.0.0",
  "generated": "$(date -Iseconds)",
  "bundles": {
    "critical": {
      "css": "/public/dashboard/optimized/critical/critical.min.css",
      "js": "/public/dashboard/optimized/js/core.bundle.js"
    },
    "vendor": {
      "css": "/public/dashboard/optimized/css/vendor.bundle.css",
      "js": "/public/dashboard/optimized/js/vendor.bundle.js"
    },
    "lazy": {
      "datatables": "/public/dashboard/optimized/js/datatables.lazy.js"
    }
  },
  "preload": [
    "/public/dashboard/optimized/critical/critical.min.css",
    "/public/dashboard/optimized/js/core.bundle.js"
  ],
  "prefetch": [
    "/public/dashboard/optimized/js/datatables.lazy.js"
  ]
}
EOF
}

# Main execution
main() {
    echo "🔧 Checking dependencies..."
    # check_dependencies
    
    echo "🎨 Creating critical CSS..."
    create_critical_css
    
    echo "📦 Creating JavaScript bundles..."
    create_js_bundles
    
    echo "🔗 Creating resource hints..."
    create_resource_hints
    
    echo "📚 Optimizing vendor libraries..."
    optimize_vendors
    
    echo "📋 Creating performance manifest..."
    create_performance_manifest
    
    echo ""
    echo "✅ Advanced asset optimization completed!"
    echo ""
    echo "📊 Optimization Results:"
    echo "================================"
    
    # Calculate savings
    original_size=$(du -sb public/dashboard/dist/ 2>/dev/null | cut -f1 || echo "0")
    optimized_size=$(du -sb public/dashboard/optimized/ 2>/dev/null | cut -f1 || echo "0")
    
    if [ "$original_size" -gt 0 ]; then
        savings=$((original_size - optimized_size))
        percent_savings=$((savings * 100 / original_size))
        
        echo "📈 Original size: $(numfmt --to=iec-i --suffix=B $original_size)"
        echo "📉 Optimized size: $(numfmt --to=iec-i --suffix=B $optimized_size)"
        echo "💾 Space saved: $(numfmt --to=iec-i --suffix=B $savings) (${percent_savings}%)"
    fi
    
    echo ""
    echo "🚀 Next Steps:"
    echo "1. Update templates to use optimized assets"
    echo "2. Implement the asset loading strategy"
    echo "3. Enable gzip compression on server"
    echo "4. Set up proper cache headers"
    echo "5. Consider implementing a CDN"
}

# Run main function
main "$@"