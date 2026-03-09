<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Deal extends Model
{
    protected $fillable = [
        'lead_id',
        'stage',
        'priority',
        'value_estimate',
        'assigned_user_id',
        'notes',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'value_estimate' => 'decimal:2',
            'meta' => 'array',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }
}
