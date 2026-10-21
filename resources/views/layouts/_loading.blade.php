<style>
.page-loader{position:fixed;inset:0;z-index:9999;display:grid;place-items:center;background:rgba(250,249,246,.92);opacity:0;visibility:hidden;transition:opacity .16s ease,visibility .16s ease}.page-loader[data-visible="true"]{opacity:1;visibility:visible}.page-loader-panel{width:min(360px,calc(100% - 40px));padding:24px;border:1px solid #d7e0dd;background:#fff;color:#264653;text-align:center}.page-loader-mark{display:block;box-sizing:border-box;width:42px;min-width:42px;height:42px;min-height:42px;aspect-ratio:1;margin:0 auto 14px;padding:0;border:3px solid #dce7e3;border-top-color:#087a5a;border-radius:50%;line-height:0;animation:pnmg-spin .75s linear infinite}.page-loader strong,.page-loader small{display:block}.page-loader small{margin-top:5px;color:#596970}@keyframes pnmg-spin{to{transform:rotate(360deg)}}@media(prefers-reduced-motion:reduce){.page-loader,.page-loader-mark{transition:none;animation:none}.page-loader-mark{border-color:#087a5a}}
</style>
<div class="page-loader" id="page-loader" data-visible="false" role="status" aria-live="polite" aria-hidden="true">
    <div class="page-loader-panel"><span class="page-loader-mark" aria-hidden="true"></span><strong>Chargement en cours</strong><small>Merci de patienter quelques instants.</small></div>
</div>
<script>
(() => {
    const loader = document.getElementById('page-loader');
    if (!loader) return;
    const show = () => { loader.dataset.visible = 'true'; loader.setAttribute('aria-hidden', 'false'); };
    document.addEventListener('submit', event => {
        if (event.defaultPrevented || !event.target.checkValidity()) return;
        const submitter = event.submitter;
        if (submitter) { submitter.disabled = true; submitter.setAttribute('aria-disabled', 'true'); }
        show();
    });
    document.addEventListener('click', event => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey || link.target || link.hasAttribute('download')) return;
        const url = new URL(link.href, window.location.href);
        if (/\/(?:download|telecharger)(?:\/|$)/.test(url.pathname)) return;
        if (url.origin === window.location.origin && url.href !== window.location.href && !link.getAttribute('href').startsWith('#')) show();
    });
    window.addEventListener('pageshow', () => { loader.dataset.visible = 'false'; loader.setAttribute('aria-hidden', 'true'); });
})();
</script>
