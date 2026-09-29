<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user:id,name,role')
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->string('action')->toString()))
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->integer('user')))
            ->when($request->filled('q'), fn ($q) => $q->where('description', 'like', '%'.$request->string('q')->toString().'%'))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        return view('admin.activity.index', [
            'logs' => $logs,
            'users' => User::query()->orderBy('name')->get(['id', 'name', 'role']),
            'actions' => ['created', 'updated', 'deleted', 'stock_in', 'stock_out', 'login'],
        ]);
    }
}
