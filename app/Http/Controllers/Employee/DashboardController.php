<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\WorkshopService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly WorkshopService $workshopService
    ) {}

    public function index(): Response
    {
        $user = auth()->user();

        $workshops = $this->workshopService->getFuture();

        // Per ogni workshop aggiungiamo lo stato di iscrizione dell'utente corrente
        $workshops->getCollection()->transform(function ($workshop) use ($user) {
            $registration = Registration::where('user_id', $user->id)
                ->where('workshop_id', $workshop->id)
                ->first();

            $workshop->user_registration_status = $registration?->status;
            $workshop->available_seats = $workshop->availableSeats();

            return $workshop;
        });

        return Inertia::render('Employee/Dashboard', [
            'workshops' => $workshops,
        ]);
    }
}
