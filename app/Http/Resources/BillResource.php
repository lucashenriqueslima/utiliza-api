<?php

namespace App\Http\Resources;

use App\Helpers\FormatHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'id', 'status', 'due_date', 'value', 'payment_vouncher_file_path'
        return [
            'call_id' => (string) $this->call_id,
            'status' => $this->status,
            'due_date' => FormatHelper::date($this->due_date),
            'value' => FormatHelper::currency($this->value),
            'payment_vouncher_file_path' => $this->payment_vouncher_file_path,
        ];
    }
}
