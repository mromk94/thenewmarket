document.addEventListener('DOMContentLoaded', function () {
    const html = document.documentElement;
    const toggles = document.querySelectorAll('[data-theme-toggle]');

    function updateToggles() {
        const isDark = html.getAttribute('data-theme') === 'dark';
        toggles.forEach(function (btn) {
            btn.setAttribute('aria-pressed', isDark ? 'true' : 'false');
            btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            const icon = btn.querySelector('.theme-icon') || btn;
            if (icon) {
                icon.textContent = isDark ? '\u2600' : '\u263E';
            }
        });
    }

    toggles.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const current = html.getAttribute('data-theme') === 'dark' ? 'dark' : 'light';
            const next = current === 'dark' ? 'light' : 'dark';
            html.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            updateToggles();
        });
    });

    updateToggles();
});
