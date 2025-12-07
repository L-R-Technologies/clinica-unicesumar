<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index()
    {
        return view('activity-log.index');
    }

    public function show($id)
    {
        $log = Activity::with(['causer', 'subject'])->findOrFail($id);

        return view('activity-log.show', compact('log'));
    }
}
