// Legacy stub: ensure the unified admin bundle is loaded even if this path is requested.
(function () {
    if (window.__AIRewebLegacyAdminLoader) {
        return;
    }
    window.__AIRewebLegacyAdminLoader = true;
    var script = document.createElement('script');
    script.src = '/assets/js/admin.js?v=20251016';
    script.defer = true;
    document.head.appendChild(script);
})();
