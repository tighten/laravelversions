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
     * @return void
     *
     * @test
     */
    public function basicTest()
    {
        $response = $this->get('/en');

        $response->assertStatus(200);
    }

    /**
     * A basic test example.
     *
     * @return void
     *
     * @test
     */
    public function redirectionTest()
    {
        $response = $this->get('/');

        $response->assertStatus(301);
    }
}
