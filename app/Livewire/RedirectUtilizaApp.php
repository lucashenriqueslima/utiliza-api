<?php

namespace App\Livewire;

use App\Enums\CallStatus;
use App\Enums\UtilizaAppPath;
use App\Models\Call;
use App\Models\CallRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Crypt;


class RedirectUtilizaApp extends Component
{
    public UtilizaAppPath $utilizaAppPath;
    public ?string $encryptedKey;
    public Call $call;

    public function mount(string $path, string $encryptedKey)
    {
        try {
            $this->utilizaAppPath = UtilizaAppPath::from($path);

            $this->encryptedKey = $encryptedKey;

            $decryptedKey = explode('|', Crypt::decrypt($this->encryptedKey));


            $this->call = Call::where('id', $decryptedKey[0])
                ->where('created_at', $decryptedKey[1])
                ->firstOrFail();
        } catch (\Exception $e) {
            abort(404);
        }
    }



    public function render()
    {

        if ($this->call->status != CallStatus::SearchingBiker) {
            $this->dispatch('show-error-alert', message: 'Chamado não está mais disponível');
        } else {
            $this->dispatch('redirect-to-utiliza-app', path: $this->utilizaAppPath->value, encryptedKey: $this->encryptedKey);
        }
        return view('livewire.redirect-utiliza-app');
    }
}
