<?php

namespace App\Http\Controllers;

use App\Models\AgeReduction;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgeReductionController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->compagnie($request);

        return view('reductions.index', [
            'company' => $company,
            'reductions' => AgeReduction::where('company_id', $company->id)->orderBy('min_age')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $company = $this->compagnie($request);

        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'min_age' => ['required', 'integer', 'min:0', 'max:120'],
            'max_age' => ['nullable', 'integer', 'min:0', 'max:120', 'gte:min_age'],
            'percent' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        AgeReduction::create($data + ['company_id' => $company->id, 'status' => 'active']);

        return back()->with('status', 'La réduction a été enregistrée.');
    }

    public function toggle(Request $request, AgeReduction $reduction): RedirectResponse
    {
        $company = $this->compagnie($request);
        abort_unless($reduction->company_id === $company->id, 403);

        $reduction->update(['status' => $reduction->status === 'active' ? 'inactive' : 'active']);

        return back()->with('status', 'Le statut de la réduction a été modifié.');
    }

    private function compagnie(Request $request): Company
    {
        $user = $request->user();
        abort_unless($user->role === 'company_manager' && $user->company_id, 403);

        return Company::findOrFail($user->company_id);
    }
}
