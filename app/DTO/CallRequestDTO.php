<?php

namespace App\DTO;

use App\Enums\CallRequestStatus;

class CallRequestDTO
{
    //constructor
    public function __construct(
        public ?string $id,
        public ?string $callId,
    ) {}
}
