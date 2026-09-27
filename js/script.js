// PC Poa - basic front-end interactivity

document.addEventListener('DOMContentLoaded', function () {
    // Auto-dismiss alerts after 4 seconds
    document.querySelectorAll('.alert-auto-dismiss').forEach(function (alertEl) {
        setTimeout(function () {
            const alert = bootstrap.Alert.getOrCreateInstance(alertEl);
            alert.close();
        }, 4000);
    });

    // Product quantity stepper on shop/product pages
    document.querySelectorAll('.qty-stepper').forEach(function (stepper) {
        const input = stepper.querySelector('input');
        stepper.querySelectorAll('button').forEach(function (btn) {
            btn.addEventListener('click', function () {
                let val = parseInt(input.value || '1', 10);
                val = btn.dataset.action === 'inc' ? val + 1 : Math.max(1, val - 1);
                input.value = val;
            });
        });
    });

    // Confirm before delete actions in admin panel (legacy link-based, kept for compatibility)
    document.querySelectorAll('.confirm-delete').forEach(function (link) {
        link.addEventListener('click', function (e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Confirm before delete actions submitted as a form (CSRF-protected admin actions)
    document.querySelectorAll('.confirm-delete-form').forEach(function (form) {
        form.addEventListener('submit', function (e) {
            if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                e.preventDefault();
            }
        });
    });

    // Auto-submit a filter/status <select> when its value changes
    // (replaces inline onchange="this.form.submit()" for CSP compliance)
    document.querySelectorAll('.auto-submit-select').forEach(function (select) {
        select.addEventListener('change', function () {
            select.form.submit();
        });
    });
});
