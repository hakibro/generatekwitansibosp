<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReceiptBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year',
        'source_file',
        'start_proof',
        'number_mode',
        'merge_mode',
        'stamp_limit',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'stamp_limit' => 'integer',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(ReceiptItem::class);
    }
}
