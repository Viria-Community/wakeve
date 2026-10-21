<?php

namespace App\Http\Controllers;

use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Agency;
use App\Models\Company;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Employee::class);

        $query = Employee::query()
            ->with(['company', 'agency', 'user'])
            ->whereHas('user')
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                $query->where('company_id', $request->user()->company_id);

                if ($request->user()->accessScope() === 'agency') {
                    $query->where('agency_id', $request->user()->employee?->agency_id);
                } elseif ($request->user()->accessScope() === 'self') {
                    $query->where('user_id', $request->user()->id);
                }
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->input('search'));
                $query->where(fn ($query) => $query->where('last_name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('internal_role', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($users) => $users->where('email', 'like', "%{$search}%")));
            })
            ->when($request->filled('status'), fn ($query) => $query->whereHas('user', fn ($users) => $users->where('status', $request->input('status'))))
            ->orderBy('last_name')->orderBy('first_name');

        $summaryQuery = Employee::query()
            ->whereHas('user')
            ->when(! $request->user()->isAdmin(), function ($query) use ($request) {
                $query->where('company_id', $request->user()->company_id);

                if ($request->user()->accessScope() === 'agency') {
                    $query->where('agency_id', $request->user()->employee?->agency_id);
                } elseif ($request->user()->accessScope() === 'self') {
                    $query->where('user_id', $request->user()->id);
                }
            });

        $employeeSummary = (object) [
            'total' => (clone $summaryQuery)->count(),
            'active' => (clone $summaryQuery)->whereHas('user', fn ($users) => $users->where('status', 'active'))->count(),
            'inactive' => (clone $summaryQuery)->whereHas('user', fn ($users) => $users->where('status', 'inactive'))->count(),
        ];

        return view('employees.index', [
            'employees' => $query->paginate(15)->withQueryString(),
            'employeeSummary' => $employeeSummary,
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Employee::class);

        return view('employees.create', $this->formData($request, new Employee));
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $employee = DB::transaction(function () use ($request) {
            $companyId = $request->user()->isAdmin()
                ? $request->validated('company_id')
                : $request->user()->company_id;
            $agencyId = $request->user()->accessScope() === 'agency'
                ? $request->user()->employee?->agency_id
                : $request->validated('agency_id');

            $user = User::create([
                'name' => $request->validated('first_name').' '.$request->validated('last_name'),
                'email' => $request->validated('account_email'),
                'password' => $request->validated('password'),
                'role' => $request->validated('account_role'),
                'custom_role_id' => $request->validated('custom_role_id'),
                'company_id' => $companyId,
                'status' => 'active',
            ]);

            $employee = Employee::create([
                'company_id' => $companyId,
                'agency_id' => $agencyId,
                'user_id' => $user->id,
                'last_name' => $request->validated('last_name'),
                'first_name' => $request->validated('first_name'),
                'internal_role' => $request->validated('internal_role'),
                'status' => 'active',
            ]);

            return $employee;
        });

        return redirect()->route('employees.show', $employee)
            ->with('status', 'L’employé et son compte ont été créés.');
    }

    public function show(Employee $employee): View
    {
        Gate::authorize('view', $employee);

        return view('employees.show', ['employee' => $employee->load(['company', 'agency', 'user'])]);
    }

    public function edit(Request $request, Employee $employee): View
    {
        Gate::authorize('update', $employee);

        return view('employees.edit', $this->formData($request, $employee));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        DB::transaction(function () use ($request, $employee) {
            $data = $request->validated();

            if ($request->user()->accessScope() === 'agency') {
                $data['agency_id'] = $request->user()->employee?->agency_id;
            }

            $employee->update($data);
            $employee->user?->update([
                'name' => $employee->fullName(),
            ]);
        });

        return redirect()->route('employees.show', $employee)
            ->with('status', 'Les informations de l’employé ont été mises à jour.');
    }

    private function formData(Request $request, Employee $employee): array
    {
        $companies = $request->user()->isAdmin()
            ? Company::query()->orderBy('legal_name')->get()
            : collect();

        $agencies = Agency::query()->with('company')
            ->when(! $request->user()->isAdmin(), fn ($query) => $query->where('company_id', $request->user()->company_id))
            ->when($request->user()->accessScope() === 'agency', fn ($query) => $query->whereKey($request->user()->employee?->agency_id))
            ->orderBy('name')->get();

        return compact('employee', 'companies', 'agencies');
    }
}
