<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ShowVersionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_loads(): void
    {
        $version = LaravelVersion::factory()->create([
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $response = $this->get($version->url);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_loads_when_bug_fixes_date_is_null(): void
    {
        $this->seedLowestSupportedVersion();

        $version = LaravelVersion::factory()->create([
            'ends_bugfixes_at' => null,
        ]);
        $response = $this->get($version->url);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_loads_when_security_fixes_date_is_null(): void
    {
        $this->seedLowestSupportedVersion();

        $version = LaravelVersion::factory()->create([
            'ends_securityfixes_at' => null,
        ]);
        $response = $this->get($version->url);

        $response->assertStatus(200);
    }

    private function seedLowestSupportedVersion()
    {
        LaravelVersion::factory()->active()->create();
    }
}
