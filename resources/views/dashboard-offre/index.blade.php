@extends('layouts.app')
@section('title', 'Tableau de bord de l’offre - PNMG')
@section('body-class', 'pnmg-dash')

@section('content')
<style>
.od{color:#EAF3F0}
.od-entete{display:flex;justify-content:space-between;align-items:flex-end;gap:14px;flex-wrap:wrap;margin-bottom:22px}
.od-fil{font-size:13px;color:#9CB7AF}
.od-titre{font-size:30px;font-weight:800;color:#F4FBF8;margin:4px 0 4px;letter-spacing:-.01em}
.od-sous{color:#9CB7AF;font-size:14px;margin:0}
.od-direct{display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#5CFFB0;background:rgba(92,255,176,.1);border:1px solid rgba(92,255,176,.35);padding:7px 14px;border-radius:999px}
.od-direct::before{content:"";width:8px;height:8px;border-radius:50%;background:#5CFFB0;box-shadow:0 0 0 0 rgba(92,255,176,.7);animation:od-pouls 1.8s infinite}
@keyframes od-pouls{0%{box-shadow:0 0 0 0 rgba(92,255,176,.6)}70%{box-shadow:0 0 0 9px rgba(92,255,176,0)}100%{box-shadow:0 0 0 0 rgba(92,255,176,0)}}

.od-cadre{position:relative;background:linear-gradient(160deg,#12332b,#0c2620);border:1px solid rgba(233,196,106,.16);border-radius:22px;padding:24px 26px;box-shadow:0 22px 48px rgba(0,0,0,.35);margin-bottom:20px;overflow:hidden}
.od-cadre::before{content:"";position:absolute;left:0;top:0;right:0;height:3px;background:linear-gradient(90deg,#087A5A,#5CFFB0 45%,#E9C46A)}
.od-cadre-tete{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;flex-wrap:wrap;margin-bottom:16px}
.od-cadre-tete strong{display:block;font-size:17px;color:#F4FBF8}
.od-cadre-tete span{font-size:13px;color:#9CB7AF}
.od-legende{display:flex;gap:14px;flex-wrap:wrap;font-size:12px;color:#CFE5DD}
.od-legende i{display:inline-block;width:10px;height:10px;border-radius:3px;margin-right:6px;vertical-align:middle}

.od-anneaux{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
.od-anneau{display:flex;flex-direction:column;align-items:center;text-align:center;padding:10px 0 4px}
.od-anneau svg{width:170px;height:170px;overflow:visible}
.od-anneau-nom{font-size:14px;font-weight:700;color:#F4FBF8;margin-top:8px}
.od-anneau-detail{font-size:12px;color:#9CB7AF;margin-top:2px}

.od-histo{width:100%;height:280px;display:block}
.od-histo-axe{font-size:11px;fill:#9CB7AF}

.od-bas{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,1fr);gap:20px}
.od-activite{list-style:none;margin:0;padding:0}
.od-activite li{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:13px 0;border-top:1px dashed rgba(233,196,106,.16);font-size:14px;color:#EAF3F0}
.od-activite li:first-child{border-top:0}
.od-badge{font-size:11px;font-weight:800;letter-spacing:.04em;text-transform:uppercase;padding:4px 10px;border-radius:999px;background:rgba(92,255,176,.12);color:#5CFFB0;white-space:nowrap;margin-right:10px}
.od-badge.depart{background:rgba(233,196,106,.14);color:#FDE68A}
.od-quand{color:#9CB7AF;font-size:12px;white-space:nowrap}
.od-vide{color:#9CB7AF;font-size:14px;padding:10px 0}

.od-barre{display:grid;grid-template-columns:112px 1fr 110px;gap:12px;align-items:center;margin:14px 0;font-size:14px;color:#EAF3F0}
.od-piste{height:12px;border-radius:999px;background:rgba(255,255,255,.07);overflow:hidden}
.od-remplissage{height:100%;width:0;border-radius:999px;background:linear-gradient(90deg,#087A5A,#5CFFB0 60%,#E9C46A);transition:width .9s cubic-bezier(.2,.8,.2,1)}
.od-valeur{text-align:right;font-weight:800;color:#F4FBF8}

@media (max-width:1000px){.od-anneaux{grid-template-columns:1fr 1fr 1fr}.od-bas{grid-template-columns:1fr}}
@media (max-width:620px){.od-anneaux{grid-template-columns:1fr}.od-anneau svg{width:150px;height:150px}.od-barre{grid-template-columns:90px 1fr 92px}}
@media (prefers-reduced-motion:reduce){.od *,.od *::before{animation:none!important;transition:none!important}}
</style>

<div class="od">
    <div class="od-entete">
        <div>
            <div class="od-fil">Offre de transport › Tableau de bord</div>
            <h1 class="od-titre">Tableau de bord de l’offre</h1>
            <p class="od-sous">Vue d’ensemble, historique des créations et activité récente.</p>
        </div>
        <span class="od-direct">Mise à jour en direct</span>
    </div>

    {{-- 1. Diagrammes ronds --}}
    <section class="od-cadre" aria-labelledby="od-s1">
        <div class="od-cadre-tete">
            <div><strong id="od-s1">Répartition de l’offre</strong><span>Lignes et départs en un coup d’œil</span></div>
        </div>
        <div class="od-anneaux" id="od-anneaux"></div>
    </section>

    {{-- 2. Histogramme --}}
    <section class="od-cadre" aria-labelledby="od-s2">
        <div class="od-cadre-tete">
            <div><strong id="od-s2">Créations de lignes · 30 derniers jours</strong><span>Une colonne par jour</span></div>
            <div class="od-legende"><span><i style="background:#5CFFB0"></i>Créations</span></div>
        </div>
        <svg class="od-histo" id="od-histo" viewBox="0 0 640 280" preserveAspectRatio="none" role="img" aria-label="Histogramme des créations de lignes par jour"></svg>
    </section>

    {{-- 3. Éléments récents --}}
    <div class="od-bas">
        <section class="od-cadre" aria-labelledby="od-s3">
            <div class="od-cadre-tete">
                <div><strong id="od-s3">Activité récente</strong><span>Dernières lignes et départs créés</span></div>
            </div>
            <ul class="od-activite" id="od-activite"></ul>
        </section>
        <section class="od-cadre" aria-labelledby="od-s4">
            <div class="od-cadre-tete">
                <div><strong id="od-s4">Tarif moyen par classe</strong><span>Moyenne des tarifs actifs</span></div>
            </div>
            <div id="od-tarifs"></div>
        </section>
    </div>
</div>

<script>
(function () {
    var CLASSES = @json(config('offre.classes'));
    var NS = 'http://www.w3.org/2000/svg';
    var etat = @json($stats);
    var dernier = JSON.stringify(etat);

    var VERT_FONCE = '#0b3b2f', EMERAUDE = '#087A5A', FLUO = '#5CFFB0', OR = '#E9C46A', FOND = 'rgba(255,255,255,.08)';

    function svg(tag, attrs, parent) {
        var e = document.createElementNS(NS, tag);
        Object.keys(attrs).forEach(function (k) { e.setAttribute(k, attrs[k]); });
        if (parent) { parent.appendChild(e); }
        return e;
    }

    function anneaux(d) {
        var conteneur = document.getElementById('od-anneaux');
        conteneur.innerHTML = '';
        var total = d.scheduledDepartures + d.cancelledDepartures;
        var items = [
            { part: d.totalLines > 0 ? d.activeLines / d.totalLines : 0, centre: Math.round((d.totalLines > 0 ? d.activeLines / d.totalLines : 0) * 100) + ' %', nom: 'Lignes actives', detail: d.activeLines + ' sur ' + d.totalLines, couleur: '#5CFFB0' },
            { part: total > 0 ? d.scheduledDepartures / total : 0, centre: String(d.scheduledDepartures), nom: 'Départs programmés', detail: 'à venir', couleur: '#087A5A' },
            { part: total > 0 ? d.cancelledDepartures / total : 0, centre: String(d.cancelledDepartures), nom: 'Départs annulés', detail: 'historique', couleur: '#E9C46A' }
        ];
        var r = 62, c = 2 * Math.PI * r;
        items.forEach(function (it) {
            var bloc = document.createElement('div');
            bloc.className = 'od-anneau';
            var s = svg('svg', { viewBox: '0 0 170 170' }, bloc);
            svg('circle', { cx: 85, cy: 85, r: r, fill: 'none', stroke: '#E4EFEA', 'stroke-width': 16 }, s);
            var p = Math.max(0, Math.min(1, it.part));
            svg('circle', { cx: 85, cy: 85, r: r, fill: 'none', stroke: it.couleur, 'stroke-width': 16, 'stroke-linecap': 'round', transform: 'rotate(-90 85 85)', 'stroke-dasharray': (c * p) + ' ' + c }, s);
            svg('text', { x: 85, y: 93, 'text-anchor': 'middle', 'font-size': 26, 'font-weight': 800, fill: '#111827' }, s).textContent = it.centre;
            var n = document.createElement('div'); n.className = 'od-anneau-nom'; n.textContent = it.nom;
            var dt = document.createElement('div'); dt.className = 'od-anneau-detail'; dt.textContent = it.detail;
            bloc.appendChild(s); bloc.appendChild(n); bloc.appendChild(dt);
            conteneur.appendChild(bloc);
        });
    }

    function histogramme(d) {
        var racine = document.getElementById('od-histo');
        racine.innerHTML = '';
        var jours = d.creations;
        var max = Math.max(1, Math.max.apply(null, jours.map(function (j) { return j.n; })));
        var marge = 36, bas = 230, haut = 20, n = jours.length;
        var largeur = (640 - 2 * marge) / n;
        var barre = Math.max(6, largeur - 7);

        [0, 0.5, 1].forEach(function (p) {
            var y = bas - p * (bas - haut);
            svg('line', { x1: marge, x2: 640 - marge, y1: y, y2: y, stroke: 'rgba(255,255,255,.07)', 'stroke-width': 1 }, racine);
            svg('text', { x: marge - 10, y: y + 4, 'font-size': 11, 'text-anchor': 'end', class: 'od-histo-axe' }, racine).textContent = Math.round(max * p);
        });

        var defs = svg('defs', {}, racine);
        var grad = svg('linearGradient', { id: 'od-grad', x1: 0, y1: 0, x2: 0, y2: 1 }, defs);
        svg('stop', { offset: '0%', 'stop-color': FLUO }, grad);
        svg('stop', { offset: '100%', 'stop-color': EMERAUDE }, grad);

        jours.forEach(function (j, i) {
            var h = j.n > 0 ? Math.max(6, (j.n / max) * (bas - haut)) : 3;
            var x = marge + i * largeur + (largeur - barre) / 2;
            var y = bas - h;
            var rect = svg('rect', { x: x, y: y, width: barre, height: h, rx: 5, fill: j.n > 0 ? 'url(#od-grad)' : 'rgba(255,255,255,.12)' }, racine);
            rect.style.transformBox = 'fill-box';
            rect.style.transformOrigin = '50% 100%';
            rect.style.transform = 'scaleY(0)';
            rect.style.transition = 'transform .7s cubic-bezier(.2,.8,.2,1)';
            setTimeout(function () { rect.style.transform = 'scaleY(1)'; }, 30 + i * 12);
            var titre = svg('title', {}, rect);
            titre.textContent = j.date + ' : ' + j.n + ' création(s)';
            if (i % 5 === 0) {
                svg('text', { x: x + barre / 2, y: 254, 'font-size': 11, 'text-anchor': 'middle', class: 'od-histo-axe' }, racine).textContent = j.date;
            }
        });
    }

    function activite(d) {
        var liste = document.getElementById('od-activite');
        liste.innerHTML = '';
        if (!d.activite.length) {
            var vide = document.createElement('li'); vide.className = 'od-vide'; vide.textContent = 'Aucune activité pour le moment.';
            liste.appendChild(vide); return;
        }
        d.activite.forEach(function (a) {
            var li = document.createElement('li');
            var gauche = document.createElement('span');
            var badge = document.createElement('span'); badge.className = 'od-badge' + (a.type === 'Départ' ? ' depart' : ''); badge.textContent = a.type;
            gauche.appendChild(badge); gauche.appendChild(document.createTextNode(a.titre));
            var droite = document.createElement('span'); droite.className = 'od-quand'; droite.textContent = a.quand;
            li.appendChild(gauche); li.appendChild(droite);
            liste.appendChild(li);
        });
    }

    function tarifs(d) {
        var conteneur = document.getElementById('od-tarifs');
        conteneur.innerHTML = '';
        var max = Math.max(1, Math.max.apply(null, Object.keys(CLASSES).map(function (k) { return d.moyennes[k] || 0; })));
        Object.keys(CLASSES).forEach(function (code) {
            var v = d.moyennes[code] || 0;
            var ligne = document.createElement('div'); ligne.className = 'od-barre';
            var nom = document.createElement('span'); nom.textContent = CLASSES[code];
            var piste = document.createElement('div'); piste.className = 'od-piste';
            var rempli = document.createElement('div'); rempli.className = 'od-remplissage';
            piste.appendChild(rempli);
            var val = document.createElement('span'); val.className = 'od-valeur';
            val.textContent = v > 0 ? Math.round(v).toLocaleString('fr-FR') + ' FCFA' : '—';
            ligne.appendChild(nom); ligne.appendChild(piste); ligne.appendChild(val);
            conteneur.appendChild(ligne);
            setTimeout(function () { rempli.style.width = (v / max * 100) + '%'; }, 60);
        });
    }

    function tout(d) {
        anneaux(d);
        histogramme(d);
        activite(d);
        tarifs(d);
    }

    function actualiser() {
        fetch(@json(route('dashboard-offre.donnees')), { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (d) {
                if (!d) { return; }
                var sig = JSON.stringify(d);
                if (sig !== dernier) { dernier = sig; tout(d); }
            })
            .catch(function () {});
    }

    tout(etat);
    setInterval(actualiser, 8000);
    document.addEventListener('visibilitychange', function () { if (!document.hidden) { actualiser(); } });
})();
</script>
@endsection
