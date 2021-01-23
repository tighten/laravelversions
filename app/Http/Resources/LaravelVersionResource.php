<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LaravelVersionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'major' => $this->major,
            'minor' => $this->minor,
            'patch' => $this->patch,
            'is_lts' => $this->is_lts,
            'released_at' => $this->released_at,
            'ends_bugfixes_at' => $this->ends_bugfixes_at,
            'ends_securityfixes_at' => $this->ends_securityfixes_at,
            'status' => $this->status,
            'links' => [
                [
                    'type' => 'GET',
                    'rel' => 'self',
                    'href' => $this->api_url,
                ],
            ]
        ];
    }
}
