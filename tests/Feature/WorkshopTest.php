<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workshop;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $employee;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->employee = User::factory()->create(['role' => 'employee']);
    }

    public function test_admin_can_view_workshops_list(): void
    {
        Workshop::factory(3)->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.workshops.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Workshops/Index')
            ->has('workshops.data', 3)
        );
    }

    public function test_employee_cannot_access_admin_workshops(): void
    {
        $response = $this->actingAs($this->employee)->get(route('admin.workshops.index'));

        $response->assertStatus(403);
    }

    public function test_guest_cannot_access_admin_workshops(): void
    {
        $response = $this->get(route('admin.workshops.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_admin_can_create_workshop(): void
    {
        $data = [
            'title' => 'Nuovo Workshop',
            'description' => 'Descrizione del workshop',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 10,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.workshops.store'), $data);

        $response->assertRedirect(route('admin.workshops.index'));
        $this->assertDatabaseHas('workshops', ['title' => 'Nuovo Workshop']);
    }

    public function test_admin_can_update_workshop(): void
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->patch(
            route('admin.workshops.update', $workshop->id),
            ['title' => 'Titolo aggiornato',
             'description' => $workshop->description,
             'starts_at' => $workshop->starts_at->format('Y-m-d H:i:s'),
             'ends_at' => $workshop->ends_at->format('Y-m-d H:i:s'),
             'capacity' => $workshop->capacity,
            ]
        );

        $response->assertRedirect(route('admin.workshops.index'));
        $this->assertDatabaseHas('workshops', ['title' => 'Titolo aggiornato']);
    }

    public function test_admin_can_delete_workshop(): void
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->admin->id]);
        $workshopId = $workshop->id;

        $response = $this->actingAs($this->admin)->delete(
            route('admin.workshops.destroy', $workshop->id)
        );

        $response->assertRedirect(route('admin.workshops.index'));
        $this->assertDatabaseMissing('workshops', ['id' => $workshopId]);
    }

    public function test_employee_cannot_create_workshop(): void
    {
        $data = [
            'title' => 'Workshop non autorizzato',
            'description' => 'Descrizione',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 10,
        ];

        $response = $this->actingAs($this->employee)->post(route('admin.workshops.store'), $data);

        $response->assertStatus(403);
    }

    public function test_workshop_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.workshops.store'), [
            'title' => '',
            'capacity' => -1,
        ]);

        $response->assertSessionHasErrors(['title', 'capacity']);
    }
}
