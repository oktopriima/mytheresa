<?php

namespace App\Contract\Interfaces;

interface ServiceResponseInterface
{
    // To distribute of response's statuses
    public function status(): bool;

    // Messages of response
    public function message(): string;

    // To distribute of response's data
    public function result();

    // Set Http code for response
    public function httpCode(): int;

    // To distribute successful statuses
    public function ok(): bool;

// To distribute failed statuses
    public function fail(): bool;
}
