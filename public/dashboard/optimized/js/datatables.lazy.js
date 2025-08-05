// DataTables Lazy Loader
window.loadDataTables = function() {
  if (!window.jQuery.fn.DataTable) {
    const script = document.createElement('script');
    script.src = '/public/dashboard/optimized/js/datatables.min.js';
    document.head.appendChild(script);
  }
};
