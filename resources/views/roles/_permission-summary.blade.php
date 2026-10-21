@php
    $grantedPermissions = collect($permissions ?? [])->values();
    $grantedGroups = collect($groups)->map(function ($group) use ($grantedPermissions) {
        return $group + ['granted' => $grantedPermissions->intersect($group['permissions'])->values()];
    })->filter(fn ($group) => $group['granted']->isNotEmpty());
@endphp
<div class="role-access-summary">
    <div class="role-access-count">
        <strong>{{ $grantedPermissions->count() }}</strong>
        <span>{{ $grantedPermissions->count() > 1 ? 'autorisations' : 'autorisation' }}</span>
    </div>
    @if($grantedGroups->isEmpty())
        <p class="role-no-access">{{ $emptyText ?? 'Aucun accès métier accordé.' }}</p>
    @else
        <div class="role-module-badges" aria-label="Modules accessibles">
            @foreach($grantedGroups as $group)
                <span>{{ $group['label'] }} <strong>{{ $group['granted']->count() }}</strong></span>
            @endforeach
        </div>
        <details class="role-permission-details">
            <summary>Voir le détail des permissions</summary>
            <div class="role-permission-groups">
                @foreach($grantedGroups as $group)
                    <section>
                        <h4>{{ $group['label'] }}</h4>
                        <ul>
                            @foreach($group['granted'] as $permission)
                                <li>{{ $labels[$permission] ?? $permission }}</li>
                            @endforeach
                        </ul>
                    </section>
                @endforeach
            </div>
        </details>
    @endif
</div>
