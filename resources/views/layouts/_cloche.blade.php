@php($nonLues = auth()->user()?->unreadNotifications()->latest()->limit(5)->get() ?? collect())
@php($total = auth()->user()?->unreadNotifications()->count() ?? 0)
<details class="cloche">
    <summary class="cloche-bouton" aria-label="Notifications ({{ $total }} non lues)">
        <x-lucide-bell aria-hidden="true"/>
        @if($total > 0)<span class="cloche-badge">{{ $total > 9 ? '9+' : $total }}</span>@endif
    </summary>
    <div class="cloche-panneau">
        <div class="cloche-titre">Notifications <span>{{ $total }} non lue(s)</span></div>
        @forelse($nonLues as $notification)
            <a class="cloche-item" href="{{ route('notifications.read', $notification) }}">
                <strong>{{ $notification->data['title'] ?? 'Nouvelle notification' }}</strong>
                <small>{{ $notification->data['message'] ?? '' }}</small>
            </a>
        @empty
            <div class="cloche-vide">Aucune notification non lue.</div>
        @endforelse
    </div>
</details>
<style>
.cloche { position: relative; }
.cloche summary { list-style: none; }
.cloche summary::-webkit-details-marker { display: none; }
.cloche-bouton { position: relative; width: 40px; height: 40px; border-radius: 12px; display: grid; place-items: center; background: #fff; border: 1px solid #e6e2d6; color: #264653; cursor: pointer; transition: transform .2s ease, box-shadow .2s ease; }
.cloche-bouton:hover { transform: translateY(-2px); box-shadow: 0 8px 18px rgba(38,70,83,.15); }
.cloche-bouton svg { width: 20px; height: 20px; }
.cloche-badge { position: absolute; top: -6px; right: -6px; min-width: 20px; height: 20px; padding: 0 6px; border-radius: 999px; background: #E9C46A; color: #264653; font-size: 11px; font-weight: 800; display: grid; place-items: center; box-shadow: 0 0 0 2px #fff; }
.cloche-panneau { position: absolute; right: 0; top: 50px; width: 320px; background: #fff; border: 1px solid #e6e2d6; border-radius: 16px; box-shadow: 0 18px 40px rgba(38,70,83,.18); padding: 12px; z-index: 70; }
.cloche-titre { display: flex; justify-content: space-between; align-items: center; font-weight: 700; color: #264653; padding: 6px 8px 10px; border-bottom: 1px solid #efece2; margin-bottom: 6px; }
.cloche-titre span { font-size: 12px; color: #64748b; font-weight: 600; }
.cloche-item { display: block; padding: 10px 8px; border-radius: 10px; text-decoration: none; color: #334155; transition: background .2s ease; }
.cloche-item:hover { background: #fbf9f3; }
.cloche-item strong { display: block; font-size: 14px; color: #264653; }
.cloche-item small { color: #64748b; font-size: 12px; }
.cloche-vide { padding: 14px 8px; color: #64748b; font-size: 13px; text-align: center; }
</style>
