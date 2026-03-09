<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Followup extends Model
{
    protected $fillable = [
        'deal_id',
        'quote_id',
        'step',
        'next_run',
        'status',
        'subject',
        'message',
        'sent_at',
        'error_message',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'next_run' => 'datetime',
            'sent_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function quote(): BelongsTo
    {
        return $this->belongsTo(Quote::class);
    }
}
