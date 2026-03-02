<?php

use App\Models\LaravelVersion;

test('non version shaped path throws 404', function () {
    $this->get('/en/fancy.1.4')
        ->assertNotFound();

    $this->get('/en/1.1.1.14')
        ->assertNotFound();
});

test('versions after five dont require a minor', function () {
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
});

test('versions before six require a minor', function () {
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
});

it('finds versions after five', function () {
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
});

it('finds versions before six', function () {
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
});

test('before six minor is required to match', function () {
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
});

it('404s when version dne', function () {
    $response = $this->get('/en/1.0.0');

    $response->assertNotFound();
});
