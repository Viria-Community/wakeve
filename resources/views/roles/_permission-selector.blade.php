@php($selectedPermissions = collect($selectedPermissions ?? []))
<section class="role-permission-selector" data-permission-selector aria-labelledby="permission-selector-title">
    <div class="role-selector-toolbar">
        <div>
            <h2 id="permission-selector-title">{{ $legend ?? 'Actions autorisées' }}</h2>
            <p>Activez uniquement les actions nécessaires à ce rôle.</p>
        </div>
        <div class="role-selector-actions">
            <span><strong data-selected-count>0</strong> sélectionnée(s)</span>
            <button type="button" data-permissions-action="all">Tout sélectionner</button>
            <button type="button" data-permissions-action="none">Tout retirer</button>
        </div>
    </div>
    <div class="role-permission-grid">
        @foreach($groups as $groupKey => $group)
            <section class="role-permission-group" data-permission-group="{{ $groupKey }}">
                <div class="role-permission-group-head">
                    <div><h2>{{ $group['label'] }}</h2><p>{{ $group['description'] }}</p></div>
                    <button type="button" data-select-group>Sélectionner le module</button>
                </div>
                <div class="role-permission-options">
                    @foreach($group['permissions'] as $key)
                        <label class="role-permission-option" data-permission="{{ $key }}">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" @checked($selectedPermissions->contains($key))>
                            <span>{{ $labels[$key] ?? $key }}</span>
                        </label>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
</section>
<script>
(() => {
    const selector = document.querySelector('[data-permission-selector]');
    if (!selector) return;

    const enabledCheckboxes = (container = selector) => [...container.querySelectorAll('input[type="checkbox"]')].filter((input) => !input.disabled);
    const refresh = () => {
        selector.querySelector('[data-selected-count]').textContent = enabledCheckboxes().filter((input) => input.checked).length;
        selector.querySelectorAll('[data-permission-group]').forEach((group) => {
            group.classList.toggle('has-selection', enabledCheckboxes(group).some((input) => input.checked));
        });
    };

    selector.addEventListener('change', refresh);
    selector.addEventListener('permissions:updated', refresh);
    selector.querySelector('[data-permissions-action="all"]').addEventListener('click', () => {
        enabledCheckboxes().forEach((input) => input.checked = true);
        refresh();
    });
    selector.querySelector('[data-permissions-action="none"]').addEventListener('click', () => {
        enabledCheckboxes().forEach((input) => input.checked = false);
        refresh();
    });
    selector.querySelectorAll('[data-select-group]').forEach((button) => button.addEventListener('click', () => {
        const checkboxes = enabledCheckboxes(button.closest('[data-permission-group]'));
        const shouldSelect = checkboxes.some((input) => !input.checked);
        checkboxes.forEach((input) => input.checked = shouldSelect);
        refresh();
    }));
    refresh();
})();
</script>
