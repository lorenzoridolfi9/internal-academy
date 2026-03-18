<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Workshop extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by',
        'title',
        'description',
        'starts_at',
        'ends_at',
        'capacity',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function confirmedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)->where('status', 'confirmed');
    }

    public function waitlistedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class)->where('status', 'waitlisted')->orderBy('registered_at');
    }

    public function availableSeats(): int
    {
        return $this->capacity - $this->confirmedRegistrations()->count();
    }

    public function isFull(): bool
    {
        return $this->availableSeats() <= 0;
    }
}
