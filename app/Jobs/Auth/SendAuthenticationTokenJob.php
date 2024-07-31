<?php

namespace App\Jobs\Auth;

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
        protected LocavibeRenter $locavibeRenter,
        protected string $authToken
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->locavibeRenter->notify(new AuthenticationTokenNotification($this->authToken));
    }
}
