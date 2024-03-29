<?php

namespace App\Http\Resources;

use App\Models\LaravelVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaravelVersionResource extends JsonResource
{
    public function toArray($request): array
    {
        $minor_label = $this->major < 6 ? 'minor' : 'latest_minor';
        $latest_version = LaravelVersion::withoutGlobalScope('first')->released()->orderByDesc('order')->first();

        return [
            'major' => $this->last->major,
            $minor_label => $this->last->minor,
            'latest_patch' => $this->last->patch,
            'latest' => $this->last->semver,
            'released_at' => $this->released_at,
            'ends_bugfixes_at' => $this->ends_bugfixes_at,
            'ends_securityfixes_at' => $this->ends_securityfixes_at,
            'supported_php' => explode(', ', $this->supported_php),
            'status' => $this->status,
            $this->mergeWhen($this->specificVersionProvided($request), [
                'specific_version' => [
                    'provided' => $this->semver,
                    'needs_patch' => $this->needs_patch,
                    'needs_major_upgrade' => $this->needs_major_upgrade,
                ],
            ]),
            'links' => $this->links($request),
            'global' => [
                'latest_version' => (string) $latest_version,
            ],
        ];
    }

    public function specificVersionProvided(Request $request): bool
    {
        return ! $this->is_first && ! $request->routeIs('api.versions.index');
    }

    public function links(Request $request): array
    {
        return array_filter([
            $this->specificVersionProvided($request) ? [
                'type' => 'GET',
                'rel' => 'major',
                'href' => $this->first->api_url,
            ] : null,
            [
                'type' => 'GET',
                'rel' => 'self',
                'href' => $this->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'latest',
                'href' => $this->last->api_url,
            ],
        ]);
    }
}
