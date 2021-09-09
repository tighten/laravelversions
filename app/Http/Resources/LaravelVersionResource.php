<?php

namespace App\Http\Resources;

use App\Models\LaravelVersion;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaravelVersionResource extends JsonResource
{
    public function toArray($request)
    {
        $minor_label = $this->major < 6 ? 'minor' : 'latest_minor';
        $segments = $request->segments();

        return [
            'major' => $this->major,
            $minor_label => $this->minor,
            'latest_patch' => $this->patch,
            'latest' => sprintf('%s.%s.%s', $this->major, $this->minor, $this->patch),
            'latest_major' => (string) LaravelVersion::released()->orderByDesc('major')->orderByDesc('minor')->orderByDesc('patch')->first(),
            'is_lts' => $this->is_lts,
            'released_at' => $this->released_at,
            'ends_bugfixes_at' => $this->ends_bugfixes_at,
            'ends_securityfixes_at' => $this->ends_securityfixes_at,
            'status' => $this->status,
            $this->mergeWhen($this->specificVersionProvided($request), [
                'specific_version' => [
                    'provided' => end($segments),
                    'needs_patch' => $request->url() !== $this->latest_patch_api_url,
                    'needs_major_upgrade' => $this->status === 'end-of-life',
                ],
            ]),
            'links' => $this->links($request),
        ];
    }

    public function specificVersionProvided(Request $request): bool
    {
        return ($this->api_url !== $request->url()) && ! $request->routeIs('api.versions.index');
    }

    public function links(Request $request): array
    {
        if ($this->specificVersionProvided($request)) {
            $base = [
                [
                    'type' => 'GET',
                    'rel' => 'major',
                    'href' => $this->api_url,
                ],
                [
                    'type' => 'GET',
                    'rel' => 'self',
                    'href' => $request->url(),
                ],
            ];
        } else {
            $base = [
                [
                    'type' => 'GET',
                    'rel' => 'self',
                    'href' => $this->api_url,
                ],
            ];
        }

        return array_merge($base, [
            [
                'type' => 'GET',
                'rel' => 'latest',
                'href' => $this->latest_patch_api_url,
            ],
        ]);
    }
}
