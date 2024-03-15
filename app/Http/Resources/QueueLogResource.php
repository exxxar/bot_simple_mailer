<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QueueLogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'bot_id' => $this->bot_id,
            'bot_user_id' => $this->bot_user_id,
            'queue_id' => $this->queue_id,
            'sent_at' => $this->sent_at,
            'queue' => QueueResource::make($this->whenLoaded('queue')),
        ];
    }
}
