<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


it('loads', function () {
    $response = $this->get(route('versions.index'));

    $response->assertStatus(200);
});
