<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="La Plateforme Nationale de Mobilité du Gabon centralise l’enregistrement, la validation et la gestion administrative des compagnies de transport.">
    <link rel="canonical" href="{{ route('home') }}">
    <link rel="icon" href="{{ asset('images/pnmg/icone-couleur.svg') }}" type="image/svg+xml">
    <meta property="og:locale" content="fr_GA"><meta property="og:type" content="website"><meta property="og:site_name" content="PNMG">
    <meta property="og:title" content="PNMG — Plateforme Nationale de Mobilité du Gabon">
    <meta property="og:description" content="Enregistrement et gestion administrative des compagnies de transport au Gabon.">
    <meta property="og:url" content="{{ route('home') }}"><meta property="og:image" content="{{ asset('images/social-preview.png') }}"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta property="og:image:alt" content="Identité visuelle de la Plateforme Nationale de Mobilité du Gabon"><meta name="twitter:card" content="summary_large_image">
    <title>PNMG — Plateforme Nationale de Mobilité du Gabon</title>
    @include('layouts._palette')
    <style>
        *{box-sizing:border-box}html{scroll-behavior:smooth}body{margin:0;background:var(--bg);color:var(--text);font-family:"Maiandra GD", Calibri, "Segoe UI", Arial, sans-serif;line-height:1.6}body.nav-open{overflow:hidden}h1,h2,h3,.brand-name{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-weight:700}a{color:inherit}.skip-link{position:fixed;top:-60px;left:16px;z-index:100;padding:10px 14px;border-radius:7px;background:#fff;color:var(--dark);font-weight:700}.skip-link:focus{top:12px}.site-header{position:sticky;top:0;z-index:40;border-bottom:1px solid #ffffff20;background:#264653f5;color:#fff;backdrop-filter:blur(14px)}.header-inner,.section-inner,.footer-inner{width:min(1180px,calc(100% - 40px));margin:auto}.header-inner{min-height:76px;display:flex;align-items:center;gap:32px}.brand{display:flex;align-items:center;gap:12px;text-decoration:none}.brand-mark{width:42px;height:42px;display:grid;place-items:center;border:1px solid #ffffff45;border-radius:12px;background:var(--green);box-shadow:inset 0 0 0 4px #ffffff12}.brand-mark svg{width:24px;stroke:#fff;fill:none;stroke-width:1.8}.brand-name{display:block;font-size:18px;line-height:1}.brand small{display:block;margin-top:5px;color:#bdd4d5;font-size:9px;font-weight:700;letter-spacing:.14em;text-transform:uppercase}.site-nav{margin-left:auto;display:flex;align-items:center;gap:8px}.site-nav>a:not(.nav-cta){padding:10px 11px;color:#dce7e9;font-size:13px;text-decoration:none}.site-nav>a:not(.nav-cta):hover{color:#fff}.nav-login{border-left:1px solid #ffffff30;margin-left:6px;padding-left:20px!important}.nav-cta,.button{display:inline-flex;align-items:center;justify-content:center;gap:9px;min-height:46px;padding:11px 18px;border:1px solid transparent;border-radius:9px;background:var(--green);color:#fff;text-decoration:none;font-weight:800;line-height:1.2;transition:transform .18s,background .18s,box-shadow .18s}.nav-cta{min-height:42px;margin-left:4px;padding:9px 14px;font-size:13px}.button:hover,.nav-cta:hover{transform:translateY(-1px);background:#066348;box-shadow:0 10px 24px #021b1430}.button.secondary{border-color:#c9d6d3;background:#fff;color:var(--dark)}.button.secondary:hover{background:var(--bg)}.menu-button{display:none;margin-left:auto;padding:9px 11px;border:1px solid #ffffff35;border-radius:8px;background:transparent;color:#fff;font:inherit;font-weight:700}.hero{position:relative;overflow:hidden;background:var(--dark);color:#fff}.hero:before{content:"";position:absolute;width:520px;height:520px;right:-170px;top:-230px;border:1px solid #ffffff15;border-radius:50%;box-shadow:0 0 0 70px #ffffff08,0 0 0 140px #ffffff05}.hero-inner{position:relative;min-height:650px;display:grid;grid-template-columns:minmax(0,1.08fr) minmax(390px,.92fr);align-items:center;gap:72px;padding-top:80px;padding-bottom:92px}.eyebrow{display:inline-flex;align-items:center;gap:9px;margin:0 0 20px;color:var(--gold);font-size:11px;font-weight:800;letter-spacing:.15em;text-transform:uppercase}.eyebrow:before{content:"";width:28px;height:2px;background:var(--gold)}.hero h1{max-width:720px;margin:0;font-size:clamp(40px,5.7vw,68px);line-height:1.03;letter-spacing:-.045em}.hero h1 span{color:var(--gold)}.hero-copy>p{max-width:650px;margin:24px 0 30px;color:#d8e4e5;font-size:18px}.hero-actions{display:flex;align-items:center;gap:12px;flex-wrap:wrap}.hero .button.secondary{border-color:#ffffff55;background:transparent;color:#fff}.hero .button.secondary:hover{background:#ffffff12}.hero-note{display:flex;gap:22px;margin-top:30px;color:#c7d9d9;font-size:12px}.hero-note span{display:flex;align-items:center;gap:7px}.hero-note span:before{content:"";width:19px;height:19px;display:grid;place-items:center;border-radius:50%;background:#ffffff14;color:var(--gold);font-weight:900}.platform-preview{position:relative}.preview-window{position:relative;padding:18px;border:1px solid #ffffff35;border-radius:20px;background:#f8fbfa;box-shadow:0 34px 70px #071d2290;color:var(--text);transform:rotate(1deg)}.preview-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:17px}.preview-logo{display:flex;align-items:center;gap:8px;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-weight:700}.preview-logo i{width:26px;height:26px;border-radius:7px;background:var(--green)}.preview-dots{display:flex;gap:5px}.preview-dots i{width:6px;height:6px;border-radius:50%;background:#b6c4c3}.preview-card{padding:17px;border:1px solid var(--border);border-radius:12px;background:#fff}.preview-head{display:flex;align-items:center;justify-content:space-between;gap:10px}.preview-head small{color:var(--muted)}.preview-status{padding:5px 8px;border-radius:99px;background:var(--gold-pale);color:#805d16;font-size:9px;font-weight:900;text-transform:uppercase}.preview-progress{position:relative;height:5px;margin:17px 0 20px;border-radius:99px;background:#e2e9e7}.preview-progress:before{content:"";position:absolute;inset:0 32% 0 0;border-radius:inherit;background:var(--green)}.preview-steps{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}.preview-step{padding:12px 9px;border-radius:9px;background:var(--bg)}.preview-step b{display:block;margin-bottom:4px;font-size:11px}.preview-step small{display:block;color:var(--muted);font-size:9px}.preview-step.done{background:var(--pale)}.preview-metrics{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:12px}.preview-metric{padding:13px;border-radius:10px;background:#fff;border:1px solid var(--border)}.preview-metric strong{display:block;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:22px}.preview-metric span{color:var(--muted);font-size:9px}.preview-accent{position:absolute;right:-28px;bottom:-30px;width:118px;padding:14px;border-radius:13px;background:var(--gold);color:var(--dark);box-shadow:0 18px 35px #061f2260}.preview-accent strong,.preview-accent span{display:block}.preview-accent strong{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:24px}.preview-accent span{font-size:10px;font-weight:700}.modes-strip{position:relative;margin-top:-30px}.modes-panel{display:grid;grid-template-columns:1.35fr repeat(4,1fr);align-items:center;min-height:88px;border:1px solid var(--border);border-radius:14px;background:#fff;box-shadow:0 15px 40px #26465312}.modes-title{padding:20px 26px;border-right:1px solid var(--border)}.modes-title strong,.modes-title small{display:block}.modes-title strong{font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:17px}.modes-title small{color:var(--muted);font-size:11px}.mode-item{display:flex;align-items:center;justify-content:center;gap:9px;padding:18px 10px;font-size:12px;font-weight:800}.mode-icon{width:33px;height:33px;display:grid;place-items:center;border-radius:9px;background:var(--pale);color:var(--green)}.mode-icon svg,.feature-icon svg,.assurance-icon svg{width:19px;height:19px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}.content-section{padding:96px 0}.content-section.soft{background:#f0f5f2}.section-heading{max-width:720px;margin-bottom:42px}.section-heading.center{margin-right:auto;margin-left:auto;text-align:center}.section-kicker{display:block;margin-bottom:8px;color:var(--green);font-size:11px;font-weight:900;letter-spacing:.14em;text-transform:uppercase}.section-heading h2{margin:0;font-size:clamp(30px,4vw,44px);line-height:1.12;letter-spacing:-.035em}.section-heading p{margin:16px 0 0;color:var(--muted);font-size:16px}.feature-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:18px}.feature-card{min-height:225px;padding:25px;border:1px solid var(--border);border-radius:14px;background:#fff;transition:transform .18s,border-color .18s,box-shadow .18s}.feature-card:hover{transform:translateY(-4px);border-color:#b5d1c8;box-shadow:0 16px 35px #2646530d}.feature-icon{width:45px;height:45px;display:grid;place-items:center;margin-bottom:22px;border-radius:11px;background:var(--pale);color:var(--green)}.feature-card:nth-child(2) .feature-icon,.feature-card:nth-child(5) .feature-icon{background:var(--gold-pale);color:#8a681c}.feature-card h3{margin:0 0 8px;font-size:20px}.feature-card p{margin:0;color:var(--muted);font-size:14px}.scope-note{display:flex;align-items:flex-start;gap:14px;margin-top:22px;padding:17px 20px;border-left:3px solid var(--gold);border-radius:8px;background:#fffaf0;color:#69562d;font-size:13px}.scope-note strong{color:var(--dark)}.journey-layout{display:grid;grid-template-columns:minmax(260px,.7fr) minmax(0,1.3fr);gap:70px;align-items:start}.journey-intro{position:sticky;top:116px}.journey-intro h2{margin:0 0 16px;font-size:42px;line-height:1.1}.journey-intro p{margin:0 0 24px;color:var(--muted)}.journey-list{counter-reset:steps;display:grid;gap:14px}.journey-step{counter-increment:steps;display:grid;grid-template-columns:54px 1fr;gap:18px;padding:22px;border:1px solid var(--border);border-radius:13px;background:#fff}.journey-step:before{content:counter(steps,decimal-leading-zero);width:48px;height:48px;display:grid;place-items:center;border-radius:12px;background:var(--dark);color:var(--gold);font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:17px;font-weight:700}.journey-step h3{margin:1px 0 5px;font-size:18px}.journey-step p{margin:0;color:var(--muted);font-size:13px}.assurance{padding:96px 0;background:var(--dark);color:#fff}.assurance-layout{display:grid;grid-template-columns:1fr 1fr;gap:70px;align-items:center}.assurance-copy h2{margin:0;font-size:clamp(32px,4vw,46px);line-height:1.1}.assurance-copy>p{max-width:570px;margin:18px 0 0;color:#cedcdd}.assurance-list{display:grid;grid-template-columns:1fr 1fr;gap:13px}.assurance-item{display:grid;grid-template-columns:40px 1fr;gap:12px;padding:17px;border:1px solid #ffffff25;border-radius:12px;background:#ffffff08}.assurance-icon{width:38px;height:38px;display:grid;place-items:center;border-radius:9px;background:#ffffff0f;color:var(--gold)}.assurance-item strong,.assurance-item small{display:block}.assurance-item strong{font-size:13px}.assurance-item small{margin-top:3px;color:#bdd0d1;font-size:10px}.role-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:15px}.role-card{position:relative;overflow:hidden;padding:25px 22px;border:1px solid var(--border);border-radius:14px;background:#fff}.role-card:before{content:"";position:absolute;top:0;left:0;width:100%;height:4px;background:var(--green)}.role-card:nth-child(2):before{background:var(--gold)}.role-card:nth-child(3):before{background:var(--warning)}.role-card:nth-child(4):before{background:var(--dark)}.role-card span{display:block;margin-bottom:18px;color:#8a979c;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:30px;font-weight:700}.role-card h3{margin:0 0 8px;font-size:18px}.role-card p{margin:0;color:var(--muted);font-size:13px}.faq-layout{display:grid;grid-template-columns:.72fr 1.28fr;gap:70px}.faq-intro h2{margin:0 0 13px;font-size:40px}.faq-intro p{color:var(--muted)}.faq-list{border-top:1px solid var(--border)}details{border-bottom:1px solid var(--border)}summary{position:relative;padding:20px 44px 20px 0;cursor:pointer;list-style:none;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif;font-size:18px;font-weight:700}summary::-webkit-details-marker{display:none}summary .faq-chevron{position:absolute;right:4px;top:18px;width:28px;height:28px;padding:5px;border-radius:50%;background:var(--pale);color:var(--green);stroke-width:2;transition:transform .18s ease}details[open] summary .faq-chevron{transform:rotate(180deg)}details p{margin:-4px 42px 22px 0;color:var(--muted);font-size:14px}.final-cta{padding:0 0 96px}.cta-panel{position:relative;overflow:hidden;display:grid;grid-template-columns:1fr auto;align-items:center;gap:40px;padding:50px;border-radius:18px;background:var(--green);color:#fff}.cta-panel:after{content:"";position:absolute;right:-50px;bottom:-130px;width:260px;height:260px;border:44px solid #ffffff0e;border-radius:50%}.cta-panel h2{position:relative;margin:0;font-size:36px;line-height:1.1}.cta-panel p{position:relative;margin:11px 0 0;color:#d9f0e8}.cta-panel .button{position:relative;z-index:1;background:#fff;color:var(--green)}.site-footer{padding:58px 0 28px;background:#1e3b46;color:#dce7e9}.footer-top{display:grid;grid-template-columns:1.3fr .7fr .7fr;gap:70px;padding-bottom:40px}.footer-brand p{max-width:460px;margin:16px 0 0;color:#b9cccf;font-size:13px}.footer-column strong{display:block;margin-bottom:15px;color:#fff;font-family:Corbel, Calibri, "Segoe UI", Arial, sans-serif}.footer-column a{display:block;width:max-content;margin:9px 0;color:#b9cccf;font-size:13px;text-decoration:none}.footer-column a:hover{color:#fff}.footer-bottom{display:flex;justify-content:space-between;gap:20px;padding-top:24px;border-top:1px solid #ffffff18;color:#91adb1;font-size:11px}.footer-bottom p{margin:0}
        @media(max-width:1023.98px){.site-nav{display:none;position:fixed;inset:76px 0 auto 0;padding:22px 20px 28px;border-top:1px solid #ffffff20;background:var(--dark);box-shadow:0 22px 35px #071e2488;flex-direction:column;align-items:stretch}.nav-open .site-nav{display:flex}.site-nav>a:not(.nav-cta){padding:12px 4px}.nav-login{margin:0;padding-left:4px!important;border:0}.nav-cta{margin:5px 0 0}.menu-button{display:block}.hero-inner{grid-template-columns:1fr;gap:58px;padding-top:68px;padding-bottom:90px}.hero-copy{max-width:760px}.platform-preview{max-width:620px}.modes-panel{grid-template-columns:1fr 1fr 1fr}.modes-title{grid-column:1/-1;border-right:0;border-bottom:1px solid var(--border)}.feature-grid{grid-template-columns:repeat(2,1fr)}.journey-layout,.assurance-layout,.faq-layout{grid-template-columns:1fr;gap:42px}.journey-intro{position:static}.role-grid{grid-template-columns:repeat(2,1fr)}}
        @media(max-width:640px){.header-inner,.section-inner,.footer-inner{width:min(100% - 28px,1180px)}.header-inner{min-height:68px}.brand-mark{width:37px;height:37px}.site-nav{top:68px}.hero-inner{min-height:auto;padding-top:55px;padding-bottom:72px}.hero h1{font-size:39px}.hero-copy>p{font-size:16px}.hero-actions{align-items:stretch;flex-direction:column}.hero-actions .button{width:100%}.hero-note{align-items:flex-start;flex-direction:column;gap:9px}.preview-window{padding:12px;transform:none}.preview-steps{grid-template-columns:1fr}.preview-metrics{grid-template-columns:1fr 1fr}.preview-metric:last-child{grid-column:1/-1}.preview-accent{right:-6px;bottom:-24px}.modes-strip{margin-top:-22px}.modes-panel{grid-template-columns:1fr 1fr}.modes-title{padding:18px}.mode-item{justify-content:flex-start;padding:14px 16px}.content-section,.assurance{padding:72px 0}.section-heading{margin-bottom:30px}.feature-grid,.assurance-list,.role-grid{grid-template-columns:1fr}.feature-card{min-height:0}.journey-intro h2,.faq-intro h2{font-size:34px}.journey-step{grid-template-columns:44px 1fr;padding:17px}.journey-step:before{width:42px;height:42px}.cta-panel{grid-template-columns:1fr;padding:32px 24px}.cta-panel h2{font-size:30px}.cta-panel .button{width:100%}.footer-top{grid-template-columns:1fr;gap:30px}.footer-bottom{flex-direction:column}.final-cta{padding-bottom:72px}}
    </style>
    <style>.site-footer{background:var(--dark)}</style>
    <style>
        .site-header{background:var(--dark);backdrop-filter:none}.brand-mark{box-shadow:none}.nav-cta,.button{transition:background-color .18s}.nav-cta{min-height:44px}.menu-button{min-height:44px;min-width:44px;display:none;align-items:center;justify-content:center}.menu-button svg{width:21px;height:21px;stroke-width:2}.nav-backdrop{display:none}.button:hover,.nav-cta:hover{transform:none;box-shadow:none;background:var(--green-hover)}.hero{border-left:8px solid var(--gold);background-image:url('{{ asset('images/pnmg/trame-itineraires.svg') }}');background-repeat:no-repeat;background-position:left center;background-size:auto 100%}.hero:before,.cta-panel:after{display:none}.hero-note span:before{content:"";width:15px;height:2px;border-radius:0;background:var(--gold)}.preview-window{box-shadow:none;transform:none}.preview-accent,.modes-panel{box-shadow:none}.feature-card{transition:border-color .18s}.feature-card:hover{transform:none;box-shadow:none}.button svg{width:18px;height:18px;stroke-width:2}.scope-note svg{width:19px;height:19px;stroke-width:2}.lucide{display:inline-block;vertical-align:middle}
    </style>
    <style>
        .cta-panel .button:hover{background:var(--bg);color:var(--green)}
        .button:not(:disabled):active,.nav-cta:active,.menu-button:active{filter:brightness(.9)}
        .site-nav .nav-login{min-height:44px;margin-left:6px;padding:10px 16px!important;border:1px solid #ffffff80;border-radius:8px;color:#fff!important;font-weight:700}
        .site-nav .nav-login:hover{background:#ffffff12}
        .site-footer{border-top:5px solid transparent;border-image:linear-gradient(90deg,var(--green) 0 33.333%,var(--gold) 33.333% 66.666%,#3a75c4 66.666%) 1}
        .cta-panel p{color:var(--surface)}
        .footer-bottom{color:#c8d8da}
        .preview-disclaimer{display:block;margin-top:10px;color:var(--muted);font-size:11px;text-align:center}
        @media(max-width:1023.98px){
            .menu-button{display:inline-flex}
            .site-nav{display:flex;inset:76px auto 0 0;width:min(320px,85vw);padding:22px 20px 28px;border-top:1px solid #ffffff20;border-right:1px solid #ffffff20;background:var(--dark);box-shadow:12px 0 30px #071e2488;flex-direction:column;align-items:stretch;overflow-y:auto;transform:translateX(-100%);visibility:hidden;transition:transform .22s ease,visibility .22s ease;z-index:41}
            .nav-open .site-nav{display:flex;transform:translateX(0);visibility:visible}
            .site-nav .nav-login{margin-left:0}
            .nav-backdrop{display:block;position:fixed;inset:76px 0 0;z-index:39;border:0;background:#071e2488;opacity:0;visibility:hidden;transition:opacity .22s ease,visibility .22s ease}
            .nav-open .nav-backdrop{opacity:1;visibility:visible}
        }
        @media(max-width:640px){.site-nav{top:68px}.nav-backdrop{top:68px}}
        @media(prefers-reduced-motion:reduce){.site-nav,.nav-backdrop{transition:none}}
    </style>
<style id="pnmg-accueil-v3">
/* Héros : fond lumineux et bouton en pilule dégradé */
.hero{position:relative;overflow:hidden;isolation:isolate}
.hero::before{content:"";position:absolute;right:-6%;top:-12%;width:62%;height:124%;z-index:-1;pointer-events:none;background:radial-gradient(closest-side at 62% 40%,rgba(233,196,106,.42),transparent 72%),radial-gradient(closest-side at 35% 62%,rgba(8,122,90,.55),transparent 70%);filter:blur(14px);animation:derive 14s ease-in-out infinite alternate}
@keyframes derive{0%{transform:translate3d(0,0,0)}100%{transform:translate3d(-3%,4%,0)}}
.hero .button:not(.secondary){background:linear-gradient(90deg,#087A5A,#E9C46A)!important;color:#102E29!important;border:0;border-radius:999px;text-transform:uppercase;letter-spacing:.12em;font-size:13px;padding:0 28px;box-shadow:0 14px 30px rgba(8,122,90,.35);transition:transform .2s ease,box-shadow .25s ease}
.hero .button:not(.secondary):hover{transform:translateY(-2px);box-shadow:0 18px 36px rgba(8,122,90,.45)}

/* Section Périmètre : titre centré très large, comme « Our feature blogs » */
#plateforme .section-heading{text-align:center}
#plateforme .section-kicker{font-size:12px;letter-spacing:.14em;text-transform:uppercase;color:#244653;font-weight:700}
#plateforme .section-kicker::before{content:"■";margin-right:9px;font-size:9px;color:#E9C46A;vertical-align:middle}
#plateforme .section-heading h2{font-size:clamp(36px,4.6vw,60px);font-weight:500;letter-spacing:-.02em;line-height:1.1;margin:12px 0 0}

/* Carrousel de cartes : une carte par visuel, comme les blogs de référence */
#plateforme .feature-grid{display:flex!important;flex-wrap:nowrap!important;overflow-x:auto;scroll-snap-type:x mandatory;gap:26px;padding:34px 6px 14px;margin-top:10px;scrollbar-width:none;-webkit-overflow-scrolling:touch;grid-template-columns:none!important}
#plateforme .feature-grid::-webkit-scrollbar{display:none}
#plateforme .feature-card{position:relative;flex:0 0 min(340px,82vw);scroll-snap-align:start;background:#fff;border:0;border-radius:24px;padding:0 0 28px;overflow:hidden;box-shadow:0 18px 40px rgba(16,46,41,.10);transition:transform .25s ease,box-shadow .25s ease}
#plateforme .feature-card:hover{transform:translateY(-6px);box-shadow:0 26px 56px rgba(16,46,41,.18)}
#plateforme .feature-card::before{content:"";display:block;height:200px;background:radial-gradient(closest-side at 70% 30%,rgba(233,196,106,.55),transparent 70%),linear-gradient(135deg,#0b3b2f 0%,#087A5A 60%,#244653 100%)}
#plateforme .feature-icon{position:absolute;top:70px;left:50%;transform:translateX(-50%);width:68px;height:68px;border-radius:22px;display:grid;place-items:center;background:rgba(255,255,255,.16);color:#fff;border:1px solid rgba(255,255,255,.3)}
#plateforme .feature-icon svg{width:32px;height:32px}
#plateforme .feature-card h3{margin:24px 26px 10px;font-size:22px;font-weight:700;color:#244653;line-height:1.3}
#plateforme .feature-card p{margin:0 26px;color:#4B5D66;font-size:15px;line-height:1.65}
#plateforme .feature-card:not(:has(.feature-icon)){padding-top:0}
.pnmg-fleches{display:flex;gap:14px;justify-content:center;margin-top:22px}
.pnmg-fleches button{width:56px;height:56px;border-radius:50%;border:0;background:#0b0b0b;color:#E9C46A;display:grid;place-items:center;cursor:pointer;box-shadow:0 12px 26px rgba(0,0,0,.28);transition:transform .2s ease,background .2s ease,color .2s ease}
.pnmg-fleches button:hover{background:#087A5A;color:#fff;transform:translateY(-2px)}
.pnmg-fleches button:focus-visible{outline:3px solid #087A5A;outline-offset:3px}
.pnmg-fleches svg{width:22px;height:22px}

/* Pied de page : quatre colonnes, bloc de contact encadré, pastille de statut */
.footer-top{display:grid!important;grid-template-columns:1.25fr 1fr 1fr 1fr;gap:34px;align-items:start}
.footer-column strong{display:block;color:#E9C46A;font-size:13px;letter-spacing:.08em;text-transform:uppercase;margin-bottom:14px}
.footer-column a{display:block;padding:5px 0;color:rgba(255,255,255,.84);text-decoration:none;font-size:15px}
.footer-column a:hover{color:#fff;text-decoration:underline}
.footer-contact{border:1px solid rgba(233,196,106,.5);border-radius:14px;padding:20px 22px;margin-top:18px;color:rgba(255,255,255,.88);font-size:14px;line-height:1.8}
.footer-contact strong{display:block;color:#fff;font-size:15px;margin-bottom:4px}
.footer-bottom{display:flex!important;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px}
.status-pill{display:inline-flex;align-items:center;gap:8px;padding:6px 14px;border-radius:999px;background:rgba(134,239,172,.18);border:1px solid rgba(134,239,172,.4);color:#dcfce7;font-size:13px;font-weight:700}
.status-pill::before{content:"";width:9px;height:9px;border-radius:50%;background:#4ade80;animation:pouls 2s infinite}
@keyframes pouls{0%{box-shadow:0 0 0 0 rgba(74,222,128,.6)}70%{box-shadow:0 0 0 8px rgba(74,222,128,0)}100%{box-shadow:0 0 0 0 rgba(74,222,128,0)}}
@media (max-width:900px){.footer-top{grid-template-columns:1fr 1fr!important}}
@media (max-width:560px){.footer-top{grid-template-columns:1fr!important}}
@media (prefers-reduced-motion:reduce){*,*::before,*::after{animation:none!important;transition:none!important}}
</style>
<style id="pnmg-hero-v4">
.site-header{background:transparent!important;border:0!important;box-shadow:none!important;backdrop-filter:none!important;-webkit-backdrop-filter:none!important;top:0!important;left:0!important;right:0!important;width:100%!important;border-radius:0!important;padding:22px 40px!important;position:absolute!important}
.site-header .brand strong,.site-header .brand span{letter-spacing:.32em;text-transform:uppercase;font-size:13px;color:#fff}
.site-nav a{color:rgba(255,255,255,.82)!important;text-transform:uppercase;letter-spacing:.16em;font-size:12px;font-weight:600;padding:8px 6px!important;background:none!important;box-shadow:none!important}
.site-nav a:hover{color:#E9C46A!important}
.site-nav .nav-cta{background:linear-gradient(90deg,#087A5A,#E9C46A)!important;color:#102E29!important;border-radius:999px!important;padding:10px 20px!important}
body:has(.site-header){padding-top:0!important}

.hero{position:relative;min-height:100vh;display:flex;align-items:center;background:radial-gradient(circle at 80% 40%,#0c4a3b 0%,#06201b 45%,#020b09 100%)!important;overflow:hidden;border-radius:0!important;margin:0!important;padding:140px 0 110px!important;isolation:isolate}
.hero::after{content:"";position:absolute;inset:0;background:linear-gradient(90deg,rgba(2,11,9,.92) 0%,rgba(2,11,9,.55) 45%,transparent 75%);z-index:-1;pointer-events:none}
.hero-trajets{position:absolute;right:-8%;top:0;width:70%;height:100%;z-index:-1;pointer-events:none;filter:drop-shadow(0 0 18px rgba(233,196,106,.55))}
.hero-trajets path{fill:none;stroke-linecap:round;animation:trace 9s ease-in-out infinite alternate}
.hero-trajets .t2{animation-duration:12s}
.hero-trajets .t3{animation-duration:15s}
@keyframes trace{0%{stroke-dashoffset:0}100%{stroke-dashoffset:-120}}
.hero .eyebrow,.hero .kicker,.hero [class*="eyebrow"]{display:inline-flex;align-items:center;gap:8px;padding:7px 14px;border:1px solid rgba(255,255,255,.22);border-radius:999px;font-size:11px;letter-spacing:.28em;text-transform:uppercase;color:#fff;background:rgba(255,255,255,.04)}
.hero h1{font-size:clamp(46px,6.4vw,92px);font-weight:800;line-height:1.02;letter-spacing:-.03em;color:#fff;margin:26px 0 22px;max-width:12ch}
.hero h1 span{background:linear-gradient(90deg,#E9C46A,#7FD8B2);-webkit-background-clip:text;background-clip:text;color:transparent}
.hero p,.hero .lead{font-size:18px;line-height:1.7;color:rgba(255,255,255,.78);max-width:52ch}
.hero .button:not(.secondary){background:linear-gradient(90deg,#8B5CF6 0%,#087A5A 45%,#E9C46A 100%)!important;color:#fff!important;border:0;border-radius:999px;text-transform:uppercase;letter-spacing:.2em;font-size:13px;font-weight:800;padding:0 30px;min-height:56px;box-shadow:0 18px 40px rgba(139,92,246,.35);transition:transform .2s ease,box-shadow .25s ease}
.hero .button:not(.secondary):hover{transform:translateY(-3px);box-shadow:0 24px 50px rgba(139,92,246,.5)}
.hero .button.secondary{background:transparent!important;color:#fff!important;border:1px solid rgba(255,255,255,.35)!important;border-radius:999px;text-transform:uppercase;letter-spacing:.16em;font-size:12px;min-height:56px}
.hero [class*="card"],.hero [class*="panel"],.hero aside{background:rgba(255,255,255,.06)!important;border:1px solid rgba(255,255,255,.14)!important;backdrop-filter:blur(14px);-webkit-backdrop-filter:blur(14px);box-shadow:0 30px 70px rgba(0,0,0,.45)!important;color:#fff}

.hero-bande{position:absolute;left:0;right:0;bottom:28px;display:flex;justify-content:center;gap:54px;flex-wrap:wrap;padding:0 24px;z-index:1}
.hero-bande span{font-size:12px;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.72);font-weight:700}
.hero-bande span::before{content:"◆";color:#E9C46A;margin-right:10px;font-size:9px}
.hero-legende{font-size:11px;letter-spacing:.3em;text-transform:uppercase;color:rgba(255,255,255,.45);text-align:center;margin-bottom:14px}

@media (max-width:900px){.site-header{padding:18px 20px!important}.site-nav a{font-size:11px}.hero{padding:120px 0 120px!important}.hero-trajets{width:100%;right:-30%;opacity:.7}}
@media (prefers-reduced-motion:reduce){.hero-trajets path{animation:none!important}}
</style>
<style id="pnmg-hero-sans-pastille">.hero .eyebrow,.hero .kicker,.hero [class*="eyebrow"]{display:none!important}</style>
<style id="pnmg-accueil-v5">
/* Bande des modes : menu circulaire, modes autour du titre central */
.modes-panel{position:relative!important;display:block!important;width:min(600px,94vw);height:600px;margin:48px auto;border-radius:50%!important;background:radial-gradient(closest-side,#ffffff 62%,#eef6f2 100%)!important;border:1px solid #DCE3E0!important;box-shadow:0 24px 70px rgba(16,46,41,.12)!important;padding:0!important}
.modes-title{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:220px;text-align:center}
.modes-title strong,.modes-title h3{display:block;font-size:20px;color:#244653}
.modes-title small{display:block;margin-top:6px;color:#4B5D66;font-size:13px}
.mode-item{position:absolute!important;left:50%;top:50%;width:170px;margin:-52px 0 0 -85px;display:flex!important;flex-direction:column;align-items:center;gap:8px;transform:rotate(var(--a,0deg)) translate(220px) rotate(calc(var(--a,0deg) * -1));text-align:center}
.mode-item .mode-icon{width:76px;height:76px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#E6F4EE,#fff);border:2px solid #fff;box-shadow:0 12px 26px rgba(8,122,90,.18);transition:transform .25s ease,box-shadow .25s ease}
.mode-item:hover .mode-icon{transform:scale(1.1);box-shadow:0 18px 36px rgba(8,122,90,.28)}
.mode-item .mode-icon svg{width:30px;height:30px;color:#087A5A}
.mode-item span,.mode-item strong{font-size:15px;font-weight:700;color:#244653}
@media (max-width:700px){.modes-panel{height:440px}.mode-item{width:120px;margin:-40px 0 0 -60px;transform:rotate(var(--a,0deg)) translate(150px) rotate(calc(var(--a,0deg) * -1))}.mode-item .mode-icon{width:60px;height:60px}.modes-title{width:160px}}

/* Carrousel : plus large, touches directionnelles de chaque côté */
#plateforme .section-inner{position:relative;max-width:1320px!important}
#plateforme .feature-grid{padding:34px 70px 14px!important}
#plateforme .feature-card{flex:0 0 min(440px,84vw)!important}
#plateforme .feature-card::before{height:250px!important}
#plateforme .feature-icon{top:95px!important}
.pnmg-fleches{position:absolute;left:0;right:0;top:50%;transform:translateY(-50%);display:flex!important;justify-content:space-between!important;margin:0!important;pointer-events:none;z-index:2}
.pnmg-fleches button{pointer-events:auto;position:relative;width:60px;height:60px;border-radius:50%;border:0;background:#0b0b0b;color:#E9C46A;display:grid;place-items:center;cursor:pointer;box-shadow:0 14px 30px rgba(0,0,0,.3);transition:transform .2s ease,background .2s ease,color .2s ease}
.pnmg-fleches button:first-child{left:6px}
.pnmg-fleches button:last-child{right:6px}
.pnmg-fleches button:hover{background:#087A5A;color:#fff;transform:scale(1.06)}
.pnmg-fleches button:focus-visible{outline:3px solid #087A5A;outline-offset:3px}
.pnmg-fleches svg{width:24px;height:24px}
@media (max-width:700px){#plateforme .feature-grid{padding:30px 46px 14px!important}.pnmg-fleches button{width:46px;height:46px}}
@media (prefers-reduced-motion:reduce){.mode-item .mode-icon{transition:none!important}}
</style>
<style id="pnmg-bande-defilante">
.modes-panel{display:block!important;position:relative!important;width:100%!important;height:auto!important;max-width:none!important;margin:44px 0!important;padding:0!important;border-radius:0!important;border:0!important;background:linear-gradient(90deg,#102E29,#0b3b2f 50%,#102E29)!important;box-shadow:none!important;overflow:hidden!important}
.modes-panel .mode-item,.modes-panel .modes-title{display:none!important}
.pnmg-bande{display:flex;width:max-content;animation:defile 26s linear infinite}
.pnmg-bande:hover{animation-play-state:paused}
.pnmg-bande-piste{display:flex;align-items:center;gap:54px;padding:26px 27px;white-space:nowrap}
.pnmg-bande-piste span{font-size:clamp(22px,3.2vw,40px);font-weight:800;letter-spacing:.04em;text-transform:uppercase;color:#fff}
.pnmg-bande-piste span::after{content:"◆";margin-left:54px;color:#E9C46A;font-size:.5em;vertical-align:middle}
@keyframes defile{from{transform:translateX(0)}to{transform:translateX(-50%)}}
@media (prefers-reduced-motion:reduce){.pnmg-bande{animation:none}}
</style>
<style id="pnmg-accueil-v6">
#plateforme .section-inner{position:relative!important;width:min(1440px,94vw)!important;max-width:none!important;margin:0 auto!important}
#plateforme .feature-grid{padding:40px 0 18px!important;gap:30px!important;margin-top:14px!important}
#plateforme .feature-card{flex:0 0 min(500px,86vw)!important;border-radius:28px!important}
#plateforme .feature-card::before{height:300px!important}
#plateforme .feature-icon{top:118px!important;width:84px!important;height:84px!important;border-radius:26px!important}
#plateforme .feature-icon svg{width:40px!important;height:40px!important}
#plateforme .feature-card h3{font-size:26px!important;margin:30px 34px 12px!important}
#plateforme .feature-card p{font-size:17px!important;margin:0 34px!important;line-height:1.7!important}
.pnmg-fleches{position:absolute!important;left:-30px;right:-30px;top:50%;transform:translateY(-50%);display:flex!important;justify-content:space-between!important;margin:0!important;pointer-events:none;z-index:3}
.pnmg-fleches button{pointer-events:auto;width:68px!important;height:68px!important;border-radius:50%!important;border:0;background:#0b0b0b;color:#E9C46A;display:grid;place-items:center;cursor:pointer;box-shadow:0 16px 34px rgba(0,0,0,.32);transition:transform .2s ease,background .2s ease,color .2s ease}
.pnmg-fleches button:hover{background:#087A5A;color:#fff;transform:scale(1.07)}
.pnmg-fleches button:focus-visible{outline:3px solid #087A5A;outline-offset:4px}
.pnmg-fleches svg{width:28px!important;height:28px!important}
@media (max-width:1100px){.pnmg-fleches{left:-6px;right:-6px}.pnmg-fleches button{width:54px!important;height:54px!important}}
@media (max-width:700px){#plateforme .feature-card{flex-basis:84vw!important}.pnmg-fleches{left:0;right:0}.pnmg-fleches button{width:46px!important;height:46px!important}}
</style>
<style id="pnmg-roles-v1">
#acces .role-grid{display:grid!important;grid-template-columns:repeat(4,minmax(0,1fr));gap:24px;margin-top:44px}
#acces .role-card{position:relative;display:flex;flex-direction:column;min-height:100%;background:#fff;border:1px solid #e6e2d6;border-radius:22px;padding:34px 28px 30px;overflow:hidden;box-shadow:0 14px 34px rgba(16,46,41,.08);transition:transform .25s ease,box-shadow .25s ease,border-color .25s ease}
#acces .role-card::before{content:"";position:absolute;left:0;right:0;top:0;height:5px;background:linear-gradient(90deg,#087A5A,#E9C46A)}
#acces .role-card::after{content:"";position:absolute;right:-40px;top:-40px;width:150px;height:150px;border-radius:50%;background:radial-gradient(closest-side,rgba(8,122,90,.12),transparent 70%);pointer-events:none}
#acces .role-card:nth-child(2)::before{background:linear-gradient(90deg,#E9C46A,#087A5A)}
#acces .role-card:nth-child(3)::before{background:linear-gradient(90deg,#244653,#087A5A)}
#acces .role-card:nth-child(4)::before{background:linear-gradient(90deg,#E9C46A,#244653)}
#acces .role-card:hover{transform:translateY(-6px);box-shadow:0 26px 54px rgba(16,46,41,.16);border-color:rgba(8,122,90,.35)}
#acces .role-card>span{display:block;font-size:56px;font-weight:300;line-height:1;letter-spacing:-.03em;color:transparent;-webkit-text-stroke:1.5px #087A5A;margin-bottom:26px}
#acces .role-card h3{font-size:22px;font-weight:800;color:#244653;margin:0 0 12px;line-height:1.25}
#acces .role-card p{font-size:15px;line-height:1.7;color:#4B5D66;margin:0}
@media (max-width:1100px){#acces .role-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:620px){#acces .role-grid{grid-template-columns:1fr}}
@media (prefers-reduced-motion:reduce){#acces .role-card{transition:none!important}}
</style>
<style id="pnmg-faq-v1">
#questions .faq-list{counter-reset:question;display:flex;flex-direction:column;gap:14px}
#questions .faq-list details{counter-increment:question;position:relative;background:#fff;border:1px solid #e6e2d6;border-radius:18px;box-shadow:0 10px 26px rgba(16,46,41,.07);overflow:hidden;transition:box-shadow .25s ease,border-color .25s ease}
#questions .faq-list details::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:linear-gradient(180deg,#087A5A,#E9C46A);opacity:0;transition:opacity .25s ease}
#questions .faq-list details[open]{box-shadow:0 18px 40px rgba(16,46,41,.12);border-color:rgba(8,122,90,.3)}
#questions .faq-list details[open]::before{opacity:1}
#questions .faq-list details:hover{box-shadow:0 16px 34px rgba(16,46,41,.12)}
#questions .faq-list summary{list-style:none;cursor:pointer;display:flex;align-items:center;gap:18px;padding:22px 24px;font-size:17px;font-weight:700;color:#244653;line-height:1.4}
#questions .faq-list summary::-webkit-details-marker{display:none}
#questions .faq-list summary::before{content:counter(question,decimal-leading-zero);flex:0 0 auto;display:grid;place-items:center;width:44px;height:44px;border-radius:14px;background:#EAF5F0;color:#087A5A;font-size:14px;font-weight:800;letter-spacing:.02em;transition:background .25s ease,color .25s ease}
#questions .faq-list details[open] summary::before{background:linear-gradient(135deg,#087A5A,#0b3b2f);color:#E9C46A}
#questions .faq-list summary .faq-chevron{margin-left:auto;flex:0 0 auto;width:40px;height:40px;padding:10px;border-radius:50%;background:#F1F4F2;color:#087A5A;transition:transform .3s ease,background .25s ease}
#questions .faq-list details[open] summary .faq-chevron{transform:rotate(180deg);background:#E9C46A;color:#102E29}
#questions .faq-list details p{margin:0;padding:0 24px 24px 86px;font-size:15.5px;line-height:1.75;color:#4B5D66;animation:faq-ouvre .35s ease}
@keyframes faq-ouvre{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:none}}
#questions .faq-list summary:focus-visible{outline:3px solid #087A5A;outline-offset:-3px;border-radius:18px}
@media (max-width:620px){#questions .faq-list summary{padding:18px;font-size:16px;gap:12px}#questions .faq-list summary::before{width:36px;height:36px;font-size:12px}#questions .faq-list details p{padding:0 18px 20px 18px}}
@media (prefers-reduced-motion:reduce){#questions .faq-list details p,#questions .faq-list details{animation:none!important;transition:none!important}}
</style>
<style id="pnmg-cta-footer-v1">
#questions + .final-cta,.final-cta{padding:70px 0 80px!important}
.final-cta .cta-panel{position:relative;overflow:hidden;isolation:isolate;display:flex!important;align-items:center;justify-content:space-between;gap:32px;flex-wrap:wrap;padding:58px 64px!important;border-radius:30px!important;background:radial-gradient(circle at 92% 20%,rgba(233,196,106,.32),transparent 40%),radial-gradient(circle at 8% 100%,rgba(127,216,178,.22),transparent 45%),linear-gradient(135deg,#0b3b2f 0%,#087A5A 55%,#0b4d3c 100%)!important;box-shadow:0 30px 70px rgba(8,122,90,.28)!important;color:#fff}
.final-cta .cta-panel::before{content:"";position:absolute;right:-80px;top:-80px;width:280px;height:280px;border-radius:50%;border:1px solid rgba(233,196,106,.35);z-index:-1;pointer-events:none}
.final-cta .cta-panel::after{content:"";position:absolute;right:40px;bottom:-120px;width:200px;height:200px;border-radius:50%;border:1px solid rgba(255,255,255,.18);z-index:-1;pointer-events:none}
.final-cta .cta-panel h2{color:#fff;font-size:clamp(30px,3.6vw,44px);font-weight:800;letter-spacing:-.02em;line-height:1.15;margin:0 0 12px}
.final-cta .cta-panel p{color:rgba(255,255,255,.85);font-size:17px;line-height:1.6;margin:0;max-width:56ch}
.final-cta .cta-panel>a,.final-cta .cta-panel .button,.final-cta .cta-panel a[class]{display:inline-flex!important;align-items:center;gap:12px;background:#fff!important;color:#0b3b2f!important;border-radius:999px!important;padding:0 30px!important;min-height:58px;font-size:16px;font-weight:800;text-decoration:none;box-shadow:0 16px 34px rgba(0,0,0,.22);transition:transform .25s ease,box-shadow .25s ease,background .25s ease}
.final-cta .cta-panel>a:hover,.final-cta .cta-panel .button:hover{transform:translateY(-3px);box-shadow:0 22px 44px rgba(0,0,0,.3);background:#E9C46A!important}
.final-cta .cta-panel>a svg,.final-cta .cta-panel .button svg{transition:transform .25s ease}
.final-cta .cta-panel>a:hover svg,.final-cta .cta-panel .button:hover svg{transform:translateX(5px)}

.site-footer{position:relative;background:radial-gradient(circle at 90% 0%,rgba(8,122,90,.25),transparent 40%),linear-gradient(180deg,#244653,#1b3a44)!important;color:rgba(255,255,255,.86)!important;padding-top:70px!important}
.site-footer::before,.site-footer::after{display:none!important}
.site-footer::after{content:"";}
.site-footer{border-top:0!important}
.site-footer .footer-inner{position:relative}
.site-footer .footer-inner::before{content:"";position:absolute;left:0;right:0;top:-70px;height:6px;background:linear-gradient(90deg,#087A5A 0%,#087A5A 33%,#E9C46A 33%,#E9C46A 66%,#7FD8B2 66%,#7FD8B2 100%);border-radius:0 0 6px 6px}
.site-footer .footer-top{display:grid!important;grid-template-columns:1.4fr 1fr 1.2fr!important;gap:48px!important;align-items:start}
.site-footer .footer-brand p{font-size:15px;line-height:1.75;color:rgba(255,255,255,.75);max-width:34ch;margin-top:18px}
.site-footer .footer-column strong{display:block;color:#E9C46A!important;font-size:13px!important;letter-spacing:.14em!important;text-transform:uppercase;margin-bottom:18px!important;padding-bottom:12px;border-bottom:1px solid rgba(255,255,255,.12)}
.site-footer .footer-column a{display:block;color:rgba(255,255,255,.86)!important;text-decoration:none;font-size:16px!important;padding:8px 0!important;transition:color .2s ease,transform .2s ease;width:max-content}
.site-footer .footer-column a:hover{color:#E9C46A!important;transform:translateX(4px);text-decoration:underline;text-underline-offset:5px}
.site-footer .footer-bottom{margin-top:52px;padding-top:26px;border-top:1px solid rgba(255,255,255,.12);display:flex!important;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:16px}
.site-footer .footer-bottom p{margin:0;font-size:14px;color:rgba(255,255,255,.7)}
.site-footer .status-pill{background:rgba(134,239,172,.16)!important;border:1px solid rgba(134,239,172,.42)!important;color:#dcfce7!important;padding:9px 18px!important;font-size:13px!important}
@media (max-width:900px){.site-footer .footer-top{grid-template-columns:1fr 1fr!important}}
@media (max-width:620px){.site-footer .footer-top{grid-template-columns:1fr!important}.final-cta .cta-panel{padding:40px 26px!important}}
@media (prefers-reduced-motion:reduce){.final-cta .cta-panel>a,.site-footer .footer-column a{transition:none!important}}
</style>
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="Accueil PNMG">
            <img src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40">
        </a>
        <button class="menu-button" type="button" aria-expanded="false" aria-label="Ouvrir le menu" aria-controls="navigation-publique"><x-lucide-menu aria-hidden="true"/></button>
        <nav class="site-nav" id="navigation-publique" aria-label="Navigation publique">
            <a href="{{ route('home') }}">Accueil</a>
            <a href="#plateforme">Compagnies</a>
            <a href="{{ route('public.application-status') }}">Suivre une demande</a>
            <a href="#parcours">Inscription</a>
            @auth
                <a class="nav-cta" href="{{ auth()->user()->role === 'company_manager' && auth()->user()->company_id !== null && auth()->user()->company?->status !== \App\Enums\CompanyStatus::Active ? route('application.status') : route('dashboard') }}">Mon espace</a>
            @else
                <a class="nav-login" href="{{ route('login') }}">Se connecter</a>
            @endauth
        </nav>
        <button class="nav-backdrop" type="button" aria-label="Fermer le menu" tabindex="-1"></button>
    </div>
</header>

<main id="contenu">
    <section class="hero">
        <div class="section-inner hero-inner">
            <div class="hero-copy">
                <h1>Les compagnies de transport du Gabon, <span>agréées et suivies</span> sur une seule plateforme.</h1>
                <p>Bus, voitures, navires et trains : inscription des compagnies, agréments, documents et suivi des demandes.</p>
                <div class="hero-actions">
                    <a class="button" href="{{ route('registration.identification') }}">Inscrire ma compagnie <x-lucide-arrow-right aria-hidden="true"/></a>
                    <a class="button secondary" href="{{ route('public.application-status') }}">Suivre une demande</a>
                </div>

            </div>
            <div class="platform-preview" aria-label="Aperçu illustratif du suivi administratif">
                <div class="preview-window">
                    <div class="preview-bar"><span class="preview-logo"><i></i> Espace compagnie</span><span class="preview-dots"><i></i><i></i><i></i></span></div>
                    <div class="preview-card">
                        <div class="preview-head"><div><strong>Dossier d’enregistrement</strong><br><small>Suivi administratif PNMG</small></div><span class="preview-status">En examen</span></div>
                        <div class="preview-progress" aria-hidden="true"></div>
                        <div class="preview-steps"><div class="preview-step done"><b>Identification</b><small>Informations vérifiées</small></div><div class="preview-step done"><b>Documents</b><small>Pièces transmises</small></div><div class="preview-step"><b>Décision</b><small>Contrôle en cours</small></div></div>
                    </div>
                    <div class="preview-metrics"><div class="preview-metric"><strong>01</strong><span>Mode déclaré</span></div><div class="preview-metric"><strong>01</strong><span>Agence siège</span></div><div class="preview-metric"><strong>100 %</strong><span>Dossier traçable</span></div></div>
                    <small class="preview-disclaimer">Aperçu illustratif — données d’exemple</small>
                </div>
                <div class="preview-accent"><strong>4</strong><span>modes pris en charge</span></div>
            </div>
        </div>
    </section>

    <div class="modes-strip">
        <div class="section-inner modes-panel">
            <div class="modes-title"><strong>Mobilité couverte</strong><small>Un mode principal par compagnie</small></div>
            <div class="mode-item"><span class="mode-icon" aria-hidden="true"><x-lucide-car-front/></span>Routier</div>
            <div class="mode-item"><span class="mode-icon" aria-hidden="true"><x-lucide-waves/></span>Fluvial</div>
            <div class="mode-item"><span class="mode-icon" aria-hidden="true"><x-lucide-ship/></span>Maritime</div>
            <div class="mode-item"><span class="mode-icon" aria-hidden="true"><x-lucide-train-front/></span>Ferroviaire</div>
        </div>
    </div>

    <section class="content-section" id="plateforme">
        <div class="section-inner">
            <div class="section-heading"><span class="section-kicker">Périmètre fonctionnel</span><h2>Ce que cette première version prend en charge</h2><p>Cette première version centralise les informations indispensables à la supervision des compagnies de transport, depuis leur demande d’inscription jusqu’à leur activité quotidienne.</p></div>
            <div class="feature-grid">
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-building-2/></span><h3>Dossier compagnie</h3><p>Identité juridique, RCCM, coordonnées, localisation du siège et mode de transport réunis dans une fiche unique.</p></article>
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-files/></span><h3>Conformité documentaire</h3><p>Dépôt, consultation, contrôle et suivi des dates de validité des pièces administratives obligatoires.</p></article>
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-map-pinned/></span><h3>Agences et siège</h3><p>Une agence siège créée par défaut et des agences secondaires rattachées à leur province et leur localité.</p></article>
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-users-round/></span><h3>Utilisateurs habilités</h3><p>Création des comptes utiles à la plateforme avec des rôles et permissions adaptés au périmètre de chacun.</p></article>
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-bus-front/></span><h3>Inventaire de flotte</h3><p>Enregistrement des véhicules ou embarcations, avec leur type, leur capacité, leur état et leurs documents.</p></article>
                <article class="feature-card"><span class="feature-icon" aria-hidden="true"><x-lucide-chart-no-axes-column-increasing/></span><h3>Supervision nationale</h3><p>Consultation des dossiers, décisions administratives, états des compagnies et journal des opérations sensibles.</p></article>
            </div>
            <div class="scope-note"><span aria-hidden="true"><x-lucide-info/></span><div><strong>Périmètre de cette phase.</strong> La réservation, le paiement, la billetterie, le suivi GPS et l’application mobile seront traités dans des phases ultérieures.</div></div>
        </div>
    </section>

    <section class="content-section soft" id="parcours">
        <div class="section-inner journey-layout">
            <div class="journey-intro"><span class="section-kicker">Inscription guidée</span><h2>Soumettre un dossier en cinq étapes</h2><p>Le parcours sépare clairement les informations de la compagnie de celles du gestionnaire. Vous pouvez ensuite suivre la décision administrative depuis votre compte.</p><a class="button" href="{{ route('registration.identification') }}">Commencer l’inscription</a></div>
            <div class="journey-list">
                <article class="journey-step"><div><h3>Identifier la compagnie</h3><p>Raison sociale, RCCM obligatoire, coordonnées et adresse du siège.</p></div></article>
                <article class="journey-step"><div><h3>Créer le compte gestionnaire</h3><p>Un compte personnel sécurisé pour suivre le dossier et administrer la compagnie après activation.</p></div></article>
                <article class="journey-step"><div><h3>Choisir le mode de transport</h3><p>Un seul choix parmi les modes routier, fluvial, maritime et ferroviaire.</p></div></article>
                <article class="journey-step"><div><h3>Transmettre les justificatifs</h3><p>Autorisation d’exploitation et autres documents requis, avec leurs dates de validité.</p></div></article>
                <article class="journey-step"><div><h3>Suivre la décision</h3><p>Consultez l’état avec votre référence et votre e-mail. Connectez-vous pour corriger les documents signalés.</p></div></article>
            </div>
        </div>
    </section>

    <section class="assurance" aria-labelledby="assurance-title">
        <div class="section-inner assurance-layout">
            <div class="assurance-copy"><span class="eyebrow">Confiance et traçabilité</span><h2 id="assurance-title">Des règles claires pour un référentiel fiable</h2><p>Chaque dossier suit le même processus de contrôle. Les décisions, les documents et les accès restent rattachés à la compagnie concernée.</p></div>
            <div class="assurance-list">
                <div class="assurance-item"><span class="assurance-icon" aria-hidden="true"><x-lucide-circle-check/></span><div><strong>Validation administrative</strong><small>Activation après examen du dossier</small></div></div>
                <div class="assurance-item"><span class="assurance-icon" aria-hidden="true"><x-lucide-shield-check/></span><div><strong>Accès cloisonnés</strong><small>Chaque compte reste dans son périmètre</small></div></div>
                <div class="assurance-item"><span class="assurance-icon" aria-hidden="true"><x-lucide-file-check-2/></span><div><strong>Documents contrôlés</strong><small>Motif indiqué en cas de refus</small></div></div>
                <div class="assurance-item"><span class="assurance-icon" aria-hidden="true"><x-lucide-history/></span><div><strong>Actions historisées</strong><small>Journal conservé pendant six mois</small></div></div>
            </div>
        </div>
    </section>

    <section class="content-section" id="acces">
        <div class="section-inner">
            <div class="section-heading center"><span class="section-kicker">Accès adaptés</span><h2>Un espace pour chaque responsabilité</h2><p>Les droits sont attribués selon la fonction et peuvent être limités aux seules opérations nécessaires.</p></div>
            <div class="role-grid">
                <article class="role-card"><span>01</span><h3>Administration PNMG</h3><p>Supervision nationale, examen des dossiers, contrôle documentaire et cycle de vie des compagnies.</p></article>
                <article class="role-card"><span>02</span><h3>Gestionnaire</h3><p>Gestion de sa compagnie, de ses agences, des comptes habilités, des documents et de la flotte.</p></article>
                <article class="role-card"><span>03</span><h3>Responsable d’agence</h3><p>Accès limité à son agence, à ses utilisateurs autorisés et à son journal d’activité.</p></article>
                <article class="role-card"><span>04</span><h3>Agent</h3><p>Permissions personnalisées de consultation ou d’action selon les besoins de l’organisation.</p></article>
            </div>
        </div>
    </section>

    <section class="content-section soft" id="questions">
        <div class="section-inner faq-layout">
            <div class="faq-intro"><span class="section-kicker">Questions fréquentes</span><h2>Avant de commencer</h2><p>Les principales règles à connaître pour préparer correctement votre demande.</p></div>
            <div class="faq-list">
                <details><summary>Une compagnie peut-elle déclarer plusieurs modes de transport ?<x-lucide-chevron-down class="faq-chevron" aria-hidden="true" /></summary><p>Non. Un profil correspond à un seul mode de transport. Une activité différente doit faire l’objet d’un profil distinct.</p></details>
                <details><summary>Faut-il enregistrer les véhicules pendant l’inscription ?<x-lucide-chevron-down class="faq-chevron" aria-hidden="true" /></summary><p>Non. L’inventaire détaillé de la flotte est renseigné après l’activation de la compagnie. L’inscription porte d’abord sur l’identité, le gestionnaire, le mode de transport et les documents administratifs.</p></details>
                <details><summary>Que se passe-t-il lorsqu’un document est refusé ?<x-lucide-chevron-down class="faq-chevron" aria-hidden="true" /></summary><p>Le dossier reste en attente. Le motif est communiqué au gestionnaire, qui peut remplacer ou corriger la pièce avant un nouveau contrôle.</p></details>
                <details><summary>Quand les fonctions de gestion deviennent-elles accessibles ?<x-lucide-chevron-down class="faq-chevron" aria-hidden="true" /></summary><p>Après la validation et l’activation de la compagnie par l’administration PNMG. Avant cette décision, le gestionnaire accède au suivi de sa demande.</p></details>
                <details><summary>L’adresse e-mail de la compagnie sert-elle à se connecter ?<x-lucide-chevron-down class="faq-chevron" aria-hidden="true" /></summary><p>Non. Elle sert de contact administratif. La connexion utilise l’adresse personnelle déclarée pour le gestionnaire ou l’utilisateur habilité.</p></details>
            </div>
        </div>
    </section>

    <section class="final-cta">
        <div class="section-inner cta-panel"><div><h2>Prêt à enregistrer votre compagnie ?</h2><p>Préparez votre RCCM, les coordonnées du gestionnaire et vos documents administratifs.</p></div><a class="button" href="{{ route('registration.identification') }}">Démarrer la demande <x-lucide-arrow-right aria-hidden="true"/></a></div>
    </section>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-top">
            <div class="footer-brand"><a class="brand" href="{{ route('home') }}"><img src="{{ asset('images/pnmg/logo-horizontal-inverse.png') }}" alt="PNMG — Plateforme Nationale de Mobilité du Gabon" width="188" height="40"></a><p>Première brique de la Plateforme Nationale de Mobilité du Gabon, consacrée à l’enregistrement et à la gestion administrative des compagnies de transport.</p></div>
            <div class="footer-column"><strong>Plateforme</strong><a href="#plateforme">Fonctionnalités</a><a href="#parcours">Inscription</a><a href="#acces">Accès et rôles</a></div>
            <div class="footer-column"><strong>Accès et informations</strong><a href="{{ route('login') }}">Se connecter</a><a href="{{ route('public.application-status') }}">Suivre une demande</a><a href="{{ route('registration.identification') }}">Inscrire une compagnie</a><a href="{{ route('legal.terms') }}">Conditions générales</a><a href="{{ route('legal.privacy') }}">Politique de confidentialité</a><a href="mailto:contact@pnmg.ga">Contacter la PNMG : contact@pnmg.ga</a></div>
        </div>
        <div class="footer-bottom"><span class="status-pill">Plateforme opérationnelle</span><p>© {{ date('Y') }} Plateforme Nationale de Mobilité du Gabon</p><p>Module Gestion des compagnies de transport</p></div>
    </div>
</footer>
@include('layouts._loading')
<script>
    (() => {
        const button = document.querySelector('.menu-button');
        const nav = document.querySelector('.site-nav');
        if (!button || !nav) return;
        const close = () => {
            document.body.classList.remove('nav-open');
            button.setAttribute('aria-expanded', 'false');
            button.setAttribute('aria-label', 'Ouvrir le menu');
        };
        button.addEventListener('click', () => {
            const open = document.body.classList.toggle('nav-open');
            button.setAttribute('aria-expanded', open ? 'true' : 'false');
            button.setAttribute('aria-label', open ? 'Fermer le menu' : 'Ouvrir le menu');
        });
        nav.parentElement.querySelector('.nav-backdrop')?.addEventListener('click', close);
        nav.querySelectorAll('a').forEach(link => link.addEventListener('click', close));
        window.addEventListener('keydown', event => { if (event.key === 'Escape' && document.body.classList.contains('nav-open')) close(); });
        window.addEventListener('resize', () => { if (window.innerWidth >= 1024) close(); });
    })();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var panneau = document.querySelector('.modes-panel');
    if (!panneau) { return; }
    var items = panneau.querySelectorAll('.mode-item');
    var n = items.length;
    items.forEach(function (el, i) { el.style.setProperty('--a', (i * 360 / n - 90) + 'deg'); });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var panneau = document.querySelector('.modes-panel');
    if (!panneau || panneau.querySelector('.pnmg-bande')) { return; }
    var mots = ['Routier', 'Fluvial', 'Maritime', 'Ferroviaire'];
    var bande = document.createElement('div');
    bande.className = 'pnmg-bande';
    bande.setAttribute('aria-label', 'Modes de transport : ' + mots.join(', '));
    for (var k = 0; k < 2; k++) {
        var piste = document.createElement('div');
        piste.className = 'pnmg-bande-piste';
        piste.setAttribute('aria-hidden', k === 0 ? 'false' : 'true');
        for (var r = 0; r < 3; r++) {
            mots.forEach(function (m) {
                var sp = document.createElement('span');
                sp.textContent = m;
                piste.appendChild(sp);
            });
        }
        bande.appendChild(piste);
    }
    panneau.appendChild(bande);
});
</script>
</body>
</html>
