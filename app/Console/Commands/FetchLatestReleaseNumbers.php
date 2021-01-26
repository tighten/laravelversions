<?php

namespace App\Console\Commands;

use App\Models\LaravelVersion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PHLAK\SemVer\Version;

class FetchLatestReleaseNumbers extends Command
{
    protected $signature = 'fetch-laravel-versions';

    protected $description = 'Pull the latest Laravel Version releases from GitHub into our application.';

    private $defaultFilters = [
        'first' => '100',
        'orderBy' => '{field: CREATED_AT, direction: DESC}',
    ];

    public function handle(): int
    {
        Log::info('Syncing Laravel Versions');

        $this->fetchVersionsFromGitHub()
            ->each(function ($item) {
                $semver = new Version($item['tagName']);
                $version = LaravelVersion::where([
                    'major' => $semver->major,
                    'minor' => $semver->minor,
                    'patch' => $semver->patch,
                ])->first();

                if (! $version) {
                    // Create it if it doesn't exist
                    $created = LaravelVersion::create([
                        'major' => $semver->major,
                        'minor' => $semver->minor,
                        'patch' => $semver->patch,
                        'released_at' => $item['createdAt'],
                        'changelog' => $item['descriptionHTML'],
                    ]);

                    $this->info('Created Laravel version ' . $semver);

                    return;
                }
                if (empty($version->changelog)) {
                    $version->update([
                        'released_at' => $item['createdAt'],
                        'changelog' => $item['descriptionHTML'],
                    ]);
                    $this->info('Updated Laravel version ' . $semver);
                }

                return $version;
            });

        $this->info('Finished saving Laravel versions.');
        Artisan::call('page-cache:clear');
    }

    private function fetchVersionsFromGitHub()
    {
        return cache()->remember('github::laravel-versions', 60 * 60, function () {
            $releases = collect();

            do {
                // Format the filters at runtime to include pagination
                $filters = collect($this->defaultFilters)
                    ->map(function ($value, $key) {
                        return "{$key}: {$value}";
                    })
                    ->implode(', ');

                $query = <<<QUERY
                    {
                        repository(owner: "laravel", name: "framework") {
                            releases({$filters}) {
                                nodes {
                                    name
                                    createdAt
                                    descriptionHTML
                                    tagName
                                    url
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

                $releases->push(data_get($responseJson, 'data.repository.releases.nodes'));

                $nextPage = data_get($responseJson, 'data.repository.releases.pageInfo')['endCursor'];

                if ($nextPage) {
                    $this->defaultFilters['after'] = '"' . $nextPage . '"';
                }
            } while ($nextPage);

            return $releases->flatten(1);
        });
    }
}
