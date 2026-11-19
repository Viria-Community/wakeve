<style>
.site-header, .public-bar {
    position: fixed !important; top: 14px; left: 14px; right: 14px; width: auto !important; transform: none !important;
    z-index: 60; display: flex; align-items: center; justify-content: space-between; gap: 16px;
    padding: 10px 18px; border-radius: 22px;
    background: linear-gradient(135deg, rgba(8, 122, 90, .42), rgba(8, 122, 90, .22)) !important;
    backdrop-filter: blur(18px) saturate(160%); -webkit-backdrop-filter: blur(18px) saturate(160%);
    border: 1px solid rgba(255, 255, 255, .35);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .45), 0 18px 40px rgba(0, 0, 0, .18);
    transition: top .35s ease, background .35s ease, box-shadow .35s ease;
}
.site-header.is-scrolled, .public-bar.is-scrolled {
    top: 8px;
    background: linear-gradient(135deg, rgba(6, 84, 62, .92), rgba(8, 122, 90, .86)) !important;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .35), 0 22px 48px rgba(0, 0, 0, .28);
}
body:has(.site-header) { padding-top: 96px; }

.site-nav, .public-bar nav { display: flex; align-items: center; gap: 6px; margin-left: auto; }
.site-nav a, .public-bar a {
    position: relative; display: inline-flex; align-items: center; gap: 6px;
    border-radius: 14px; padding: 8px 12px; color: rgba(255, 255, 255, .92); text-decoration: none;
    transition: background .25s ease, color .25s ease, transform .25s ease;
}
.nav-ico { display: inline-flex; color: #e9c46a; transition: transform .25s ease; }
.site-nav a:hover, .public-bar a:hover {
    background: rgba(255, 255, 255, .16); color: #fff !important; transform: translateY(-1px);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25);
}
.site-nav a:hover .nav-ico, .public-bar a:hover .nav-ico { transform: scale(1.12); }
.site-nav a:not(.nav-cta):not(.nav-login)::after, .public-bar a::after {
    content: ""; position: absolute; left: 50%; bottom: 3px; width: 0; height: 2px;
    background: linear-gradient(90deg, transparent, #e9c46a, transparent);
    transform: translateX(-50%); transition: width .3s ease;
}
.site-nav a:hover::after, .public-bar a:hover::after { width: 60%; }

.btn, .btn-action, .nav-cta, .nav-login, .button, button[type="submit"] {
    position: relative; overflow: hidden;
    transition: transform .2s ease, box-shadow .25s ease, background-color .25s ease, filter .25s ease;
}
.btn:hover, .btn-action:hover, .nav-cta:hover, .nav-login:hover, .button:hover, button[type="submit"]:hover {
    transform: translateY(-2px); box-shadow: 0 10px 22px rgba(8, 122, 90, .28); filter: brightness(1.05);
}
.btn:active, .btn-action:active, .nav-cta:active, .button:active, button[type="submit"]:active {
    transform: translateY(0) scale(.98);
}
.btn::after, .btn-action::after, .nav-cta::after, .button::after {
    content: ""; position: absolute; inset: 0;
    background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .35), transparent);
    transform: translateX(-120%); transition: transform .6s ease; pointer-events: none;
}
.btn:hover::after, .btn-action:hover::after, .nav-cta:hover::after, .button:hover::after { transform: translateX(120%); }

@media (max-width: 760px) {
    .site-header, .public-bar { top: 8px; left: 8px; right: 8px; padding: 8px 10px; border-radius: 18px; }
    body:has(.site-header) { padding-top: 84px; }
    .site-nav a, .public-bar a { padding: 7px 8px; }
}
@media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { transition: none !important; animation: none !important; }
}
</style>
<script>
(function () {
    var icones = {
        'Accueil': '<path d="M3 11l9-8 9 8"/><path d="M5 10v10h14V10"/>',
        'Compagnies': '<rect x="4" y="3" width="16" height="18" rx="2"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2"/>',
        'Suivre une demande': '<circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/>',
        'Inscription': '<circle cx="9" cy="8" r="4"/><path d="M2 21v-1a6 6 0 0 1 12 0v1"/><path d="M19 8v6M16 11h6"/>',
        'Mon espace': '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'Se connecter': '<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/>'
    };
    var defaut = '<circle cx="12" cy="12" r="9"/>';

    function ajouterIcones() {
        document.querySelectorAll('.site-nav a, .public-bar a').forEach(function (lien) {
            if (lien.querySelector('.nav-ico')) { return; }
            var libelle = lien.textContent.trim();
            var chemin = icones[libelle] || defaut;
            var span = document.createElement('span');
            span.className = 'nav-ico';
            span.setAttribute('aria-hidden', 'true');
            span.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' + chemin + '</svg>';
            lien.insertBefore(span, lien.firstChild);
        });
    }

    function majBarre() {
        var barre = document.querySelector('.site-header, .public-bar');
        if (barre) { barre.classList.toggle('is-scrolled', window.scrollY > 24); }
    }

    document.addEventListener('DOMContentLoaded', ajouterIcones);
    window.addEventListener('scroll', majBarre, { passive: true });
    majBarre();
})();
</script>
