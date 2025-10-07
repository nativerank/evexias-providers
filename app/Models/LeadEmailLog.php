<?php

namespace App\Models;

use App\EmailDeliveryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;

class LeadEmailLog extends Model
{
    protected $fillable = [
        'lead_id',
        'recipient_email',
        'status',
        'message_id',
        'error_message',
        'sent_at',
    ];

    protected $casts = [
        'status' => EmailDeliveryStatus::class,
        'sent_at' => 'datetime',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function practice(): HasOneThrough
    {
        return $this->hasOneThrough(
            Practice::class,
            Lead::class,
            'id',
            'id',
            'lead_id',
            'practice_id'
        );
    }
}
