<?php

namespace App\Http\Controllers;

use App\Models\Endpoint;
use App\Models\Practice;
use Illuminate\Support\Carbon;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class WPCallbackController extends Controller
{

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function receiveCallback()
    {
        if (!request()->has('id')) {
            return response()->json([
                'message' => 'practice id is required'
            ], 400);
        }

        /** @var ?Practice $practice */
        $practice = Practice::find(request()->input('id'));

        if (is_null($practice)) {
            logger()->debug('practice not found', ['request' => request()->toArray()]);
            return response()->json([
                'message' => 'practice not found'
            ], 404);
        }

        /** @var ?Endpoint $endpoint */
        $endpoint = $practice->inventorySyncEndpoints()->find(request()->input('endpoint_id'));

        if (is_null($endpoint)) {
            logger()->debug('endpoint not found', ['request' => request()->toArray()]);
            return response()->json([
                'message' => 'endpoint not found'
            ], 404);
        }

        $this->handlePracticeSyncHashUpdate($practice, $endpoint, request()->input('external_id'), request()->input('wp_hash', null));

        return response()->json([
            'message' => 'request received'
        ]);
    }

    public function handlePracticeSyncHashUpdate(Practice $practice, Endpoint $endpoint, int $externalId, null|string $hash): void
    {
        $timestamp = Carbon::now();

        $endpoint->endpoint_item->setExternalId($externalId);
        $endpoint->endpoint_item->setTargetHash($hash);
        $endpoint->endpoint_item->synced_at = $timestamp;

        $endpoint->endpoint_item->save();

        if ($practice->shouldBeSearchable()) {
            $practice->searchable();
        }
    }
}
