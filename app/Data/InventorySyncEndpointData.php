<?php

namespace App\Data;

use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use NativeRank\InventorySync\Contracts\Endpoint;

final readonly class InventorySyncEndpointData implements Endpoint
{
    private string $url;
    private const WORDPRESS_REST_API_VERSION = 'v2';

    public function __construct(
        private int|string $groupKey,
        string $url,
    ) {
        $url = rtrim($url, '/');
        $url = Str::replaceStart('http://', 'https://', $url);

        $this->url = Str::start($url, 'https://');
    }

    public function groupKey(): int|string
    {
        return $this->groupKey;
    }

   public function hashCheckUrl(): ?string
    {
        return $this->url . '/wp-json/evexias-providers/' . self::WORDPRESS_REST_API_VERSION . '/practices-hash-check?tm='. Carbon::now()->timestamp;
    }

    public function dropPreciselyUrl(): string
    {
        return $this->url . '/wp-json/evexias-providers/' . self::WORDPRESS_REST_API_VERSION . '/drop-precisely';
    }

    public function syncUrl(): string
    {
        return $this->url . '/wp-json/evexias-providers/' . self::WORDPRESS_REST_API_VERSION . '/practice';
    }

    public function dropUrl(): string
    {
        return $this->url . '/wp-json/evexias-providers/' . self::WORDPRESS_REST_API_VERSION . '/drop';
    }
}
