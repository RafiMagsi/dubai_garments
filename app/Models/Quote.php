<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Quote extends Model
{
    protected $fillable = [
        'deal_id',
        'quote_number',
        'items_json',
        'subtotal',
        'discount',
        'total_price',
        'currency',
        'status',
        'pdf_url',
        'sent_at',
        'expires_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'items_json' => 'array',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total_price' => 'decimal:2',
            'sent_at' => 'datetime',
            'expires_at' => 'date',
        ];
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }
}
