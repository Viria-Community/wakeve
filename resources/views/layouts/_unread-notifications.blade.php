@php($unreadNotifications = auth()->user()->unreadNotifications()->latest()->limit(3)->get())
@if($unreadNotifications->isNotEmpty())
<section class="notification-center" aria-label="Notifications non lues" aria-live="polite">
    <div class="notification-center-title"><strong>Notifications</strong><span>{{ auth()->user()->unreadNotifications()->count() }} non lue(s)</span></div>
    @foreach($unreadNotifications as $notification)
        <a class="notification-entry" href="{{ route('notifications.read', $notification) }}">
            <span class="notification-symbol" aria-hidden="true"><x-lucide-circle-alert/></span>
            <span><strong>{{ $notification->data['title'] ?? 'Nouvelle notification' }}</strong><small>{{ $notification->data['message'] ?? '' }}</small>@if(!empty($notification->data['reason']))<small><b>Motif :</b> {{ $notification->data['reason'] }}</small>@endif</span>
            <span class="notification-action">Consulter</span>
        </a>
    @endforeach
</section>
@endif
