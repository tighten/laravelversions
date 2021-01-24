<?php

namespace App\Console\Commands;

use App\Models\LaravelVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchLatestReleaseNumbers extends Command
{
    protected $signature = 'fetch-laravel-versions';

    protected $description = 'Pull the latest Laravel Version releases from GitHub into our application.';

    private $defaultFilters = [
        'first' => '100',
        'refPrefix' => '"refs/tags/"',
        'orderBy' => '{field: TAG_COMMIT_DATE, direction: DESC}',
    ];

    public function handle()
    {
        Log::info('Syncing Laravel Versions');

        $this->fetchVersionsFromGitHub()
            // Map into arrays containing major, minor, and patch numbers
            ->map(function ($item) {
                $pieces = explode('.', ltrim($item['name'], 'v'));

                return [
                    'major' => $pieces[0],
                    'minor' => $pieces[1],
                    'patch' => $pieces[2] ?? null,
                ];
            })
            // Map into groups by release; pre-6, major/minor pair; post-6, major
            ->mapToGroups(function ($item) {
                if ($item['major'] < 6) {
                    return [$item['major'] . '.' . $item['minor'] => $item];
                }

                return [$item['major'] => $item];
            })
            // Take the highest patch or minor/patch number from each release
            ->map(function ($item) {
                if ($item->first()['major'] < 6) {
                    // Take the highest patch
                    return $item->sortByDesc('patch')->first();
                }

                // Take the highest minor, then its highest patch
                return $item->sortBy([['minor', 'desc'], ['patch', 'desc']])->first();
            })
            ->each(function ($item) {
                if ($item['major'] < 6) {
                    $version = LaravelVersion::where([
                        'major' => $item['major'],
                        'minor' => $item['minor'],
                    ])->first();

                    if ($version->patch < $item['patch']) {
                        $version->update(['patch' => $item['patch']]);
                        $this->info('Updated Laravel version ' . $version . ' to use latest patch.');
                    }

                    return;
                }

                $version = LaravelVersion::where([
                    'major' => $item['major'],
                ])->first();

                if (! $version) {
                    // Create it if it doesn't exist
                    $created = LaravelVersion::create([
                        'major' => $item['major'],
                        'minor' => $item['minor'],
                        'patch' => $item['patch'],
                    ]);

                    $this->info('Created Laravel version ' . $created);
                    return;
                }

                // Update the minor and patch if needed
                if ($version->minor != $item['minor'] || $version->patch != $item['patch']) {
                    $version->update(['minor' => $item['minor'], 'patch' => $item['patch']]);
                    $this->info('Updated Laravel version ' . $version . ' to use latest minor/patch.');
                }

                return $version;
            });

        $this->info('Finished saving Laravel versions.');
    }

    private function fetchVersionsFromGitHub()
    {
        return cache()->remember('github::laravel-versions', 60 * 60, function () {
            $tags = collect();

            do {
                // Format the filters at runtime to include pagination
                $filters = collect($this->defaultFilters)
                    ->map(function ($value, $key) {
                        return "{$key}: $value";
                    })
                    ->implode(', ');

                $query = <<<QUERY
                    {
                        repository(owner: "laravel", name: "framework") {
                            refs({$filters}) {
                                nodes {
                                    name
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
                QUERY;

                $response = Http::withToken(config('services.github.token'))
                    ->post('https://api.github.com/graphql', ['query' => $query]);

                $responseJson = $response->json();

                if (! $response->ok()) {
                    abort($response->getStatusCode(), 'Error connecting to GitHub: ' . $responseJson['message']);
                }

                $tags->push(collect(data_get($responseJson, 'data.repository.refs.nodes')));

                $nextPage = data_get($responseJson, 'data.repository.refs.pageInfo')['endCursor'];

                if ($nextPage) {
                    $this->defaultFilters['after'] = '"' . $nextPage . '"';
                }
            } while ($nextPage);

            return $tags->flatten(1);
        });
    }
}
