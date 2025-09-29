<?php

namespace App\Models;

use App\Observers\PracticeObserver;
use App\PracticeStatus;
use Database\Factories\PracticeFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use NativeRank\InventorySync\Contracts\Group;
use NativeRank\InventorySync\Contracts\Item;
use Propaganistas\LaravelPhone\Casts\RawPhoneNumberCast;

/**
 * @property Location $location
 */
#[ObservedBy(classes: PracticeObserver::class)]
class Practice extends Model implements Item
{
    /** @use HasFactory<PracticeFactory> */
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'external_id',
        'unique_name',
        'phone',
        'name',
        'address',
        'lat',
        'lng',
        'status',
        'tenant_id',
        'efko_guid',
    ];

    protected $casts = [
        'status' => PracticeStatus::class,
        'phone' => RawPhoneNumberCast::class . ':US',
    ];

    protected $appends = [
        'slug',
    ];

    protected function slug(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return Str::slug(
                    implode(' ',
                        array_filter([
                            $attributes['name'],
                            Str::endsWith(Str::slug($attributes['name']), Str::slug($this->location?->locality))
                                ? null
                                : $this->location?->locality,
                            $this->location?->administrative_area_level_1,
                        ]),
                    ),
                );
            },
        );
    }

    protected function content(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return \Storage::disk($this->getContentFileDisk())->get($this->getContentFilePath());
            }
        );

    }

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

    public function thirdPartyConnections(): MorphMany
    {
        return $this->morphMany(ThirdPartyConnection::class, 'connectable');
    }

    public function location(): MorphOne
    {
        return $this->morphOne(Location::class, 'locatable');
    }

    public function syncKey(): string
    {
        return 'practice';
    }

    public function getContentFilePath(): string
    {
        return 'generated_content/content_' . $this->getKey() . '.html';
    }

    public function getContentFileDisk(): string
    {
        return 'local';
    }

    public function toSyncArray(): array
    {
        $array = $this->toArray();

        if (isset($this->content)) {
            $array['content'] = $this->content;
            $array['contentHash'] = Storage::disk($this->getContentFileDisk())->lastModified($this->getContentFilePath());
        }

        $array['phone_formatted'] = rescue(fn () => $this->phone?->formatNational());

        $array['practitioners'] = array_map(fn($practitioner) => Arr::only($practitioner, [
            'id',
            'external_id',
            'active',
            'first_name',
            'last_name',
            'email',
            'specialization',
            'practitioner_type',
        ]), $array['practitioners'] ?? []);
        
        $array['third_party_connections'] = array_map(fn($connection) => Arr::only($connection, [
            'provider',
            'external_id',
        ]), $array['third_party_connections'] ?? []);
        
        $array['location'] = Arr::only($array['location'] ?? [], [
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
        ]);


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
                'content',
                'location.metadata.reviews',
                'location.metadata.photos',
            ]
        );

        ksort($array);

        return $array;
    }

    public function itemRelations(): array
    {
        return ['practitioners', 'thirdPartyConnections', 'marketingEmails', 'location'];
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


    public function shouldBeSearchable(): bool
    {
        if (empty($this->location->latitude) || empty($this->location->longitude)) {
            return false;
        }

        return true;
    }

    public function toSearchableArray()
    {
        $array = $this->loadMissing(['thirdPartyConnections', 'location'])->toArray();
        $lat = $array['location']['latitude'] ?? null;
        $lng = $array['location']['longitude'] ?? null;

        unset($array['location']['latitude']);
        unset($array['location']['longitude']);
        unset($array['location']['created_at']);
        unset($array['location']['updated_at']);
        unset($array['location']['id']);
        unset($array['location']['locatable_id']);
        unset($array['location']['locatable_type']);
        unset($array['created_at']);
        unset($array['updated_at']);

        return [
            ...$array,
            '_geoloc' => [
                'lat' => floatval($lat),
                'lng' => floatval($lng),
            ],
            'phone_formatted' => rescue(fn () => $this->phone?->formatNational()),
            'third_party_connections' => array_map(fn($connection) => Arr::only($connection, [
                'provider',
                'external_id',
            ]), $array['third_party_connections']),
        ];
    }
}
