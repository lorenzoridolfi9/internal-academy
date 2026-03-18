<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Http\RedirectResponse;

class RegistrationController extends Controller
{
    public function __construct(
        private readonly RegistrationService $registrationService
    ) {}

    public function store(Workshop $workshop): RedirectResponse
    {
        try {
            $this->registrationService->register(auth()->user(), $workshop);

            return redirect()->route('employee.dashboard')
                ->with('success', 'Iscrizione effettuata con successo.');
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')
                ->with('error', $e->getMessage());
        }
    }

    public function destroy(Workshop $workshop): RedirectResponse
    {
        try {
            $this->registrationService->cancel(auth()->user(), $workshop);

            return redirect()->route('employee.dashboard')
                ->with('success', 'Iscrizione cancellata con successo.');
        } catch (\Exception $e) {
            return redirect()->route('employee.dashboard')
                ->with('error', $e->getMessage());
        }
    }
}
