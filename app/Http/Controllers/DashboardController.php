<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Resume;
use App\Models\Vacancie;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'activeVacancies' => Vacancie::where('active', true)->count(),
                'totalCandidates' => Resume::count(),
                'interviewsThisWeek' => Interview::whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            ],
            'recentApplications' => Resume::latest()->take(5)->get(),
        ]);
    }
}
