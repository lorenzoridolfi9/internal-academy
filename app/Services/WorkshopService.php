<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkshopService
{
    public function getAll(): LengthAwarePaginator
    {
        return Workshop::with('creator')
            ->withCount('confirmedRegistrations')
            ->orderBy('starts_at')
            ->paginate(10);
    }

    public function getFuture(): LengthAwarePaginator
    {
        return Workshop::with('creator')
            ->withCount('confirmedRegistrations')
            ->where('starts_at', '>', now())
            ->orderBy('starts_at')
            ->paginate(10);
    }

    public function create(User $admin, array $data): Workshop
    {
        return Workshop::create([
            'created_by' => $admin->id,
            'title' => $data['title'],
            'description' => $data['description'],
            'starts_at' => $data['starts_at'],
            'ends_at' => $data['ends_at'],
            'capacity' => $data['capacity'],
        ]);
    }

    public function update(Workshop $workshop, array $data): Workshop
    {
        $workshop->update($data);
        return $workshop;
    }

    public function delete(Workshop $workshop): void
    {
        $workshop->delete();
    }
}
