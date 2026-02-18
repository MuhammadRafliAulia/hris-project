<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    /**
     * @return User
     */
    private function authUser()
    {
        /** @var User $user */
        $user = Auth::user();
        return $user;
    }

    public function index(Request $request)
    {
        if (! optional($this->authUser())->isSuperAdmin()) {
            abort(403);
        }

        $query = ActivityLog::query()->orderBy('created_at', 'desc');

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Filter by action
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('description', 'like', "%$q%")
                    ->orWhere('user_name', 'like', "%$q%")
                    ->orWhere('module', 'like', "%$q%");
            });
        }

        // Date filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(50);

        // For filter dropdowns
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');
        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('activity-logs.index', compact('logs', 'modules', 'actions', 'users'));
    }

    public function clear(Request $request)
    {
        if (! optional($this->authUser())->isSuperAdmin()) {
            abort(403);
        }

        ActivityLog::truncate();

        ActivityLog::log('clear', 'system', 'Menghapus semua log aktivitas');

        return redirect()->route('activity-logs.index')
            ->with('success', 'Semua log aktivitas berhasil dihapus.');
    }
}
