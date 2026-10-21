<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgencyActivityController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->permits('activity.view'), 403);
        $agency = $user->employee?->agency;
        $companyScope = $user->hasFreeRole() && $user->accessScope() === 'company';
        abort_unless($user->company_id && ($companyScope || (($user->role === 'agency_manager' || $user->hasFreeRole()) && $agency && $agency->company_id === $user->company_id)), 403);
        $filters = $request->validate(['date' => ['nullable', 'date'], 'action' => ['nullable', 'string', 'max:50']]);
        $query = AuditLog::query()->where('company_id', $user->company_id)->when(! $companyScope, fn ($q) => $q->where('agency_id', $agency->id));

        return view('agencies.activity', [
            'agency' => $companyScope ? null : $agency,
            'actions' => (clone $query)->distinct()->pluck('action'),
            'logs' => $query->with('user')
                ->when($filters['date'] ?? null, fn ($q, $date) => $q->whereDate('created_at', $date))
                ->when($filters['action'] ?? null, fn ($q, $action) => $q->where('action', $action))
                ->latest()->paginate(25)->withQueryString(),
        ]);
    }
}
