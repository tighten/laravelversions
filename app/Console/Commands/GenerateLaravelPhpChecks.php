<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class GenerateLaravelPhpChecks extends Command
{
    protected $signature = 'github:workflows';

    protected $description = 'Generate the Laravel PHP checks workflows.';

    public function handle()
    {
        if (! is_dir(getcwd() . '/.github/workflows')) {
            mkdir(getcwd() . '/.github/workflows', 0777, true);
        }

        $this->getMatrix()->filter(function (array $phpVersions, string $laravelVersion) {
            return $this->generateWorkflow($laravelVersion, $phpVersions);
        })->whenNotEmpty(
            fn () => $this->output->success('Workflows (re)generated successfully.'),
            fn () => $this->output->error('Generating workflows failed.')
        );
    }

    private function getMatrix(): Collection
    {
        return collect([
            '10.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '9.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '8.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '7.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '6.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.8.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.7.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.6.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.5.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.4.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.3.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.2.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.1.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
            '5.0.*' => ['8.2', '8.1', '8.0', '7.4', '7.3', '7.2', '7.1', '7.0', '5.6', '5.5', '5.4'],
        ]);
    }

    private function generateWorkflow(string $laravelVersion, array $phpVersions): bool
    {
        $workflow = $this->getWorkflowStub();
        $workflow = str_replace('{{ LARAVEL_VERSION }}', $laravelVersion, $workflow);
        $workflow = str_replace('{{ LARAVEL_MATRIX }}', $this->generateMatrix($laravelVersion, $phpVersions), $workflow);

        $path = getcwd() . "/.github/workflows/check-laravel-{$laravelVersion}-php.yml";

        return (bool) file_put_contents($path, $workflow);
    }

    private function getWorkflowStub(): string
    {
        return
<<<'EOT'
name: {{ LARAVEL_VERSION }} Checks

on:
  workflow_dispatch:
  schedule:
    - cron: '0 0 1 * *'

jobs:
  test:
    strategy:
      fail-fast: false
      matrix:
        laravel:{{ LARAVEL_MATRIX }}

    name: Laravel ${{ matrix.laravel[0] }} | PHP ${{ matrix.laravel[1] }}

    runs-on: ubuntu-latest

    steps:
      - uses: actions/checkout@v3
      - uses: actions/cache@v3
        with:
          path: ~/.composer/cache/files
          key: laravel-${{ matrix.laravel[0] }}-php-${{ matrix.laravel[1] }}
      - uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.laravel[1] }}
      - run: |
          composer create-project --no-install --no-scripts laravel/laravel="${{ matrix.laravel[0] }}" laravel
          cd laravel
          composer config --no-interaction allow-plugins.kylekatarnls/update-helper true
          composer install
          php -r "file_exists('.env') || copy('.env.example', '.env');"
          php artisan key:generate --ansi
          vendor/bin/phpunit

EOT;
    }

    private function generateMatrix(string $laravelVersion, array $phpVersions): string
    {
        return "\n" . collect($phpVersions)->map(fn ($phpVersion) =>
            "        - [\"{$laravelVersion}\", \"{$phpVersion}\"]"
        )->implode("\n");
    }
}
