<?php

namespace App\Models;

use Database\Factories\MarketingEmailFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketingEmail extends Model
{
    /** @use HasFactory<MarketingEmailFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
    ];
}
