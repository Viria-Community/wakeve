<?php

namespace App\Http\Controllers;

use App\Http\Requests\Agency\StoreAgencyRequest;
use App\Http\Requests\Agency\UpdateAgencyRequest;
use App\Models\Agency;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AgencyController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Agency::class);

        $query = Agency::query()
            ->with('company')
            ->withCount([
                'employees' => fn ($query) => $query->whereHas('user', fn ($users) => $users->where('status', 'active')),
            ])
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                $query->where('company_id', $request->user()->company_id);

                if (in_array($request->user()->accessScope(), ['agency', 'self'], true)) {
                    $query->whereKey($request->user()->employee?->agency_id);
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(fn ($query) => $query->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%"));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('company_id') && $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->integer('company_id')))
            ->orderBy('name');

        $summaryQuery = Agency::query()
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                $query->where('company_id', $request->user()->company_id);

                if (in_array($request->user()->accessScope(), ['agency', 'self'], true)) {
                    $query->whereKey($request->user()->employee?->agency_id);
                }
            });

        return view('agencies.index', [
            'agencies' => $query->paginate(15)->withQueryString(),
            'companies' => $request->user()->isAdmin()
                ? Company::query()->orderBy('legal_name')->get()
                : collect(),
            'agencySummary' => $summaryQuery->selectRaw('COUNT(*) as total')
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END), 0) as active")
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END), 0) as inactive")
                ->selectRaw('COUNT(DISTINCT company_id) as companies')
                ->first(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Agency::class);

        return view('agencies.create', [
            'agency' => new Agency,
            'companies' => $request->user()->isAdmin()
                ? Company::query()->orderBy('legal_name')->get()
                : collect(),
        ]);
    }

    public function store(StoreAgencyRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('company_id');
        $data['company_id'] = $request->user()->isAdmin()
            ? $request->validated('company_id')
            : $request->user()->company_id;

        $agency = Agency::create($data);

        return redirect()->route('agencies.show', $agency)
            ->with('status', 'L’agence a été créée.');
    }

    public function show(Agency $agency): View
    {
        Gate::authorize('view', $agency);
        $agency->load(['company', 'employees.user']);
        $agency->setRelation('employees', $agency->employees
            ->filter(fn ($employee) => $employee->user?->status === 'active' && auth()->user()->can('view', $employee))
            ->values());

        return view('agencies.show', [
            'agency' => $agency,
        ]);
    }

    public function edit(Agency $agency): View
    {
        Gate::authorize('update', $agency);

        return view('agencies.edit', compact('agency'));
    }

    public function update(UpdateAgencyRequest $request, Agency $agency): RedirectResponse
    {
        $agency->update($request->validated());

        return redirect()->route('agencies.show', $agency)
            ->with('status', 'Les informations de l’agence ont été mises à jour.');
    }
}
