<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Support\Carbon;
use NativeRank\InventorySync\Contracts\EndpointItem as EndpointItemContract;
use NativeRank\InventorySync\Observers\EndpointItemObserver;

/**
 * App\Models\EndpointItem
 *
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem query()
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string $source_hash
 * @property string|null $target_hash
 * @property int|null $external_id
 * @property string $item_type
 * @property int $item_id
 * @property int $endpoint_id
 * @property string|null $modified_at
 * @property string|null $synced_at
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereDroppedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereEndpointId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereItemType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereModifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereSourceHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereSyncedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereTargetHash($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EndpointItem whereUpdatedAt($value)
 * @mixin \Eloquent
 */
#[ObservedBy(classes: EndpointItemObserver::class)]
class EndpointItem extends MorphPivot implements EndpointItemContract
{
    protected $table = 'endpoint_items';

    public function sourceHash(): string
    {
        return $this->source_hash;
    }

    public function targetHash(): ?string
    {
        return $this->target_hash;
    }

    public function setTargetHash(?string $hash): void
    {
        $this->target_hash = $hash;
    }

    public function setExternalId(?int $id): void
    {
        $this->external_id = $id;
    }

    public function setModifiedAt(?Carbon $modifiedAt = null): void
    {
        $this->modified_at = $modifiedAt ?? now();
    }
}
