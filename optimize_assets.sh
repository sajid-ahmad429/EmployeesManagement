#!/bin/bash

# Enhanced Asset Optimization Script for Employee Management System
# This script optimizes CSS and JavaScript files for better performance

echo "🚀 Starting enhanced asset optimization..."

# Create optimized directories
mkdir -p public/dashboard/optimized/css
mkdir -p public/dashboard/optimized/js
mkdir -p public/dashboard/optimized/images

# Function to compress CSS files with advanced optimization
compress_css() {
    local input_file=$1
    local output_file=$2
    
    echo "Compressing CSS: $(basename "$input_file")"
    
    # Advanced CSS minification with multiple passes
    cat "$input_file" | \
        # Remove comments
        sed -e 's/\/\*[^*]*\*\///g' | \
        # Remove unnecessary whitespace
        sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' | \
        # Remove empty lines
        sed '/^$/d' | \
        # Optimize selectors and properties
        sed -e 's/[[:space:]]*{[[:space:]]*/{/g' | \
        sed -e 's/[[:space:]]*}[[:space:]]*/}/g' | \
        sed -e 's/[[:space:]]*;[[:space:]]*/;/g' | \
        sed -e 's/[[:space:]]*,[[:space:]]*/,/g' | \
        sed -e 's/[[:space:]]*:[[:space:]]*/:/g' | \
        # Remove trailing semicolons
        sed -e 's/;}/}/g' | \
        # Optimize colors
        sed -e 's/#000000/black/g' -e 's/#ffffff/white/g' | \
        # Optimize zero values
        sed -e 's/0px/0/g' -e 's/0em/0/g' -e 's/0%/0/g' > "$output_file"
    
    # Calculate compression ratio
    original_size=$(wc -c < "$input_file")
    compressed_size=$(wc -c < "$output_file")
    reduction=$((100 - (compressed_size * 100 / original_size)))
    
    echo "  ✅ Compressed: ${original_size} → ${compressed_size} bytes (${reduction}% reduction)"
}

# Function to compress JavaScript files with advanced optimization
compress_js() {
    local input_file=$1
    local output_file=$2
    
    echo "Compressing JS: $(basename "$input_file")"
    
    # Advanced JS minification
    cat "$input_file" | \
        # Remove single-line comments
        sed -e '/^[[:space:]]*\/\//d' | \
        # Remove multi-line comments
        sed -e 's/\/\*[^*]*\*\///g' | \
        # Remove unnecessary whitespace
        sed -e 's/^[[:space:]]*//' -e 's/[[:space:]]*$//' | \
        # Remove empty lines
        sed '/^$/d' | \
        # Optimize operators
        sed -e 's/[[:space:]]*=[[:space:]]*/=/g' | \
        sed -e 's/[[:space:]]*+[[:space:]]*/+/g' | \
        sed -e 's/[[:space:]]*-[[:space:]]*/-/g' | \
        sed -e 's/[[:space:]]*\*[[:space:]]*/*/g' | \
        sed -e 's/[[:space:]]*\/[[:space:]]*/\//g' | \
        # Optimize parentheses
        sed -e 's/[[:space:]]*([[:space:]]*/(/g' | \
        sed -e 's/[[:space:]]*)[[:space:]]*/)/g' | \
        # Optimize brackets
        sed -e 's/[[:space:]]*\[[[:space:]]*/[/g' | \
        sed -e 's/[[:space:]]*\][[:space:]]*/]/g' | \
        # Optimize braces
        sed -e 's/[[:space:]]*{[[:space:]]*/{/g' | \
        sed -e 's/[[:space:]]*}[[:space:]]*/}/g' > "$output_file"
    
    # Calculate compression ratio
    original_size=$(wc -c < "$input_file")
    compressed_size=$(wc -c < "$output_file")
    reduction=$((100 - (compressed_size * 100 / original_size)))
    
    echo "  ✅ Compressed: ${original_size} → ${compressed_size} bytes (${reduction}% reduction)"
}

# Function to optimize images
optimize_images() {
    local input_file=$1
    local output_file=$2
    
    echo "Optimizing image: $(basename "$input_file")"
    
    # Check if imagemagick is available
    if command -v convert >/dev/null 2>&1; then
        # Optimize image with imagemagick
        convert "$input_file" -strip -quality 85 "$output_file"
        
        # Calculate compression ratio
        original_size=$(wc -c < "$input_file")
        compressed_size=$(wc -c < "$output_file")
        reduction=$((100 - (compressed_size * 100 / original_size)))
        
        echo "  ✅ Optimized: ${original_size} → ${compressed_size} bytes (${reduction}% reduction)"
    else
        # Fallback: just copy the file
        cp "$input_file" "$output_file"
        echo "  ⚠️  ImageMagick not available, copied without optimization"
    fi
}

