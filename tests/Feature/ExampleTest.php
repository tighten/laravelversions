<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     *
     * @test
     */
    public function basic_test(): void
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     *
     *
     * @test
     */
    public function redirection_test(): void
    {
        $response = $this->get('/');

        $response->assertStatus(301);
    }
}
