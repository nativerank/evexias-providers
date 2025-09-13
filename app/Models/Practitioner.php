<?php

namespace App\Models;

use App\PractitionerType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Practitioner extends Model
{
    /** @use HasFactory<\Database\Factories\PractitionerFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'first_name',
        'last_name',
        'email',
        'practitioner_type',
        'active',
    ];

    protected $casts = [
        'practitioner_type' => PractitionerType::class,
    ];
    
    public function practices(): BelongsToMany
    {
        return $this->belongsToMany(Practice::class);
    }
}
