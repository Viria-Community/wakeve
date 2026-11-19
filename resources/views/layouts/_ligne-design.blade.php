<style>
.ligne-arrets { position: relative; display: flex; justify-content: space-between; align-items: center; margin: 18px 0; }
.ligne-arrets::before { content: ""; position: absolute; left: 8px; right: 8px; top: 50%; height: 3px; border-radius: 3px; background: linear-gradient(90deg, #087A5A, #E9C46A); transform: translateY(-50%); opacity: .55; }
.arret { position: relative; z-index: 1; width: 14px; height: 14px; border-radius: 50%; background: #fff; border: 3px solid #087A5A; }
.arret.is-actif { background: #E9C46A; border-color: #E9C46A; box-shadow: 0 0 0 6px rgba(233, 196, 106, .25); }

.billet { position: relative; background: #fff; border: 1px solid #e6e2d6; border-radius: 16px; padding: 20px 22px; box-shadow: 0 10px 30px rgba(38, 70, 83, .10); overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; }
.billet::before, .billet::after { content: ""; position: absolute; top: 50%; width: 18px; height: 18px; border-radius: 50%; background: #F7F5EF; transform: translateY(-50%); }
.billet::before { left: -9px; }
.billet::after { right: -9px; }
.billet:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(38, 70, 83, .16); }

.btn::before, .btn-action::before, .nav-cta::before, .button::before {
    content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 3px;
    background: linear-gradient(90deg, transparent, #E9C46A, transparent);
    transform: translateX(-100%); pointer-events: none;
}
.btn:hover::before, .btn-action:hover::before, .nav-cta:hover::before, .button:hover::before {
    animation: trajet-ligne 1.1s ease forwards;
}
@keyframes trajet-ligne { to { transform: translateX(100%); } }

@media (prefers-reduced-motion: reduce) {
    .btn::before, .btn-action::before, .nav-cta::before, .button::before, .billet { animation: none !important; transition: none !important; }
}

.billets { display: grid; gap: 18px; padding: 4px 0; }
.billet-tete { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
.billet-titre { font-weight: 700; color: #264653; font-size: 17px; }
.billet-sous { color: #64748b; font-size: 13px; margin-top: 2px; }
.billet-trajet { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-weight: 600; color: #264653; margin-top: 2px; }
.fleche { color: #E9C46A; font-size: 20px; }
.billet-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 14px; }
.billet-vide { text-align: center; color: #64748b; }

.dashboard-kpis { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; margin-bottom: 22px; }
.kpi-card { position: relative; background: #fff; border: 1px solid #e6e2d6; border-radius: 18px; padding: 22px 22px 26px; box-shadow: 0 10px 28px rgba(38, 70, 83, .09); overflow: hidden; transition: transform .25s ease, box-shadow .25s ease; }
.kpi-card:hover { transform: translateY(-3px); box-shadow: 0 18px 40px rgba(38, 70, 83, .16); }
.kpi-card::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: linear-gradient(180deg, #087A5A, #E9C46A); }
.kpi-label { font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: .06em; font-weight: 600; }
.kpi-value { font-size: 40px; font-weight: 800; color: #264653; margin: 8px 0 4px; line-height: 1; }
.kpi-sub { font-size: 13px; color: #64748b; }
.kpi-card::after { content: ""; position: absolute; left: 22px; right: 22px; bottom: 12px; height: 6px; border-radius: 6px; background: #efece2; }
.kpi-card { isolation: isolate; }
.kpi-card::after { background: linear-gradient(90deg, #087A5A, #E9C46A) left / 60% 100% no-repeat, #efece2; }

.dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 18px; }
.dashboard-card { background: #fff; border: 1px solid #e6e2d6; border-radius: 18px; padding: 20px 22px; box-shadow: 0 10px 28px rgba(38, 70, 83, .09); transition: transform .25s ease, box-shadow .25s ease; }
.dashboard-card:hover { transform: translateY(-2px); box-shadow: 0 16px 36px rgba(38, 70, 83, .15); }
.dashboard-card ul { list-style: none; margin: 0; padding: 0; }
.dashboard-card li { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-top: 1px dashed #e6e2d6; }
.dashboard-card li:first-child { border-top: 0; }
.item-label { color: #475569; font-size: 14px; }
.item-value { font-weight: 800; font-size: 18px; color: #264653; }

main table { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border: 1px solid #e6e2d6; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 28px rgba(38, 70, 83, .08); }
main thead th { background: #F7F5EF; color: #264653; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; font-weight: 700; padding: 14px 16px; text-align: left; }
main tbody td { padding: 14px 16px; border-top: 1px solid #efece2; color: #334155; vertical-align: middle; }
main tbody tr { transition: background .2s ease; }
main tbody tr:hover { background: #fbf9f3; }
main tbody tr:hover td:first-child { box-shadow: inset 4px 0 0 #E9C46A; }

.status-label { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; }
.status-active, .status-validated { background: #e6f5ef; color: #066b4d; }
.status-inactive, .status-suspended { background: #eef0f3; color: #475569; }
.status-pending { background: #fdf3d7; color: #8a6a10; }
.status-rejected { background: #fde8e8; color: #b42318; }

.sidebar { background: linear-gradient(180deg, #0f3d33 0%, #264653 100%) !important; border-right: 1px solid rgba(255,255,255,.08); box-shadow: 8px 0 30px rgba(11,47,40,.18); }
.sidebar .brand { padding: 6px 8px 18px; margin-bottom: 8px; border-bottom: 1px solid rgba(255,255,255,.08); display: block; }
.side-nav a { position: relative; display: flex; align-items: center; gap: 11px; border-radius: 12px; margin: 3px 0; padding: 10px 12px; color: rgba(255,255,255,.88); text-decoration: none; transition: background .2s ease, transform .2s ease, color .2s ease; }
.side-nav a:hover { background: rgba(255,255,255,.09); color: #fff; transform: translateX(3px); }
.side-nav a.active { background: linear-gradient(90deg, rgba(233,196,106,.24), rgba(233,196,106,.04)); color: #fff; box-shadow: inset 3px 0 0 #E9C46A; }
.side-nav a .nav-icon { color: rgba(255,255,255,.75); transition: transform .2s ease, color .2s ease; }
.side-nav a:hover .nav-icon, .side-nav a.active .nav-icon { color: #E9C46A; transform: scale(1.12); }
.nav-group-title { font-size: 11px !important; letter-spacing: .12em; text-transform: uppercase; color: #E9C46A !important; opacity: .9; font-weight: 800; margin: 18px 0 6px 6px; }
.sidebar-logout { border-radius: 14px !important; transition: transform .2s ease, background .2s ease, box-shadow .2s ease; }
.sidebar-logout:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(0,0,0,.2); }

main h1 { font-size: 30px; font-weight: 800; color: #264653; letter-spacing: -.01em; margin: 6px 0 4px; }
main h1::after { content: ""; display: block; width: 56px; height: 4px; border-radius: 4px; margin-top: 10px; background: linear-gradient(90deg, #087A5A, #E9C46A); }
main .breadcrumb, main nav[aria-label="Fil d'Ariane"] { font-size: 13px; color: #64748b; letter-spacing: .01em; }
main .breadcrumb a { color: #087A5A; font-weight: 600; text-decoration: none; }
main .breadcrumb a:hover { text-decoration: underline; }
main .muted, main p.muted { color: #64748b; font-size: 14px; }

.btn, .btn-action { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 42px; padding: 0 18px; border-radius: 12px; font-weight: 700; font-size: 14px; text-decoration: none; cursor: pointer; }
.btn { background: linear-gradient(135deg, #087A5A, #0a6b4f); color: #fff; border: 0; box-shadow: 0 10px 22px rgba(8,122,90,.25); }
.btn.secondary { background: #fff; color: #264653; border: 1.5px solid #d6d3c7; box-shadow: none; }
.btn.secondary:hover { border-color: #087A5A; color: #087A5A; }
.btn-action.secondary { background: #fff; color: #264653; border: 1.5px solid #d6d3c7; }
.btn-action.primary { background: linear-gradient(135deg, #087A5A, #0a6b4f); color: #fff; border: 0; }
.btn-action.danger { background: linear-gradient(135deg, #b42318, #8f1d14); color: #fff; border: 0; }
.btn-action:hover { border-color: #087A5A; color: #087A5A; }
.btn-action.danger:hover, .btn-action.primary:hover { color: #fff; border-color: transparent; }

.account-menu, .account-trigger, [class*="account"] > summary, .user-menu summary {
    border-radius: 14px; transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}
.account-menu:hover, .account-trigger:hover, .user-menu summary:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(38,70,83,.16); }
.account-dropdown, .account-panel, .user-menu-panel {
    border-radius: 16px; border: 1px solid #e6e2d6; box-shadow: 0 18px 40px rgba(38,70,83,.18); padding: 8px; background: #fff;
}
.account-dropdown a, .account-panel a, .user-menu-panel a {
    display: block; padding: 10px 12px; border-radius: 10px; color: #334155; text-decoration: none; transition: background .2s ease, color .2s ease;
}
.account-dropdown a:hover, .account-panel a:hover, .user-menu-panel a:hover { background: #fbf9f3; color: #087A5A; }

.steps, .onboarding-steps, .stepper, .progress-steps {
    display: flex; align-items: center; justify-content: center; gap: 0; margin: 24px auto 30px; padding: 0; list-style: none;
}
.steps li, .onboarding-steps li, .stepper li, .progress-steps li { display: flex; flex-direction: column; align-items: center; position: relative; flex: 1; font-size: 13px; color: #64748b; font-weight: 600; }
.steps li::before, .onboarding-steps li::before, .stepper li::before, .progress-steps li::before {
    content: attr(data-step); display: grid; place-items: center; width: 34px; height: 34px; border-radius: 50%;
    background: #fff; border: 2px solid #d6d3c7; color: #64748b; font-weight: 800; margin-bottom: 8px; transition: all .3s ease;
}
.steps li.is-done::before, .steps li.is-active::before, .onboarding-steps li.is-done::before, .onboarding-steps li.is-active::before,
.stepper li.is-done::before, .stepper li.is-active::before, .progress-steps li.is-done::before, .progress-steps li.is-active::before {
    background: linear-gradient(135deg, #087A5A, #0a6b4f); border-color: #087A5A; color: #fff; box-shadow: 0 0 0 5px rgba(233,196,106,.25);
}
.steps li.is-active, .onboarding-steps li.is-active, .stepper li.is-active, .progress-steps li.is-active { color: #264653; }

.topbar { display: flex; align-items: center; gap: 12px; }
.topbar .account-menu { margin-left: auto; }
.topbar .cloche { margin-left: 0; }
.account-trigger-copy, .account-chevron { display: none !important; }
.account-trigger {
    width: 44px; height: 44px; padding: 0; border: 0; border-radius: 50%; background: transparent;
    display: grid; place-items: center; cursor: pointer; position: relative;
    transition: transform .2s ease, box-shadow .2s ease;
}
.account-trigger::after {
    content: ""; position: absolute; inset: -3px; border-radius: 50%;
    background: conic-gradient(#E9C46A, #087A5A, #E9C46A); z-index: -1; opacity: .9;
}
.account-trigger:hover { transform: translateY(-2px); }
.account-trigger .account-avatar { width: 38px; height: 38px; font-size: 13px; }
.account-trigger:focus-visible { outline: 3px solid rgba(8,122,90,.35); outline-offset: 3px; }

.topbar-actions { display: flex; align-items: center; gap: 12px; margin-left: auto; }
.topbar .account-menu { margin-left: 0; }

.topbar-actions { display: flex !important; flex-direction: row !important; align-items: center; gap: 12px; margin-left: auto; }
.topbar-actions .cloche, .topbar-actions .account-menu { display: inline-block !important; margin: 0 !important; }
.cloche-bouton, .account-trigger {
    width: 46px !important; height: 46px !important; padding: 0 !important; border-radius: 14px !important;
    display: grid !important; place-items: center; background: rgba(255,255,255,.92) !important;
    border: 1px solid rgba(8,122,90,.18) !important; box-shadow: 0 6px 16px rgba(36,70,83,.10);
    color: #264653 !important; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease;
}
.cloche-bouton:hover, .account-trigger:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(8,122,90,.2); }
.account-trigger::after { display: none !important; }
.account-trigger svg, .cloche-bouton svg { width: 20px !important; height: 20px !important; stroke-width: 1.8; }

.topbar .account-trigger, .topbar-actions .account-trigger {
    display: flex !important; align-items: center; justify-content: center;
    min-width: 46px !important; min-height: 46px !important; width: 46px !important; height: 46px !important;
    color: #264653 !important; background: rgba(255,255,255,.95) !important;
    border: 1px solid rgba(8,122,90,.18) !important; border-radius: 14px !important; padding: 0 !important;
}
.topbar .account-trigger svg { display: block; width: 22px; height: 22px; flex-shrink: 0; }
</style>
