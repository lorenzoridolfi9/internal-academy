<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    private User $employee;
    private Workshop $workshop;

    protected function setUp(): void
    {
        parent::setUp();

        $this->employee = User::factory()->create(['role' => 'employee']);
        $this->workshop = Workshop::factory()->create(['capacity' => 5]);
    }

    public function test_employee_can_view_dashboard(): void
    {
        $response = $this->actingAs($this->employee)->get(route('employee.dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Employee/Dashboard'));
    }

    public function test_guest_cannot_view_employee_dashboard(): void
    {
        $response = $this->get(route('employee.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_cannot_view_employee_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('employee.dashboard'));

        $response->assertStatus(403);
    }

    public function test_employee_can_register_to_workshop(): void
    {
        $response = $this->actingAs($this->employee)
            ->post(route('employee.workshops.register', $this->workshop->id));

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_employee_is_waitlisted_when_workshop_is_full(): void
    {
        // Riempi il workshop
        User::factory(5)->create(['role' => 'employee'])->each(function ($user) {
            Registration::create([
                'user_id' => $user->id,
                'workshop_id' => $this->workshop->id,
                'status' => 'confirmed',
                'registered_at' => now(),
            ]);
        });

        $response = $this->actingAs($this->employee)
            ->post(route('employee.workshops.register', $this->workshop->id));

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => 'waitlisted',
        ]);
    }

    public function test_employee_can_cancel_registration(): void
    {
        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => 'confirmed',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($this->employee)
            ->delete(route('employee.workshops.unregister', $this->workshop->id));

        $response->assertRedirect(route('employee.dashboard'));
        $this->assertDatabaseMissing('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
        ]);
    }

    public function test_employee_cannot_register_twice(): void
    {
        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $this->workshop->id,
            'status' => 'confirmed',
            'registered_at' => now(),
        ]);

        $response = $this->actingAs($this->employee)
            ->post(route('employee.workshops.register', $this->workshop->id));

        $response->assertRedirect(route('employee.dashboard'));
        $response->assertSessionHas('error', 'Sei già iscritto a questo workshop.');
    }

    public function test_waitlisted_employee_is_promoted_on_cancellation(): void
    {
        $confirmed = User::factory()->create(['role' => 'employee']);

        // Riempi il workshop con un solo posto
        $fullWorkshop = Workshop::factory()->create(['capacity' => 1]);

        Registration::create([
            'user_id' => $confirmed->id,
            'workshop_id' => $fullWorkshop->id,
            'status' => 'confirmed',
            'registered_at' => now(),
        ]);

        Registration::create([
            'user_id' => $this->employee->id,
            'workshop_id' => $fullWorkshop->id,
            'status' => 'waitlisted',
            'registered_at' => now(),
        ]);

        // Il confermato si cancella
        $this->actingAs($confirmed)
            ->delete(route('employee.workshops.unregister', $fullWorkshop->id));

        // L'employee in waiting list deve essere promosso
        $this->assertDatabaseHas('registrations', [
            'user_id' => $this->employee->id,
            'workshop_id' => $fullWorkshop->id,
            'status' => 'confirmed',
        ]);
    }
}
