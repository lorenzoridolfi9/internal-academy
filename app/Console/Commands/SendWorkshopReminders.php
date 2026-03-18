<?php

namespace App\Console\Commands;

use App\Mail\WorkshopReminderMail;
use App\Models\Workshop;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

#[Signature('academy:remind')]
#[Description('Invia email di promemoria per i workshop di domani')]
class SendWorkshopReminders extends Command
{
    public function handle(): void
    {
        $tomorrow = now()->addDay();

        $workshops = Workshop::with('confirmedRegistrations.user')
            ->whereDate('starts_at', $tomorrow->toDateString())
            ->get();

        if ($workshops->isEmpty()) {
            $this->info('Nessun workshop in programma per domani.');
            return;
        }

        $count = 0;

        foreach ($workshops as $workshop) {
            foreach ($workshop->confirmedRegistrations as $registration) {
                Mail::to($registration->user->email)
                    ->send(new WorkshopReminderMail($workshop, $registration->user));
                $count++;
            }

            $this->info("✅ Reminder inviati per: {$workshop->title} ({$workshop->confirmedRegistrations->count()} partecipanti)");
        }

        $this->info("Totale email inviate: {$count}");
    }
}
