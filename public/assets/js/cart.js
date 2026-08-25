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

    function openCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        if (!drawer) return;
        drawer.style.display = 'block';
        document.body.style.overflow = 'hidden';
        loadCartDrawer();
    }

    function closeCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        if (!drawer) return;
        drawer.style.display = 'none';
        document.body.style.overflow = '';
    }

    function getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function removeFromMiniCart(cartItemId) {
        const body = new FormData();
        body.append('csrf_token', getCsrfToken());

        fetch('/cart/' + cartItemId + '/remove', {
            method: 'POST',
            body: body,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                updateCartBadge();
                loadCartDrawer();
            } else {
                showToast(data.message || 'Could not remove item.');
            }
        })
        .catch(() => showToast('Request failed.'));
    }

    function loadCartDrawer() {
        const container = document.querySelector('[data-cart-items]');
        if (!container) return;

        fetch('/cart?format=json', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                const items = data.items || [];
                const currency = window.currencySymbol || '$';

                if (items.length === 0) {
                    container.innerHTML = '<div class="cart-drawer-empty"><p>Your cart is empty.</p></div>';
                    return;
                }

                const list = document.createElement('div');
                items.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'cart-drawer-item';
                    const image = item.thumbnail
                        ? `<img src="/assets/${item.thumbnail}" alt="" loading="lazy">`
                        : `<img src="/assets/images/placeholder-product.svg" alt="" loading="lazy">`;
                    const affiliate = item.affiliate_vendor_name ? ` · via ${item.affiliate_vendor_name}` : '';
                    row.innerHTML = `
                        ${image}
                        <div class="info">
                            <h4>${item.name}</h4>
                            <p>Qty ${item.quantity} · ${currency}${(parseFloat(item.unit_price) * parseInt(item.quantity)).toFixed(2)}${affiliate}</p>
                        </div>
                        <button type="button" class="btn btn-outline" data-cart-remove="${item.cart_item_id}" style="padding:0.3rem 0.6rem; color:#ef4444; border-color:#ef4444;">×</button>
                    `;
                    list.appendChild(row);
                });

                const summary = data.summary || {};
                const total = document.createElement('div');
                total.style.cssText = 'display:flex; justify-content:space-between; font-weight:700; margin:1rem 0; padding-top:0.75rem; border-top:1px solid var(--border);';
                total.innerHTML = `<span>Total</span><span>${currency}${(parseFloat(summary.total) || 0).toFixed(2)}</span>`;
                list.appendChild(total);

                container.innerHTML = '';
                container.appendChild(list);

                container.querySelectorAll('[data-cart-remove]').forEach(btn => {
                    btn.addEventListener('click', function () {
                        removeFromMiniCart(this.getAttribute('data-cart-remove'));
                    });
                });
            })
            .catch(() => {
                container.innerHTML = '<p style="color:var(--muted);">Could not load cart.</p>';
            });
    }

    document.querySelectorAll('[data-cart-toggle]').forEach(el => {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            openCartDrawer();
        });
    });

    document.querySelectorAll('[data-cart-close]').forEach(el => {
        el.addEventListener('click', function () {
            closeCartDrawer();
        });
    });

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
                    openCartDrawer();
                } else {
                    showToast(data.message || 'Something went wrong.');
                }
            })
            .catch(() => showToast('Request failed.'));
        });
    });

    updateCartBadge();
});
