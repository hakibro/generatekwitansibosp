<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_date',
        'proof_number',
        'activity_code',
        'account_code',
        'detail_code',
        'description',
        'amount',
        'receiver_name',
        'warning',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'integer',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(ReceiptBatch::class, 'receipt_batch_id');
    }
}
