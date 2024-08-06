<?php

namespace App\Models;

use App\Enums\BillStatus;
use App\Helpers\FormatHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;

class Bill extends Model
{
    use HasFactory;

    protected $guarded = [];


    protected static function booted()
    {
        static::creating(function ($bill) {
            $bill->value = CallValue::where('is_valid', true)
                ->latest()
                ->value;

            #next friday day
            $bill->due_date = Carbon::parse('next friday')->toDateString();
        });
    }

    protected function casts(): array
    {
        return [
            'value' => 'float',
            'status' => BillStatus::class,
        ];
    }

    public function call(): BelongsTo
    {
        return $this->belongsTo(Call::class);
    }

    public function getFormattedValueAttribute(): string
    {
        return FormatHelper::currency($this->value);
    }
}
