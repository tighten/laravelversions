<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(Tests\TestCase::class);
uses(RefreshDatabase::class);

it('loads', function () {
    $response = $this->get(route('versions.index'));

    $response->assertStatus(200);
});