# Optimize large CSS files
echo "📦 Optimizing CSS files..."
for css_file in $(find public/dashboard -name "*.css" -size +50k); do
    relative_path=${css_file#public/dashboard/}
    output_path="public/dashboard/optimized/${relative_path}"
    output_dir=$(dirname "$output_path")
    mkdir -p "$output_dir"
    
    compress_css "$css_file" "$output_path"
done

# Optimize large JavaScript files
echo "📦 Optimizing JavaScript files..."
for js_file in $(find public/dashboard -name "*.js" -size +50k); do
    relative_path=${js_file#public/dashboard/}
    output_path="public/dashboard/optimized/${relative_path}"
    output_dir=$(dirname "$output_path")
    mkdir -p "$output_dir"
    
    compress_js "$js_file" "$output_path"
done

# Optimize images
echo "🖼️  Optimizing images..."
for img_file in $(find public/dashboard -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" -o -name "*.gif" | head -20); do
    relative_path=${img_file#public/dashboard/}
    output_path="public/dashboard/optimized/${relative_path}"
    output_dir=$(dirname "$output_path")
    mkdir -p "$output_dir"
    
    optimize_images "$img_file" "$output_path"
done

# Create enhanced critical CSS file
echo "🎯 Creating enhanced critical CSS bundle..."
critical_css="public/dashboard/optimized/critical.min.css"
cat > "$critical_css" << 'EOF'
/* Enhanced Critical CSS - Above the fold styles */
*{box-sizing:border-box}body{font-family:system-ui,-apple-system,BlinkMacSystemFont,sans-serif;margin:0;padding:0;line-height:1.6;color:#333}.container{max-width:1200px;margin:0 auto;padding:0 15px}.header{background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.1);position:sticky;top:0;z-index:1000}.nav{display:flex;align-items:center;padding:1rem 0}.btn{display:inline-block;padding:.5rem 1rem;border:1px solid #ccc;background:#f8f9fa;text-decoration:none;border-radius:4px;transition:all .2s ease}.btn-primary{background:#007bff;color:#fff;border-color:#007bff}.btn:hover{transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,.1)}.table{width:100%;border-collapse:collapse;margin:1rem 0}.table th,.table td{padding:12px;text-align:left;border-bottom:1px solid #ddd}.table th{background:#f8f9fa;font-weight:600}.loading{display:inline-block;width:20px;height:20px;border:3px solid #f3f3f3;border-top:3px solid #007bff;border-radius:50%;animation:spin 1s linear infinite}@keyframes spin{0%{transform:rotate(0deg)}100%{transform:rotate(360deg)}}.fade-in{opacity:0;animation:fadeIn .3s ease-in forwards}@keyframes fadeIn{to{opacity:1}}
EOF

# Create service worker for caching
echo "🔧 Creating service worker for enhanced caching..."
sw_file="public/dashboard/optimized/sw.js"
cat > "$sw_file" << 'EOF'
// Enhanced Service Worker for Employee Management System
const CACHE_NAME = 'ems-v1.0.0';
const urlsToCache = [
  '/dashboard/optimized/critical.min.css',
  '/dashboard/optimized/dist/css/core.css',
  '/dashboard/optimized/dist/js/vendors.min.js'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request);
      })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});
EOF

# Create performance monitoring script
echo "📊 Creating performance monitoring script..."
perf_script="public/dashboard/optimized/performance.js"
cat > "$perf_script" << 'EOF'
// Performance Monitoring Script
(function() {
    'use strict';
    
    const perfData = {
        startTime: performance.now(),
        marks: {},
        measures: {}
    };
    
    // Mark performance points
    window.markPerformance = function(name) {
        performance.mark(name);
        perfData.marks[name] = performance.now();
    };
    
    // Measure performance between marks
    window.measurePerformance = function(name, startMark, endMark) {
        try {
            performance.measure(name, startMark, endMark);
            const measure = performance.getEntriesByName(name)[0];
            perfData.measures[name] = measure.duration;
            
            // Log slow operations
            if (measure.duration > 100) {
                console.warn(`Slow operation: ${name} took ${measure.duration.toFixed(2)}ms`);
            }
        } catch (e) {
            console.warn('Performance measurement failed:', e);
        }
    };
    
    // Send performance data to server
    window.sendPerformanceData = function() {
        if (navigator.sendBeacon) {
            const data = {
                url: window.location.href,
                userAgent: navigator.userAgent,
                performance: perfData,
                timestamp: Date.now()
            };
            
            navigator.sendBeacon('/api/performance', JSON.stringify(data));
        }
    };
    
    // Mark initial load
    markPerformance('page-start');
    
    // Mark when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        markPerformance('dom-ready');
        measurePerformance('dom-load-time', 'page-start', 'dom-ready');
    });
    
    // Mark when page is fully loaded
    window.addEventListener('load', function() {
        markPerformance('page-loaded');
        measurePerformance('total-load-time', 'page-start', 'page-loaded');
        
        // Send performance data after a delay
        setTimeout(sendPerformanceData, 1000);
    });
})();
EOF

echo "✅ Enhanced asset optimization completed!"
echo ""
echo "📈 Performance improvements:"
echo "- CSS files optimized with advanced compression"
echo "- JavaScript files minified and optimized"
echo "- Images compressed and optimized"
echo "- Critical CSS created for above-the-fold content"
echo "- Service worker added for enhanced caching"
echo "- Performance monitoring script included"
echo ""
echo "📁 Optimized files are in: public/dashboard/optimized/"
echo ""
echo "🚀 To use optimized assets, update your view templates to reference:"
echo "- public/dashboard/optimized/ instead of public/dashboard/dist/"
echo "- Load critical.min.css inline for immediate render"
echo "- Include performance.js for monitoring"
echo "- Register service worker for caching"
echo ""
echo "📊 File size comparison:"
find public/dashboard -name "*.css" -size +50k -exec wc -c {} \; | while read size file; do
    optimized_file="public/dashboard/optimized/${file#public/dashboard/}"
    if [ -f "$optimized_file" ]; then
        optimized_size=$(wc -c < "$optimized_file")
        reduction=$((100 - (optimized_size * 100 / size)))
        echo "$(basename "$file"): ${size} bytes → ${optimized_size} bytes (${reduction}% reduction)"
    fi
done

echo ""
echo "🎯 Next steps:"
echo "1. Run database migrations: php spark migrate"
echo "2. Update view templates to use optimized assets"
echo "3. Configure Redis for caching"
echo "4. Monitor performance with the new metrics system"