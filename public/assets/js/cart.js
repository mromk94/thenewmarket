document.addEventListener('DOMContentLoaded', function () {
    const toast = document.createElement('div');
    toast.className = 'cart-toast';
    toast.style.cssText = 'position:fixed; bottom:1rem; right:1rem; padding:0.75rem 1rem; border-radius:0.5rem; background:#0f172a; color:#fff; z-index:9999; opacity:0; transition:opacity 0.2s; pointer-events:none;';
    document.body.appendChild(toast);

    function showToast(message) {
        toast.textContent = message;
        toast.style.opacity = '1';
        setTimeout(() => { toast.style.opacity = '0'; }, 2500);
    }

    function updateCartBadge() {
        fetch('/cart?format=json', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                const count = (data.items || []).reduce((sum, item) => sum + (parseInt(item.quantity) || 0), 0);
                document.querySelectorAll('[data-cart-count]').forEach(el => el.textContent = count);
            })
            .catch(() => {});
    }

    function parseMoney(text) {
        return parseFloat((text || '').replace(/[^0-9.]/g, '')) || 0;
    }

    function formatMoney(amount) {
        return '$' + amount.toFixed(2);
    }

    document.querySelectorAll('form[data-ajax-cart]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const action = form.getAttribute('data-ajax-cart');

            fetch(form.action, {
                method: form.method || 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    if (action === 'remove' || action === 'update') {
                        window.location.reload();
                        return;
                    }
                    showToast(data.message);
                    updateCartBadge();
                } else {
                    showToast(data.message || 'Something went wrong.');
                }
            })
            .catch(() => showToast('Request failed.'));
        });
    });
});
