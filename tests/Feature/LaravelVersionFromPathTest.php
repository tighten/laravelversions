<?php

namespace Tests\Feature;

use App\Models\LaravelVersion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LaravelVersionFromPathTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function non_version_shaped_path_throws_404(): void
    {
        $this->get('/en/fancy.1.4')
            ->assertNotFound();

        $this->get('/en/1.1.1.14')
            ->assertNotFound();
    }

    #[Test]
    public function versions_after_five_dont_require_a_minor(): void
    {
        LaravelVersion::factory()->create([
            'major' => 6,
            'minor' => 0,
            'patch' => 0,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $this->get('/en/6')
            ->assertOk();

        $this->get('/en/6.0')
            ->assertOk();
    }

    #[Test]
    public function versions_before_six_require_a_minor(): void
    {
        LaravelVersion::factory()->create([
            'major' => 5,
            'minor' => 0,
            'patch' => 0,
            'ends_securityfixes_at' => now()->addYear(),
        ]);

        $this->get('/en/5')
            ->assertRedirect('/en/5.0');

        $this->get('/5.0')
            ->assertRedirect('/en/5.0');

        $this->get('/en/5.0')
            ->assertOk();
    }

    #[Test]
    public function it_finds_versions_after_five(): void
    {
        collect(
            [
                ['major' => 10, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 9, 'minor' => 9, 'patch' => 42],
                ['major' => 9, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 8, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 7, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 6, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
            ]
        )->each(fn ($version) => LaravelVersion::factory()->create($version));

        $this->get('/en/10')->assertOk();
        $this->get('/en/9.9.42')->assertOk();
        $this->get('/en/9')->assertOk();
        $this->get('/en/8')->assertOk();
        $this->get('/en/7')->assertOk();
        $this->get('/en/6')->assertOk();
    }

    #[Test]
    public function it_finds_versions_before_six(): void
    {
        collect(
            [
                ['major' => 6, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 5, 'minor' => 2, 'patch' => 1],
                ['major' => 5, 'minor' => 2, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 5, 'minor' => 1, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 5, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 4, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 4, 'minor' => 0, 'patch' => 1],
            ]
        )->each(fn ($version) => LaravelVersion::factory()->create($version));

        $this->get('/en/6')->assertOk();
        $this->get('/en/5.2.1')->assertOk();
        $this->get('/en/5.2')->assertOk();
        $this->get('/en/5.1')->assertOk();
        $this->get('/en/5.0')->assertOk();
        $this->get('/en/4.0.1')->assertOk();
        $this->get('/en/4.0')->assertOk();
    }

    #[Test]
    public function before_six_minor_is_required_to_match(): void
    {
        collect(
            [
                ['major' => 5, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 5, 'minor' => 1, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 4, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 3, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 2, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
                ['major' => 1, 'minor' => 0, 'patch' => 0, 'ends_securityfixes_at' => now()->addYear()],
            ]
        )->each(fn ($version) => LaravelVersion::factory()->create($version));

        $this->get('/en/5')->assertRedirect('/en/5.0');
        $this->get('/5.0')->assertRedirect('/en/5.0');
        $this->get('/en/5.0')->assertOk();
        $this->get('/en/5.1')->assertOk();
        $this->get('/en/4')->assertRedirect('/en/4.0');
        $this->get('/4.0')->assertRedirect('/en/4.0');
        $this->get('/en/4.0')->assertOk();
        $this->get('/en/3')->assertRedirect('/en/3.0');
        $this->get('/3.0')->assertRedirect('/en/3.0');
        $this->get('/en/3.0')->assertOk();
        $this->get('/en/2')->assertRedirect('/en/2.0');
        $this->get('/2.0')->assertRedirect('/en/2.0');
        $this->get('/en/2.0')->assertOk();
        $this->get('/en/1')->assertRedirect('/en/1.0');
        $this->get('/1.0')->assertRedirect('/en/1.0');
        $this->get('/en/1.0')->assertOk();
    }

    #[Test]
    public function it_404s_when_version_dne(): void
    {
        $response = $this->get('/en/1.0.0');

        $response->assertNotFound();
    }
}
