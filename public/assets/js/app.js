// Copy server IP to clipboard
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-copy]');
    if (!btn) return;
    const text = btn.getAttribute('data-copy');
    navigator.clipboard.writeText(text).then(function () {
        const original = btn.querySelector('[data-copy-label]');
        if (original) {
            const prev = original.textContent;
            original.textContent = 'Kopyalandı!';
            setTimeout(() => (original.textContent = prev), 1500);
        }
    });
});

// Dropdown toggles
document.addEventListener('click', function (e) {
    const toggle = e.target.closest('[data-dropdown]');
    document.querySelectorAll('[data-dropdown-menu]').forEach(function (menu) {
        if (!toggle || menu.getAttribute('data-dropdown-menu') !== toggle.getAttribute('data-dropdown')) {
            if (!menu.contains(e.target)) menu.classList.add('hidden');
        }
    });
    if (toggle) {
        const menu = document.querySelector('[data-dropdown-menu="' + toggle.getAttribute('data-dropdown') + '"]');
        if (menu) menu.classList.toggle('hidden');
    }
});

// Mobile nav
document.addEventListener('click', function (e) {
    if (e.target.closest('[data-mobile-toggle]')) {
        const nav = document.getElementById('mobile-nav');
        if (nav) nav.classList.toggle('hidden');
    }
});

// Countdown timers (data-countdown="ISO date")
function initCountdowns() {
    document.querySelectorAll('[data-countdown]').forEach(function (el) {
        const target = new Date(el.getAttribute('data-countdown')).getTime();
        function tick() {
            const now = Date.now();
            let diff = Math.max(0, Math.floor((target - now) / 1000));
            const d = Math.floor(diff / 86400); diff -= d * 86400;
            const h = Math.floor(diff / 3600); diff -= h * 3600;
            const m = Math.floor(diff / 60); const s = diff - m * 60;
            const set = (sel, val) => { const n = el.querySelector(sel); if (n) n.textContent = String(val).padStart(2, '0'); };
            set('[data-d]', d); set('[data-h]', h); set('[data-m]', m); set('[data-s]', s);
        }
        tick();
        setInterval(tick, 1000);
    });
}
document.addEventListener('DOMContentLoaded', initCountdowns);

// Auto-dismiss flash messages
document.addEventListener('DOMContentLoaded', function () {
    setTimeout(function () {
        document.querySelectorAll('[data-flash]').forEach(function (el) {
            el.style.transition = 'opacity .4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        });
    }, 5000);
});
