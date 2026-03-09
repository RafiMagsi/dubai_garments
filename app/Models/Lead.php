<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'source',
        'customer_name',
        'company',
        'email',
        'phone',
        'product_slug',
        'product_type',
        'quantity',
        'required_delivery_date',
        'design_file_path',
        'message',
        'ai_score',
        'classification',
        'status',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'required_delivery_date' => 'date',
            'meta' => 'array',
        ];
    }
}
