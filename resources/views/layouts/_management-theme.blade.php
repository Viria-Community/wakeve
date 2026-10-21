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
</style>
