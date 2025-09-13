<?php

namespace App\Api\Requests;

interface FetchPracticesRequestContract
{
    public function url(): string;
    public function query(): array;
    public function headers(): array;   
}