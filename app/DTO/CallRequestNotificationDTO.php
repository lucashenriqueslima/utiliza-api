<?php

namespace App\DTO;

class CallRequestNotificationDTO
{
    public function __construct(
        public CallRequestDTO $callRequestDTO,
        public string $address,
        public string $distance,
        public string $time,
        public string $price,
        public string $timeLimitToAccept,
        public string $timeoutResponse,
    ) {}

    public function toArray(): array
    {
        return [
            'path' => '/dashboard',
            'call_id' => $this->callRequestDTO->callId,
            'call_request_id' => $this->callRequestDTO->id,
            'address' => $this->address,
            'distance' => $this->distance,
            'time' => $this->time,
            'price' => $this->price,
            'time_limit_to_accept' => $this->timeLimitToAccept,
            'timeout_response' => $this->timeoutResponse,
        ];
    }
}
