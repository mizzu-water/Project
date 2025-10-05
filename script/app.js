document.addEventListener('DOMContentLoaded', function () {
    // BASIC: Go back when anything with data-back (or #backBtn) is clicked
    var backElements = document.querySelectorAll('[data-back], #backBtn');
    backElements.forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            window.history.back();
        });
    });

    // Inject very simple CSS for animations
    var style = document.createElement('style');
    style.textContent = `
    .fade-in-up { opacity: 0; transform: translateY(8px); animation: fadeInUp 420ms ease-out forwards; }
    .btn-press { transition: transform 80ms ease; }
    .btn-press:active { transform: scale(0.98); }
    @keyframes fadeInUp { to { opacity: 1; transform: translateY(0); } }
    `;
    document.head.appendChild(style);

    // Add fade-in to common elements with a tiny stagger
    var animated = Array.prototype.slice.call(document.querySelectorAll('.card, table tbody tr, .list-group a'));
    animated.forEach(function (el, i) {
        el.style.animationDelay = (i * 60) + 'ms';
        el.classList.add('fade-in-up');
    });

    // Add small press effect to buttons and list-group links
    var pressables = document.querySelectorAll('button, .btn, .list-group a');
    pressables.forEach(function (el) { el.classList.add('btn-press'); });
});


