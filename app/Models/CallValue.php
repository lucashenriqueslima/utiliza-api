<?php

namespace App\Models;

use App\Helpers\FormatHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CallValue extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'value' => 'float',
        'is_valid' => 'boolean',
    ];

    public function getValidValueAttribute()
    {
        return FormatHelper::currency($this->where('is_valid', true)
            ->latest()
            ->first()
            ->value ?? '50');
    }
}
