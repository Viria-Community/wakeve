<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->permitsPlatform('audit.view'), 403);

        $logs = AuditLog::query()->with(['user', 'company'])
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->input('action')))
            ->when($request->filled('user_id'), fn ($query) => $query->where('user_id', $request->integer('user_id')))
            ->when($request->filled('company_id'), fn ($query) => $query->where('company_id', $request->integer('company_id')))
            ->when($request->filled('date'), fn ($query) => $query->whereDate('created_at', $request->input('date')))
            ->latest()->paginate(30)->withQueryString();

        return view('admin.audit-logs.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->get(['id', 'name']),
            'companies' => Company::query()->orderBy('legal_name')->get(['id', 'legal_name']),
            'actions' => AuditLog::query()->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
