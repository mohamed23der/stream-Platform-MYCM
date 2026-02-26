<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'resolution',
        'storage_driver',
        'file_path',
        'hls_path',
        'encryption_key_path',
        'visibility',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function allowedDomains(): HasMany
    {
        return $this->hasMany(AllowedDomain::class);
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(VideoAccessLog::class);
    }

    public function isReady(): bool
    {
        return $this->status === 'ready';
    }

    public function getHashAttribute(): string
    {
        return base64_encode(encrypt($this->id));
    }
}
