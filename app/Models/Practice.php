<?php

namespace App\Models;

use App\Observers\PracticeObserver;
use App\PracticeStatus;
use Database\Factories\PracticeFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use NativeRank\InventorySync\Contracts\Group;
use NativeRank\InventorySync\Contracts\Item;

#[ObservedBy(classes: PracticeObserver::class)]
class Practice extends Model implements Item
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

    public function syncKey(): string
    {
        return 'practice';
    }

    public function toSyncArray(): array
    {
        $array = $this->toArray();

        $array['practitioners'] = array_map(fn ($practitioner) => Arr::only($practitioner, [
            'id',
            'external_id',
            'active',
            'first_name',
            'last_name',
            'email',
            'specialization',
            'practitioner_type',
        ]), $array['practitioners']);

        return $array;
    }

    public function toHashArray(): array
    {
        $array = $this->toSyncArray();

        Arr::forget(
            $array,
            [
                'created_at',
                'updated_at',
            ]
        );

        ksort($array);

        return $array;
    }

    public function itemRelations(): array
    {
        return ['practitioners'];
    }
    
    public function changesStoreKey(): string
    {
        return 'practice_changes_' . $this->getKey();
    }

    public function group(): Group
    {
        /** @var Tenant $tenant */
        $tenant = $this->tenant()->sole();

        return $tenant;
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function inventorySyncEndpoints(): MorphToMany
    {
        return $this->endpoints()->whereType('inventory_sync');
    }

    public function endpoints(): MorphToMany
    {
        return $this->morphToMany(Endpoint::class, 'item', 'endpoint_items')
            ->using(EndpointItem::class)
            ->as('endpoint_item')
            ->withPivot(
                'source_hash',
                'target_hash',
                'external_id',
                'modified_at',
                'synced_at',
            );
    }

    public function inactive(): bool
    {
        return false;
    }

    public function trashed(): bool
    {
        return false;
    }
}
