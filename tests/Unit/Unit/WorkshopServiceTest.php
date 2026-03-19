<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopServiceTest extends TestCase
{
    use RefreshDatabase;

    private WorkshopService $service;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new WorkshopService();
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_can_create_workshop(): void
    {
        $data = [
            'title' => 'Vue Workshop',
            'description' => 'Impariamo Vue, il miglior framework per interfacce utente',
            'starts_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'ends_at' => now()->addDays(2)->addHours(2)->format('Y-m-d H:i:s'),
            'capacity' => 10,
        ];

        $workshop = $this->service->create($this->admin, $data);

        $this->assertInstanceOf(Workshop::class, $workshop);
        $this->assertEquals('Vue Workshop', $workshop->title);
        $this->assertEquals($this->admin->id, $workshop->created_by);
        $this->assertDatabaseHas('workshops', ['title' => 'Vue Workshop']);
    }

    public function test_can_update_workshop(): void
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->admin->id]);

        $this->service->update($workshop, ['title' => 'Titolo aggiornato']);

        $this->assertEquals('Titolo aggiornato', $workshop->fresh()->title);
        $this->assertDatabaseHas('workshops', ['title' => 'Titolo aggiornato']);
    }

    public function test_can_delete_workshop(): void
    {
        $workshop = Workshop::factory()->create(['created_by' => $this->admin->id]);
        $workshopId = $workshop->id;

        $this->service->delete($workshop);

        $this->assertDatabaseMissing('workshops', ['id' => $workshopId]);
    }

    public function test_get_future_workshops_excludes_past(): void
    {
        Workshop::factory()->create([
            'created_by' => $this->admin->id,
            'starts_at' => now()->subDays(2),
            'ends_at' => now()->subDays(2)->addHours(2),
        ]);

        Workshop::factory()->create([
            'created_by' => $this->admin->id,
            'starts_at' => now()->addDays(2),
            'ends_at' => now()->addDays(2)->addHours(2),
        ]);

        $workshops = $this->service->getFuture();

        $this->assertEquals(1, $workshops->total());
    }
}
