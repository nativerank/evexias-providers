<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        'place_id',
        'latitude',
        'longitude',
        'formatted_address',
        'street_number',
        'route',
        'subpremise',
        'locality',
        'administrative_area_level_1',
        'country',
        'postal_code',
        'postal_code_suffix',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}
