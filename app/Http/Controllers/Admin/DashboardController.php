<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Workshop;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalWorkshops = Workshop::count();
        $totalRegistrations = Registration::where('status', 'confirmed')->count();
        $totalEmployees = User::where('role', 'employee')->count();

        $mostPopular = Workshop::withCount('confirmedRegistrations')
            ->orderByDesc('confirmed_registrations_count')
            ->first();

        $upcomingWorkshops = Workshop::withCount('confirmedRegistrations')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalWorkshops' => $totalWorkshops,
                'totalRegistrations' => $totalRegistrations,
                'totalEmployees' => $totalEmployees,
            ],
            'mostPopular' => $mostPopular,
            'upcomingWorkshops' => $upcomingWorkshops,
        ]);
    }
}
