<?php

namespace App\Jobs\Auth;

use App\Models\Biker;
use App\Models\Locavibe\LocavibeRenter;
use App\Notifications\AuthenticationTokenNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAuthenticationTokenJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected Biker $renter,
        protected string $authToken
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->renter->notify(new AuthenticationTokenNotification($this->authToken));
    }
}
