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
        $latest_version = LaravelVersion::withoutGlobalScope('front')->released()->orderByDesc('order')->first();

        return [
            'major' => $this->lastRelease->major,
            $minor_label => $this->lastRelease->minor,
            'latest_patch' => $this->lastRelease->patch,
            'latest' => $this->lastRelease->semver,
            'is_lts' => $this->is_lts,
            'released_at' => $this->released_at,
            'ends_bugfixes_at' => $this->ends_bugfixes_at,
            'ends_securityfixes_at' => $this->ends_securityfixes_at,
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
                'latest_version_is_lts' => $latest_version->is_lts,
            ],
        ];
    }

    public function specificVersionProvided(Request $request): bool
    {
        return ! $this->is_front && ! $request->routeIs('api.versions.index');
    }

    public function links(Request $request): array
    {
        return array_filter([
            $this->specificVersionProvided($request) ? [
                'type' => 'GET',
                'rel' => 'major',
                'href' => $this->firstRelease->api_url,
            ] : null,
            [
                'type' => 'GET',
                'rel' => 'self',
                'href' => $this->api_url,
            ],
            [
                'type' => 'GET',
                'rel' => 'latest',
                'href' => $this->lastRelease->api_url,
            ],
        ]);
    }
}
