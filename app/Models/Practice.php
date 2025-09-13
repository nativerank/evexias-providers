<?php

namespace App\Models;

use App\PracticeStatus;
use Database\Factories\PracticeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Practice extends Model
{
    /** @use HasFactory<PracticeFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'unique_name',
        'phone',
        'name',
        'address',
        'lat',
        'lng',
        'status',
    ];

    protected $casts = [
        'status' => PracticeStatus::class,
    ];


    public function practitioners(): BelongsToMany
    {
        return $this->belongsToMany(Practitioner::class);
    }

    public function salesReps(): BelongsToMany
    {
        return $this->belongsToMany(SalesRep::class);
    }

    public function marketingEmails(): BelongsToMany
    {
        return $this->belongsToMany(MarketingEmail::class, 'practice_marketing_email');
    }
}
