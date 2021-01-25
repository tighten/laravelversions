<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaravelVersionFromPathTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function non_version_shaped_path_throws_404()
    {
        $response = $this->get('/fancy.1.4');

        $response->assertNotFound();
    }

    /** @test */
    public function more_than_three_segments_redirects_to_three()
    {
        $response = $this->get('/1.1.1.14');

        $response->assertRedirect('1.1.1');
    }

    /** @test */
    public function versions_after_five_dont_require_a_minor()
    {
        LaravelVersion::factory()->create([
            'major' => 7,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $response = $this->get('/7');

        $response->assertOk();
    }

    /** @test */
    public function versions_before_six_require_a_minor()
    {
        LaravelVersion::factory()->create([
            'major' => 5,
        ]);

        $response = $this->get('/5');

        $response->assertNotFound();
    }

    /** @test */
    public function it_finds_versions_after_five()
    {
        LaravelVersion::factory()->create([
            'major' => 7,
            'minor' => 9,
            'patch' => 42,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $this->get('7.9.42')->assertOk();
        $this->get('7.9')->assertOk();
        $this->get('7')->assertOk();
        $this->get('7.2.0')->assertOk();
        $this->get('7.0.0')->assertOk();
        $this->get('7.5')->assertOk();
    }

    /** @test */
    public function it_finds_versions_before_six()
    {
        LaravelVersion::factory()->create([
            'major' => 5,
            'minor' => 3,
            'patch' => 21,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $this->get('5.3.21')->assertOk();
        $this->get('5.3')->assertOk();
        $this->get('5.3.0')->assertOk();
    }

    /** @test */
    public function before_six_minor_is_required_to_match()
    {
        LaravelVersion::factory()->create([
            'major' => 5,
            'minor' => 3,
            'patch' => 21,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $this->get('5.3.21')->assertOk();
        $this->get('5.2.21')->assertNotFound();
    }

    /** @test */
    public function it_404s_when_version_dne()
    {
        $response = $this->get('/1.0.0');

        $response->assertNotFound();
    }
}
