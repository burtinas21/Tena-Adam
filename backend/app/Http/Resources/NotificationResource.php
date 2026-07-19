<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [

            'id' => $this->id,

            'user' => [

                'id' => $this->user?->id,

                'first_name' => $this->user?->first_name,

                'last_name' => $this->user?->last_name,

                'email' => $this->user?->email,

            ],

            'type' => $this->type,

            'channel' => $this->channel,

            'subject' => $this->subject,

            'content' => $this->content,

            'status' => $this->status,

            'error_message' => $this->error_message,

            'retry_count' => $this->retry_count,

            'sent_at' => $this->sent_at,

            'created_at' => $this->created_at,

            'updated_at' => $this->updated_at,

        ];
    }
}