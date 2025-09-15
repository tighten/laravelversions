<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ListVersionsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_loads(): void
    {
        $response = $this->get(route('versions.index'));

        $response->assertStatus(200);
    }
}
