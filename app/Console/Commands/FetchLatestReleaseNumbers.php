<?php

namespace App\Console\Commands;

use App\Models\LaravelVersion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHLAK\SemVer\Version;

class FetchLatestReleaseNumbers extends Command
{
    protected $signature = 'fetch-laravel-versions';

    protected $description = 'Pull the latest Laravel Version releases from GitHub into our application.';

    // Not all tags have releases - so we need to pull both.
    private $gitHubQueries = [
        'refs' => [
            'filters' => [
                'first' => '100',
                'refPrefix' => '"refs/tags/"',
                'orderBy' => '{field: TAG_COMMIT_DATE, direction: DESC}',
            ],
            'query' => <<<'QUERY'
                {
                    repository(owner: "laravel", name: "framework") {
                        refs(__FILTERS__) {
                            nodes {
                                name
                                target {
                                ... on Commit {
                                        committedDate
                                    }
                                }
                            }
                            pageInfo {
                                endCursor
                                hasNextPage
                            }
                        }
                    }
                    rateLimit {
                        cost
                        remaining
                    }
                }
            QUERY,
        ],
        'releases' => [
            'filters' => [
                'first' => '100',
                'orderBy' => '{field: CREATED_AT, direction: DESC}',
            ],
            'query' => <<<'QUERY'
                {
                    repository(owner: "laravel", name: "framework") {
                        releases(__FILTERS__) {
                            nodes {
                                name
                                createdAt
                                descriptionHTML
                                tagName
                            }
                            pageInfo {
                                endCursor
                                hasNextPage
                            }
                        }
                    }
                    rateLimit {
                        cost
                        remaining
                    }
                }
            QUERY,
        ],
    ];

    public function handle(): int
    {
        Log::info('Syncing Laravel Versions');

        $this->fetchVersionsFromGitHub()
            ->each(function ($item) {
                $semver = new Version($item['name']);
                $firstReleaseSemver = $semver->major > 5 ? $semver->major . '.0.0' : $semver->major . '.' . $semver->minor . '.0';
                $firstRelease = LaravelVersion::where('first_release', $firstReleaseSemver)->first();

                $versionMeta = [
                    'changelog' => $item['changelog'],
                    'released_at' => Carbon::parse($item['released_at'])->format('Y-m-d'),
                    'ends_bugfixes_at' => $firstRelease?->ends_bugfixes_at,
                    'ends_securityfixes_at' => $firstRelease?->ends_securityfixes_at,
                ];

                $version = LaravelVersion::withoutGlobalScope('first')
                    ->firstOrCreate([
                        'major' => $semver->major,
                        'minor' => $semver->minor,
                        'patch' => $semver->patch,
                    ], $versionMeta)
                    ->fill($versionMeta);

                if ($version->isDirty()) {
                    $version->save();
                    $this->info('Updated Laravel version ' . $semver);
                }

                if ($version->wasRecentlyCreated) {
                    $this->info('Created Laravel version ' . $semver);
                }
            });

        $this->info('Finished saving Laravel versions.');
        Artisan::call('page-cache:clear');

        return 0;
    }

    private function fetchVersionsFromGitHub()
    {
        return cache()->remember('github::laravel-versions', 60 * 60, function () {
            return collect($this->gitHubQueries)->reduce(function ($carry, $query, $key) {
                do {
                    // Format the filters at runtime to include pagination
                    $filters = collect($query['filters'])
                        ->map(fn ($value, $key) => "{$key}: {$value}")
                        ->implode(', ');

                    $response = Http::withToken(config('services.github.token'))
                        ->post(
                            'https://api.github.com/graphql',
                            ['query' => str_replace('__FILTERS__', $filters, $query['query'])]
                        );

                    $responseJson = $response->json();

                    if (! $response->ok()) {
                        abort($response->getStatusCode(), 'Error connecting to GitHub: ' . $responseJson['message']);
                    }

                    $carry = $carry->merge(
                        collect(data_get($responseJson, "data.repository.{$key}.nodes"))
                            ->map(fn ($item) => [
                                'name' => $item['tagName'] ?? $item['name'],
                                'released_at' => $item['target']['committedDate'] ?? $item['createdAt'],
                                'changelog' => $item['descriptionHTML'] ?? null,
                            ])
                            ->keyBy('name')
                            ->reject(fn ($item) => str($item['name'])->contains('-') || $item['name'] === '5.3')
                    );

                    $nextPage = data_get($responseJson, "data.repository.{$key}.pageInfo")['endCursor'];

                    if ($nextPage) {
                        $query['filters']['after'] = '"' . $nextPage . '"';
                    }
                } while ($nextPage);

                return $carry;
            }, collect())
                ->push(
                    [
                        'name' => '3.2.0',
                        'changelog' => null,
                        'released_at' => '2012-05-22',
                    ],
                    [
                        'name' => '3.1.0',
                        'changelog' => null,
                        'released_at' => '2012-03-27',
                    ],
                    [
                        'name' => '3.0.0',
                        'changelog' => null,
                        'released_at' => '2012-02-22',
                    ],
                    [
                        'name' => '2.0.0',
                        'changelog' => null,
                        'released_at' => '2011-09-01',
                    ],
                    [
                        'name' => '1.0.0',
                        'changelog' => null,
                        'released_at' => '2011-06-01',
                    ]
                );
        });
    }
}
