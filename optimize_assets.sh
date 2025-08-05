#!/bin/bash

# Asset Optimization Script for Employee Management System
# This script optimizes CSS and JavaScript files for better performance

echo "Starting asset optimization..."

# Create optimized directories
mkdir -p public/dashboard/optimized/css
mkdir -p public/dashboard/optimized/js

# Function to compress CSS files
compress_css() {
    local input_file=$1
    local output_file=$2
    
    # Remove comments, whitespace, and minify CSS
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
}

# Function to compress JavaScript files
compress_js() {
    local input_file=$1
    local output_file=$2
    
    # Basic JS minification (remove comments and extra whitespace)
    sed -e '/^[[:space:]]*\/\//d' \
        -e 's/\/\*[^*]*\*\///g' \
        -e 's/^[[:space:]]*//' \
        -e 's/[[:space:]]*$//' \
        -e '/^$/d' \
        "$input_file" > "$output_file"
}

# Optimize large CSS files
echo "Optimizing CSS files..."
for css_file in $(find public/dashboard -name "*.css" -size +100k); do
    relative_path=${css_file#public/dashboard/}
    output_path="public/dashboard/optimized/${relative_path}"
    output_dir=$(dirname "$output_path")
    mkdir -p "$output_dir"
    
    echo "Compressing $css_file -> $output_path"
    compress_css "$css_file" "$output_path"
done

# Optimize large JavaScript files
echo "Optimizing JavaScript files..."
for js_file in $(find public/dashboard -name "*.js" -size +100k); do
    relative_path=${js_file#public/dashboard/}
    output_path="public/dashboard/optimized/${relative_path}"
    output_dir=$(dirname "$output_path")
    mkdir -p "$output_dir"
    
    echo "Compressing $js_file -> $output_path"
    compress_js "$js_file" "$output_path"
done

# Create a combined critical CSS file
echo "Creating critical CSS bundle..."
critical_css="public/dashboard/optimized/critical.min.css"
cat > "$critical_css" << 'EOF'
/* Critical CSS - Above the fold styles */
body{font-family:system-ui,-apple-system,sans-serif;margin:0;padding:0}
.container{max-width:1200px;margin:0 auto;padding:0 15px}
.header{background:#fff;box-shadow:0 2px 4px rgba(0,0,0,.1);position:sticky;top:0;z-index:1000}
.nav{display:flex;align-items:center;padding:1rem 0}
.btn{display:inline-block;padding:.5rem 1rem;border:1px solid #ccc;background:#f8f9fa;text-decoration:none;border-radius:4px}
.btn-primary{background:#007bff;color:#fff;border-color:#007bff}
.table{width:100%;border-collapse:collapse}
.table th,.table td{padding:12px;text-align:left;border-bottom:1px solid #ddd}
.table th{background:#f8f9fa;font-weight:600}
EOF

echo "Asset optimization completed!"
echo "Optimized files are in public/dashboard/optimized/"
echo ""
echo "To use optimized assets, update your view templates to reference:"
echo "- public/dashboard/optimized/ instead of public/dashboard/dist/"
echo "- Load critical.min.css inline for above-the-fold content"
echo ""
echo "File size comparison:"
find public/dashboard -name "*.css" -size +100k -exec wc -c {} \; | while read size file; do
    optimized_file="public/dashboard/optimized/${file#public/dashboard/}"
    if [ -f "$optimized_file" ]; then
        optimized_size=$(wc -c < "$optimized_file")
        reduction=$((100 - (optimized_size * 100 / size)))
        echo "$(basename "$file"): ${size} bytes -> ${optimized_size} bytes (${reduction}% reduction)"
    fi
done