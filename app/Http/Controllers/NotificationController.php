<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function __invoke(Request $request, DatabaseNotification $notification): RedirectResponse
    {
        abort_unless(
            $notification->notifiable_type === $request->user()::class
            && (int) $notification->notifiable_id === (int) $request->user()->getKey(),
            403
        );

        $notification->markAsRead();
        $url = $notification->data['url'] ?? route('dashboard');

        abort_unless(is_string($url) && str_starts_with($url, '/'), 400);

        return redirect($url);
    }
}
