<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_gallery_endpoint_returns_a_successful_response(): void
    {
        $response = $this->getJson('/api/content/galeri');

        $response->assertStatus(200);
    }
}
