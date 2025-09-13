<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use NativeRank\InventorySync\Contracts\Group;

class Tenant extends Model implements Group
{
    public function items(): HasMany
    {
        return $this->hasMany(Practice::class);
    }

    public function endpoints(): MorphMany
    {
        return $this->morphMany(Endpoint::class, 'group');
    }

    public function inventorySyncEndpoints(): MorphMany
    {
        return $this->endpoints()->whereType('inventory_sync');
    }
}
