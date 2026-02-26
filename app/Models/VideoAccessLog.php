<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAccessLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'video_id',
        'ip_address',
        'user_agent',
        'referer',
        'access_time',
        'blocked',
    ];

    protected function casts(): array
    {
        return [
            'access_time' => 'datetime',
            'blocked' => 'boolean',
        ];
    }

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}
