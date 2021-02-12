<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowVersionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_loads()
    {
        $version = LaravelVersion::factory()->create([
            'ends_securityfixes_at' => now()->addYear(),
        ]);
        $response = $this->get(route('versions.show', [$version->__toString()]));

        $response->assertStatus(200);
    }

    /** @test */
    public function it_loads_lang()
    {
        $version = LaravelVersion::factory()->create([
                'ends_securityfixes_at' => now()->addYear(),
            ]);
        $response = $this->get(route('versions.show.lang', [$version->__toString(), 'lang' => 'pl']));

        $response->assertStatus(200);
    }
}
