<?php

namespace App\Services;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Support\Facades\DB;

class RegistrationService
{
    public function register(User $user, Workshop $workshop): Registration
    {
        // Controlla se l'utente è già iscritto
        $existing = Registration::where('user_id', $user->id)
            ->where('workshop_id', $workshop->id)
            ->first();

        if ($existing) {
            throw new \Exception('Sei già iscritto a questo workshop.');
        }

        // Controlla sovrapposizioni orarie
        $overlap = Registration::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('workshop', function ($query) use ($workshop) {
                $query->where('starts_at', '<', $workshop->ends_at)
                      ->where('ends_at', '>', $workshop->starts_at);
            })->exists();

        if ($overlap) {
            throw new \Exception('Hai già un workshop in questo orario.');
        }

        // Determina lo status
        $status = $workshop->isFull() ? 'waitlisted' : 'confirmed';

        return Registration::create([
            'user_id' => $user->id,
            'workshop_id' => $workshop->id,
            'status' => $status,
            'registered_at' => now(),
        ]);
    }

    public function cancel(User $user, Workshop $workshop): void
    {
        DB::transaction(function () use ($user, $workshop) {
            $registration = Registration::where('user_id', $user->id)
                ->where('workshop_id', $workshop->id)
                ->firstOrFail();

            $wasConfirmed = $registration->isConfirmed();
            $registration->delete();

            // Se era confermato, promuovi il primo in waiting list (FIFO)
            if ($wasConfirmed) {
                $next = Registration::where('workshop_id', $workshop->id)
                    ->where('status', 'waitlisted')
                    ->orderBy('registered_at')
                    ->first();

                if ($next) {
                    $next->update([
                        'status' => 'confirmed',
                    ]);
                }
            }
        });
    }
}
