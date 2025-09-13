<?php

namespace App\Api;

use App\Api\Requests\FetchPracticesRequestContract;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class PracticeApi
{
    public function fetch(FetchPracticesRequestContract $request): Response
    {
        return Http::withHeaders($request->headers())
            ->get($request->url(), $request->query())
            ->throw();
    }
}