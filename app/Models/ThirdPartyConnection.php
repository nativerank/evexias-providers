<?php

namespace App\Models;

use App\Data\Enums\ThirdPartyClass;
use App\Data\Enums\ThirdPartyProvider;
use Illuminate\Database\Eloquent\Model;

class ThirdPartyConnection extends Model
{
    protected $fillable = [
        'provider',
        'class',
        'external_id',
    ];

    protected $casts = [
        'provider' => ThirdPartyProvider::class,
        'class' => ThirdPartyClass::class,
    ];

    public function connectable()
    {
        return $this->morphTo();
    }
}
