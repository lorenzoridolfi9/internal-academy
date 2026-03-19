<?php

namespace Tests\Unit;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationServiceTest extends TestCase
{
    use RefreshDatabase;

    private RegistrationService $service;
    private User $employee;
    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new RegistrationService();
        $this->employee = User::factory()->create(['role' => 'employee']);
        $this->workshop = Workshop::factory()->create(['capacity' => 3]);
    }

    public function test_employee_can_register_to_workshop(): void
    {
        $registration = $this->service->register($this->employee, $this->workshop);

        $this->assertEquals('confirmed', $registration->status);
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_employee_is_waitlisted_when_workshop_is_full(): void
    {
        // Riempi il workshop
        User::factory(3)->create(['role' => 'employee'])->each(function ($user) {
            $this->service->register($user, $this->workshop);
        });

        // Prova a iscrivere un altro employee
        $registration = $this->service->register($this->employee, $this->workshop);

        $this->assertEquals('waitlisted', $registration->status);
    }

    public function test_employee_cannot_register_twice(): void
    {
        $this->service->register($this->employee, $this->workshop);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Sei già iscritto a questo workshop.');

        $this->service->register($this->employee, $this->workshop);
    }

    public function test_employee_cannot_register_to_overlapping_workshops(): void
    {
        $workshop2 = Workshop::factory()->create([
            'starts_at' => $this->workshop->starts_at,
            'ends_at' => $this->workshop->ends_at,
            'capacity' => 10,
        ]);

        $this->service->register($this->employee, $this->workshop);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Hai già un workshop in questo orario.');

        $this->service->register($this->employee, $workshop2);
    }

    public function test_first_waitlisted_is_promoted_on_cancellation(): void
    {
        $employees = User::factory(3)->create(['role' => 'employee']);

        // Riempi il workshop
        foreach ($employees as $emp) {
            $this->service->register($emp, $this->workshop);
        }

        // Metti l'employee in waiting list
        $waitlisted = $this->service->register($this->employee, $this->workshop);
        $this->assertEquals('waitlisted', $waitlisted->status);

        // Cancella il primo iscritto
        $this->service->cancel($employees->first(), $this->workshop);

        // Verifica che l'employee in waiting list sia stato promosso
        $this->assertEquals('confirmed', $waitlisted->fresh()->status);
    }

    public function test_cancel_removes_registration(): void
    {
        $this->service->register($this->employee, $this->workshop);

        $this->service->cancel($this->employee, $this->workshop);

        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
        ]);
    }
}
