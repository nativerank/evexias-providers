<?php

namespace App\Models;

use App\Data\InventorySyncEndpointData;
use Database\Factories\EndpointFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\Relation;
use NativeRank\InventorySync\Contracts\Endpoint as EndpointContract;
use NativeRank\InventorySync\Contracts\EndpointModel;

/**
 * App\Models\Endpoint
 *
 * @property-read Model|\Eloquent $group
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint query()
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $url
 * @property string|null $user
 * @property string|null $token
 * @property string $direction
 * @property string|null $target
 * @property string $group_type
 * @property int $group_id
 * @property EndpointItem $endpoint_item
 * @method static EndpointFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereDirection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereGroupId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereGroupType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereTarget($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereUser($value)
 * @property string $root
 * @property string $type
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereRoot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Endpoint whereType($value)
 * @mixin \Eloquent
 */
class Endpoint extends Model implements EndpointModel
{
    use HasFactory;

    protected $fillable = [
        'root',
        'type',
        'target',
    ];

    public function groupKey(): int|string
    {
        return $this->group->getKey();
    }

    public function group(): MorphTo
    {
        return $this->morphTo();
    }

    public function items(string $morphClass): MorphToMany
    {
        $modelClass = Relation::getMorphedModel($morphClass);
        $table = (new $modelClass)->getTable();

        return $this->morphedByMany($modelClass, 'item', 'endpoint_items')
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

    public function toInventorySyncEndpoint(): EndpointContract
    {
        return new InventorySyncEndpointData($this->groupKey(), $this->url());   
    }

    public function url(): string
    {
        return $this->root;
    }

    public function root(): Attribute
    {
        return Attribute::set(function (string $value) {
            $value = trim($value);
            $value = rtrim($value, '/');
            $filteredUrl = filter_var($value, FILTER_VALIDATE_URL);

            if ($filteredUrl === false) {
                return (string) filter_var($value, FILTER_VALIDATE_DOMAIN);   
            }

            return parse_url($filteredUrl, PHP_URL_HOST);
        });
    }
}
