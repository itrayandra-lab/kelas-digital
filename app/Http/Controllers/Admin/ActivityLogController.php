<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display activity log with filters.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Filter by causer
        if ($request->filled('causer_id')) {
            $query->where('causer_id', $request->causer_id)
                ->where('causer_type', 'App\\Models\\User');
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by description keyword
        if ($request->filled('description')) {
            $query->where('description', 'like', '%'.$request->description.'%');
        }

        $activities = $query->paginate(20);

        // Get unique causers for filter dropdown
        $causers = User::whereHas('actions')->get();

        return view('admin.activity-log.index', compact('activities', 'causers'));
    }
}
