<style>
/* Appliqué après les styles généraux pour homogénéiser le langage visuel des modules. */
.content { color: var(--text); }
.content :is(h1,h2,h3) { color: var(--text); }
.content h1 { font-size: clamp(25px, 2.4vw, 32px); letter-spacing: -.035em; line-height: 1.2; }
.content :is(.page-title,.companies-head,.agency-page-head,.employee-head,.fleet-head,.doc-head) { margin-bottom: 28px; }
.content :is(.breadcrumb,.agency-breadcrumb,.employee-breadcrumb,.fleet-breadcrumb,.doc-breadcrumb) { color: var(--muted); font-size: 12px; margin-bottom: 8px; }
.content :is(.muted,.metric-note,.summary-note) { color: var(--muted); }
.content :is(.panel,.dashboard-card,.company-panel,.company-form-card,.agency-card,.employee-card,.fleet-card,.doc-card,.agency-form-card,.employee-form,.fleet-form,.doc-form) {
    background: var(--surface); border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 3px 14px #26465305;
}
.content :is(.metrics,.company-summary,.agency-summary,.employee-summary,.fleet-summary,.doc-summary) { gap: 16px; }
.content :is(.metric,.summary-card,.agency-stat,.employee-stat,.fleet-stat,.doc-stat) {
    background: var(--surface); border-color: var(--border); border-radius: 12px; box-shadow: none;
}
.content :is(.metric-value,.summary-value,.agency-stat strong,.employee-stat strong,.fleet-stat strong,.doc-stat strong) {
    color: var(--text); font-variant-numeric: tabular-nums; letter-spacing: -.035em;
}
.content :is(.metric.warning,.fleet-stat.warning) { border-top-color: var(--warning); }
.content :is(.metric.success,.fleet-stat.success,.summary-card.approved) { border-top-color: var(--green); }
.content .summary-card.pending { border-top-color: var(--gold); }
.content .summary-card.restricted { border-top-color: var(--danger); }
.content :is(.agency-stat.success,.employee-stat.success) { border-top: 3px solid var(--green); }
.content .agency-stat.warning { border-top: 3px solid var(--warning); }
.content .employee-stat.account { border-top: 3px solid var(--gold); }
.content :is(.card-head,.agency-card-head,.employee-card-head,.fleet-card-head,.doc-card-head) { padding: 20px 22px; border-bottom-color: var(--border); gap: 16px; }
.content :is(.card-head,.agency-card-head,.employee-card-head,.fleet-card-head,.doc-card-head) h2 { font-size: 18px; letter-spacing: -.02em; }
.content :is(.company-filters,.agency-filters,.employee-filters,.fleet-filters,.doc-filters) { background: var(--bg); border-color: var(--border); gap: 12px; }
.content :is(input:not([type=checkbox]):not([type=radio]),select,textarea) { min-width: 0; border-color: var(--field-border); color: var(--text); }
.content :is(input,select,textarea):focus { border-color: var(--green); }
.content :is(input,select,textarea):focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); }
.content :is(.btn,.button) { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
.content :is(.btn,.button).success { background: var(--green); color: var(--surface); }
.content :is(.btn,.button).success:hover { background: var(--green-hover); }
.content :is(.btn,.button).danger { background: var(--danger-text); color: var(--surface); }
.content :is(.btn,.button).danger:hover { background: #851c28; }
.content :is(.btn,.button).warning { background: var(--warning); color: var(--text); }
.content :is(.btn,.button):not(:disabled):active { filter: brightness(.9); }
.content :is(.btn,.button):disabled { background: #d9e0dd; color: var(--muted); cursor: not-allowed; filter: none; }
.content :is(.table-action,.view-link,.agency-link,.employee-link,.fleet-link,.doc-link):hover { text-decoration: underline; text-underline-offset: 4px; }
.content table th { background: var(--bg); color: var(--muted); font-size: 11px; letter-spacing: .045em; font-weight: 750; }
.content table :is(th,td) { border-color: var(--border); }
.content table td { font-size: 14px; vertical-align: middle; }
.content tbody tr:hover { background: #f7faf8; }
.content :is(.status-active,.status-validated,.status-valid,.agency-status.active,.employee-status.active,.fleet-badge.active,.fleet-badge.available,.doc-status.valid,.health.valid) { background: var(--pale); color: #076047; }
.content .status-pending { background: var(--gold-pale); color: #765715; }
.content :is(.status-suspended,.status-to_renew,.doc-status.to_renew,.health.renew,.fleet-badge.unavailable) { background: var(--warning-pale); color: var(--warning-text); border: 1px solid var(--warning); }
.content :is(.status-rejected,.status-expired,.doc-status.expired,.health.expired) { background: var(--danger-pale); color: var(--danger-text); }
.content :is(.agency-status.inactive,.employee-status.inactive,.fleet-badge.inactive) { background: #edf0f1; color: var(--muted); }
.content :is(.empty,.empty-state,.empty-row,.doc-empty) { padding: 36px 24px; color: var(--muted); }
.content :is(.form-footer,.agency-form-footer,.employee-form-footer,.fleet-form-footer,.doc-form-footer) { background: var(--bg); border-color: var(--border); }
.content :is(.form-section-number,.form-section-head>span) { background: var(--pale); color: var(--green); }
.content :is(.agency-info,.employee-info,.company-form-grid,.agency-form-grid,.employee-form-grid,.fleet-grid,.doc-grid) { min-width: 0; }
.content :is(.agency-info,.employee-info) > * { min-width: 0; overflow-wrap: anywhere; }
.content :is(.agency-error,.employee-error,.fleet-error,.doc-error,.field-error-message) { color: var(--danger-text); }
.dashboard-heading { display: flex; justify-content: space-between; align-items: center; gap: 24px; }
.dashboard-heading .eyebrow { margin: 0 0 10px; color: var(--green); font-size: 11px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; }
.dashboard-heading p { max-width: 620px; line-height: 1.6; }
.dashboard-heading .btn { flex-shrink: 0; }
.dashboard-table-wrap { overflow-x: auto; }
.dashboard-grid > *, .right-column { min-width: 0; }
.distribution { display: grid; gap: 10px; padding: 20px 22px; }
.distribution a { display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 12px; border: 1px solid var(--border); border-radius: 8px; color: var(--text); text-decoration: none; }
.distribution a:hover { background: var(--bg); border-color: var(--green); }
.distribution strong { font-variant-numeric: tabular-nums; font-size: 20px; }
.sidebar :is(.brand small,.sidebar-user small,.nav-section,.nav-group summary) { color: #ccdade; }
.sidebar :focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); box-shadow: 0 0 0 2px var(--surface); }
:is(.topbar,.sidebar,.content) :is(a,button,summary,input,select,textarea):focus-visible { outline: var(--pnmg-focus); outline-offset: var(--pnmg-focus-offset); }
@media(max-width:1023.98px) {
    .dashboard-heading { align-items: flex-start; flex-direction: column; }
    .topbar-title { display: none; }
    .topbar-user { overflow-wrap: anywhere; }
}
@media(max-width:650px) {
    .topbar { flex-wrap: wrap; gap: 12px; }
    .topbar form[role=search] { order: 3; flex-basis: 100%; max-width: none; }
    .content :is(.card-head,.directory-head) { align-items: flex-start; flex-direction: column; }
    .content :is(.metrics,.company-summary,.agency-summary,.employee-summary,.fleet-summary,.doc-summary) { grid-template-columns: 1fr; }
    .dashboard-heading .btn { width: 100%; }
    .content .actions { flex-wrap: wrap; }
    .content :is(.agency-page-head,.employee-head,.fleet-head,.doc-head) > .btn { width: 100%; }
    .content :is(.company-alert-group summary) { align-items: flex-start; flex-direction: column; }
    .content .alert-counts { justify-content: flex-start; }
}
.mobile-brand,.mobile-menu-label{display:none}
@media(max-width:1023.98px){
    .topbar form[role=search]{display:none}
    .mobile-brand-full{display:block;width:160px;height:auto}
    .mobile-menu{min-width:44px;min-height:44px;margin-left:auto}
    .sidebar-mobile-head .btn-close{width:44px;height:44px;box-sizing:border-box;background-position:center}
    .offcanvas-backdrop.show{background-color:var(--dark);opacity:.35}
}
@media(max-width:599.98px){
    .topbar{height:56px;min-height:56px;padding:0 16px;flex-wrap:nowrap;gap:12px;background:var(--dark);color:#fff}
    .mobile-brand-full{display:none}
    .mobile-brand-icon{display:block;width:28px;height:28px;padding:3px;border-radius:5px;background:#fff}
    .mobile-menu{width:auto;min-width:44px;height:44px;flex:0 0 auto;padding:0 10px;gap:7px;border-color:#ffffff70;background:transparent;color:#fff}
    .mobile-menu-label{display:inline}
    .account-trigger{min-width:44px;min-height:44px;padding:4px;border-color:#ffffff70;background:transparent}
    .account-avatar{background:var(--gold);color:var(--dark)}
    .content{padding:24px 16px 40px}
    .content :is(input,select,textarea){font-size:16px}
    .nav-group-links a,.sidebar-logout{min-height:44px}
    .account-profile-link,.account-logout,.pnmg-pagination a,.pnmg-pagination li>span{min-height:44px}
}

/* ===== PNMG — finition visuelle globale ===== */
:root{
    --pnmg-shadow-1: 0 1px 2px rgba(38, 70, 83, .05);
    --pnmg-shadow-2: 0 14px 34px rgba(38, 70, 83, .08);
    --pnmg-ring: 0 0 0 3px rgba(8, 122, 90, .14);
}

body { font-size: 14.5px; -webkit-font-smoothing: antialiased; }

/* --- Navigation latérale --- */
.sidebar { background: var(--dark); }
.sidebar .brand { margin: 0 4px 22px; padding: 0 4px 20px; border-bottom: 1px solid rgba(255, 255, 255, .12); }
.sidebar .brand img { display: block; width: 184px; height: auto; }
.side-nav { gap: 2px; }
.side-nav a, .side-nav .nav-soon { min-height: 42px; border-radius: 7px; color: #cfe1dd; font-size: 13.5px; transition: background-color .15s ease, color .15s ease; }
.side-nav a:hover, .side-nav .nav-soon:hover { background: rgba(255, 255, 255, .07); color: #fff; }
.side-nav a.active { background: rgba(255, 255, 255, .09); color: #fff; font-weight: 700; }
.side-nav a.active:hover { background: rgba(255, 255, 255, .12); }
.nav-group-title { color: #93bdb1; font-size: 10.5px; letter-spacing: .11em; }
.nav-group-links a { min-height: 38px; }
.offre-menu summary { border-radius: 7px; transition: background-color .15s ease; }
.offre-menu summary:hover { background: rgba(255, 255, 255, .07); }
.nav-soon small { font-size: 8.5px; letter-spacing: .06em; }
.sidebar-footer { margin-top: 10px; padding: 14px 4px 0; border-top-color: rgba(255, 255, 255, .14); }
.sidebar-logout { border-radius: 7px; font-size: 12.5px; }
.sidebar-logout:hover { background: rgba(255, 255, 255, .08); }

/* --- Barre supérieure --- */
.topbar { min-height: 68px; padding: 0 32px; background: #fff; border-bottom: 1px solid var(--border); box-shadow: 0 1px 0 rgba(38, 70, 83, .03); }
.topbar-title { color: #5f7076; font-size: 13px; letter-spacing: .01em; }
.topbar form[role=search] { max-width: 420px; }
.topbar input { height: 40px; padding: 9px 14px; border: 1px solid transparent; border-radius: 9px; background: #f4f7f6; font-size: 13.5px; transition: background-color .16s ease, border-color .16s ease, box-shadow .16s ease; }
.topbar input:hover { background: #eff4f2; }
.topbar input:focus { background: #fff; border-color: var(--green); outline: none; box-shadow: var(--pnmg-ring); }
.topbar input::placeholder { color: #8b9a9f; }

/* --- Zone de contenu --- */
.content { max-width: 1360px; padding: 34px 32px 56px; }
.content .page-title h1, .content .dashboard-heading h1 { font-size: 28px; letter-spacing: -.03em; }
.content .page-title p, .content .dashboard-heading p { font-size: 13.5px; line-height: 1.6; }
.dashboard-heading .eyebrow { margin-bottom: 8px; font-size: 10.5px; letter-spacing: .13em; }

/* --- Cartes et panneaux --- */
.content :is(.panel, .dashboard-card, .company-panel, .company-form-card, .agency-card, .employee-card, .fleet-card, .doc-card, .agency-form-card, .employee-form, .fleet-form, .doc-form, .summary-card) {
    border: 1px solid #e2e9e6;
    border-radius: 12px;
    box-shadow: var(--pnmg-shadow-1);
}
.content :is(.card-head, .agency-card-head, .employee-card-head, .fleet-card-head, .doc-card-head) { padding: 18px 20px; }
.content :is(.card-head, .agency-card-head, .employee-card-head, .fleet-card-head, .doc-card-head) h2 { font-size: 16.5px; }
.content :is(.card-head a, .table-action) { font-size: 13px; }
.content :is(.empty, .empty-state, .empty-row, .doc-empty) { padding: 36px 20px; font-size: 13.5px; }

/* --- Indicateurs --- */
.content :is(.metric, .summary-card, .agency-stat, .employee-stat, .fleet-stat, .doc-stat) { border-radius: 11px; }
.content .metric { padding: 20px; }
.content .metric-label { color: #64747a; font-size: 10.5px; font-weight: 800; letter-spacing: .07em; }
.content .metric-value { font-size: 30px; line-height: 1.1; }
.content :is(.metric-note, .summary-note) { font-size: 11.5px; }
.content .metric.success { border-top: 3px solid var(--green); }
.content .metric.warning { border-top: 3px solid var(--gold); }

/* --- Boutons --- */
.content :is(.btn, .button) {
    min-height: 42px;
    padding: 0 16px;
    border-radius: 8px;
    font-size: 13.5px;
    font-weight: 700;
    box-shadow: none;
    transition: background-color .16s ease, border-color .16s ease, color .16s ease;
}
.content :is(.btn, .button).secondary { border-color: #ccd8d4; background: #fff; color: var(--text); }
.content :is(.btn, .button).secondary:hover { background: var(--bg); border-color: #b9ccc5; }
.content :is(.btn, .button).danger { background: var(--danger-text); color: #fff; }
.content :is(.btn, .button).warning { background: var(--warning); color: #3c2a0d; }

/* --- Formulaires --- */
.content label { margin-bottom: 6px; color: #2c4a56; font-size: 12.5px; font-weight: 700; }
.content :is(input:not([type=checkbox]):not([type=radio]), select, textarea) {
    min-height: 42px;
    padding: 9px 12px;
    border: 1px solid #c7d3cf;
    border-radius: 8px;
    background: #fff;
    font-size: 14px;
    transition: border-color .16s ease, box-shadow .16s ease;
}
.content :is(input, select, textarea):hover { border-color: #aebfba; }
.content :is(input, select, textarea):focus { border-color: var(--green); outline: none; box-shadow: var(--pnmg-ring); }
.content :is(input, select, textarea)[aria-invalid=true] { border-color: var(--danger); }
.content :is(input, select, textarea)[aria-invalid=true]:focus { box-shadow: 0 0 0 3px rgba(230, 57, 70, .12); }
.content :is(.field-error-message, .agency-error, .employee-error, .fleet-error, .doc-error, .field-error) { font-size: 12px; }
.content .filters, .content :is(.company-filters, .agency-filters, .employee-filters, .fleet-filters, .doc-filters) { border-radius: 10px; }

/* --- Messages --- */
.flash { padding: 12px 15px; border: 1px solid #c6e3d8; border-left: 3px solid var(--green); border-radius: 8px; background: var(--pale); color: #1d4f40; font-size: 13.5px; }
.errors { padding: 12px 15px; border: 1px solid #f0c9cc; border-left: 3px solid var(--danger); border-radius: 8px; background: var(--danger-pale); color: var(--danger-text); font-size: 13.5px; }

/* --- Tableaux --- */
.content table th { padding: 11px 12px; background: #f6f9f8; color: #5c6c72; font-size: 10.5px; font-weight: 800; letter-spacing: .07em; white-space: nowrap; }
.content table td { padding: 13px 12px; border-bottom: 1px solid #edf1ef; font-size: 13.5px; }
.content tbody tr:hover { background: #f8fbfa; }
.content table td small, .content table th small { color: var(--muted); font-size: 11.5px; }
.content :is(.table-wrap, .dashboard-table-wrap, .suspended-table-wrap) { border-radius: 0 0 12px 12px; }

/* --- Badges et statuts --- */
.content :is(.badge, .role-badge, .status-pill, .doc-status, .review-status, .scope-badge) {
    padding: 4px 9px;
    border-radius: 999px;
    font-size: 10.5px;
    font-weight: 800;
    letter-spacing: .04em;
}
.content :is(.status-active, .status-valid, .status-validated, .health.valid, .doc-status.valid, .review-status.approved, .agency-status.active, .employee-status.active, .fleet-badge.active, .fleet-badge.available) { background: #e4f3ed; color: #07604a; }
.content :is(.status-pending, .status-to_renew, .doc-status.to_renew, .health.renew, .fleet-badge.unavailable) { background: #fdf4e1; color: #765715; border: 1px solid #eddaa6; }
.content :is(.status-suspended, .status-rejected, .status-expired, .doc-status.expired, .health.expired) { background: #fdeeee; color: #9e2f38; }
.content :is(.agency-status.inactive, .employee-status.inactive, .fleet-badge.inactive) { background: #eef1f2; color: #5f7076; }

/* --- Menu de compte --- */
.account-trigger { padding: 6px 9px; border-radius: 9px; border-color: #e0e7e4; transition: border-color .16s ease, box-shadow .16s ease; }
.account-trigger:hover, .account-trigger[aria-expanded=true] { border-color: #b9d3c9; box-shadow: 0 0 0 3px #eef6f2; }
.account-avatar { border-radius: 8px; }
.account-dropdown { border-radius: 12px; border-color: #e2e9e6; box-shadow: 0 16px 40px rgba(23, 63, 52, .14); }
.account-dropdown-head { background: var(--dark); }
.account-details > div { border-bottom-color: #eef2f0; }
.account-actions { background: #f6f9f8; }
.account-profile-link, .account-logout { border-radius: 7px; }

/* --- Notifications --- */
.notification-center { border-radius: 10px; border-color: #f0c9cc; border-left-width: 3px; }
.notification-entry { border-radius: 7px; background: #fffafa; }
.notification-entry:hover { background: #fdf4f4; }

/* --- Pagination --- */
.pnmg-pagination a, .pnmg-pagination li>span { border-radius: 7px; font-size: 12.5px; }
.pnmg-pagination a:hover { background: #f4f8f6; }

/* --- Ajustements responsives --- */
@media (max-width: 1200px) {
    .content { padding: 28px 24px 48px; }
}
@media (max-width: 1023.98px) {
    .topbar { padding: 12px 18px; min-height: 64px; }
    .sidebar .brand { margin-bottom: 16px; }
}
@media (max-width: 760px) {
    .content { padding: 24px 16px 40px; }
    .content .page-title h1, .content .dashboard-heading h1 { font-size: 24px; }
    .content :is(.card-head, .agency-card-head, .employee-card-head, .fleet-card-head, .doc-card-head) { padding: 16px; }
    .content .metric { padding: 17px; }
}

</style>
<style>
/* Compatibilité mobile : conserve la barre supérieure sombre et les cibles tactiles. */
@media (max-width: 599.98px) {
    .topbar { height: 56px; min-height: 56px; padding: 0 16px; background: var(--dark); box-shadow: none; }
    .content { padding: 24px 16px 40px; }
    .content :is(input:not([type=checkbox]):not([type=radio]), select, textarea) { font-size: 16px; }
    .nav-group-links a, .sidebar-logout { min-height: 44px; }
}
@media (prefers-reduced-motion: reduce) {
    .side-nav a, .topbar input, .account-trigger, .feature-card, .content :is(.btn, .button) { transition: none; }
}
</style>
