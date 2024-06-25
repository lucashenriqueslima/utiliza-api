<?php

namespace App\Services;

use App\Enums\CallRequestStatus;
use App\Enums\CallStatus;
use App\Models\CallRequest;

class CallRequestService
{
    public static function checkIfCallRequestWasAccepted(?CallRequestStatus $status): bool
    {
        return $status === CallRequestStatus::Accepted;
    }

    public static function handleUpdateStatus(?CallRequest $callRequest): void
    {
        if ($callRequest->status == null) {
            $callRequest->update([
                'status' => CallRequestStatus::NotAnsewered
            ]);
        }
    }
}
